(() => {
	'use strict';

	const config = window.marketPulseOkx;
	const cards = new Map(
		Array.from(document.querySelectorAll('[data-okx-market]')).map((card) => [
			card.dataset.okxMarket,
			card,
		])
	);

	if (!config?.restUrl || cards.size === 0) {
		return;
	}

	const svgNamespace = 'http://www.w3.org/2000/svg';
	const priceFormatter = new Intl.NumberFormat('zh-CN', {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	});
	let timer = null;
	let controller = null;
	let requestInProgress = false;
	let destroyed = false;
	let socket = null;
	let reconnectTimer = null;
	let heartbeatTimer = null;
	let reconnectAttempts = 0;
	let lastSocketMessage = 0;
	const marketState = new Map();

	const isFiniteNumber = (value) => typeof value === 'number' && Number.isFinite(value);

	const setStatus = (card, message, isError = false) => {
		const status = card.querySelector('[data-okx-status]');
		const content = card.querySelector('[data-okx-content]');
		if (status) {
			status.textContent = message;
			status.classList.toggle('is-error', isError);
			status.hidden = false;
		}
		if (content) {
			content.hidden = true;
		}
	};

	const formatSigned = (value, suffix) => {
		if (!isFiniteNumber(value)) return '—';
		const sign = value > 0 ? '+' : value < 0 ? '-' : '';
		return `${sign}${priceFormatter.format(Math.abs(value))}${suffix}`;
	};

	const formatVolume = (value) => {
		if (!isFiniteNumber(value)) return '—';
		const units = [
			{ threshold: 1e9, suffix: 'B' },
			{ threshold: 1e6, suffix: 'M' },
			{ threshold: 1e3, suffix: 'K' },
		];
		const unit = units.find((item) => Math.abs(value) >= item.threshold);
		return unit
			? `${new Intl.NumberFormat('zh-CN', { maximumFractionDigits: 2 }).format(value / unit.threshold)}${unit.suffix}`
			: new Intl.NumberFormat('zh-CN', { maximumFractionDigits: 2 }).format(value);
	};

	const setStreamState = (state, label) => {
		cards.forEach((card) => {
			const badge = card.querySelector('[data-okx-live-state]');
			const text = card.querySelector('[data-okx-live-label]');
			if (badge) badge.dataset.okxLiveState = state;
			if (text) text.textContent = label;
		});
	};

	const drawTrend = (container, points, status, symbol) => {
		container.replaceChildren();
		const validPoints = Array.isArray(points)
			? points.filter((point) => isFiniteNumber(point?.close) && isFiniteNumber(point?.timestamp))
			: [];

		if (validPoints.length < 2) {
			const message = document.createElement('p');
			message.className = 'okx-market__trend-empty';
			message.textContent = config.trendError;
			container.appendChild(message);
			return;
		}

		const width = 600;
		const height = 100;
		const padding = 5;
		const prices = validPoints.map((point) => point.close);
		const min = Math.min(...prices);
		const max = Math.max(...prices);
		const range = max - min;
		const usableWidth = width - padding * 2;
		const usableHeight = height - padding * 2;
		const coordinates = prices.map((price, index) => {
			const x = padding + (index / (prices.length - 1)) * usableWidth;
			const y = range === 0
				? height / 2
				: padding + ((max - price) / range) * usableHeight;
			return `${x.toFixed(2)},${y.toFixed(2)}`;
		});

		const svg = document.createElementNS(svgNamespace, 'svg');
		const definitions = document.createElementNS(svgNamespace, 'defs');
		const gradient = document.createElementNS(svgNamespace, 'linearGradient');
		const start = document.createElementNS(svgNamespace, 'stop');
		const end = document.createElementNS(svgNamespace, 'stop');
		const area = document.createElementNS(svgNamespace, 'polygon');
		const polyline = document.createElementNS(svgNamespace, 'polyline');
		const color = status === 'up' ? '#22c55e' : status === 'down' ? '#ef4444' : '#9aa8ba';
		const gradientId = `okx-trend-${symbol.toLowerCase()}`;
		svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
		svg.setAttribute('role', 'img');
		svg.setAttribute('aria-label', `${symbol} 最近24小时价格趋势`);
		svg.setAttribute('preserveAspectRatio', 'none');
		gradient.setAttribute('id', gradientId);
		gradient.setAttribute('x1', '0');
		gradient.setAttribute('y1', '0');
		gradient.setAttribute('x2', '0');
		gradient.setAttribute('y2', '1');
		start.setAttribute('offset', '0%');
		start.setAttribute('stop-color', color);
		start.setAttribute('stop-opacity', '0.28');
		end.setAttribute('offset', '100%');
		end.setAttribute('stop-color', color);
		end.setAttribute('stop-opacity', '0');
		gradient.append(start, end);
		definitions.appendChild(gradient);
		area.setAttribute('points', `${coordinates.join(' ')} ${width - padding},${height} ${padding},${height}`);
		area.setAttribute('fill', `url(#${gradientId})`);
		polyline.setAttribute('points', coordinates.join(' '));
		polyline.setAttribute('fill', 'none');
		polyline.setAttribute('stroke', color);
		polyline.setAttribute('stroke-width', '3');
		polyline.setAttribute('stroke-linecap', 'round');
		polyline.setAttribute('stroke-linejoin', 'round');
		polyline.setAttribute('vector-effect', 'non-scaling-stroke');
		svg.append(definitions, area, polyline);
		container.appendChild(svg);
	};

	const isValidMarket = (market) => market
		&& isFiniteNumber(market.last)
		&& isFiniteNumber(market.open_24h)
		&& isFiniteNumber(market.high_24h)
		&& isFiniteNumber(market.low_24h)
		&& isFiniteNumber(market.volume_24h)
		&& isFiniteNumber(market.timestamp)
		&& ['up', 'down', 'flat'].includes(market.change_status);

	const renderCard = (card, market, stale) => {
		if (!isValidMarket(market)) {
			setStatus(card, config.errorMessage, true);
			return;
		}

		const status = card.querySelector('[data-okx-status]');
		const content = card.querySelector('[data-okx-content]');
		const change = card.querySelector('[data-okx-change]');
		card.querySelector('[data-okx-price]').textContent = `${priceFormatter.format(market.last)} ${config.currency}`;
		card.querySelector('[data-okx-change-amount]').textContent = formatSigned(market.change, ` ${config.currency}`);
		card.querySelector('[data-okx-change-percent]').textContent = isFiniteNumber(market.change_percent)
			? formatSigned(market.change_percent, '%')
			: '—';
		card.querySelector('[data-okx-high]').textContent = `${priceFormatter.format(market.high_24h)} ${config.currency}`;
		card.querySelector('[data-okx-low]').textContent = `${priceFormatter.format(market.low_24h)} ${config.currency}`;
		card.querySelector('[data-okx-volume]').textContent = `${formatVolume(market.volume_24h)} ${market.symbol}`;
		card.querySelector('[data-okx-updated]').textContent = `更新时间：${market.updated_at_display || '—'}`;
		change.classList.remove('is-up', 'is-down', 'is-flat');
		change.classList.add(`is-${market.change_status}`);
		drawTrend(card.querySelector('[data-okx-trend]'), market.trend, market.change_status, market.symbol);

		const staleMessage = card.querySelector('[data-okx-stale]');
		staleMessage.textContent = stale ? config.staleMessage : '';
		staleMessage.hidden = !stale;
		status.hidden = true;
		content.hidden = false;
	};

	const updateLiveTicker = (ticker) => {
		if (!ticker || typeof ticker.instId !== 'string') return;
		const entry = Array.from(marketState.entries()).find(([, market]) => market.inst_id === ticker.instId);
		if (!entry) return;

		const [key, market] = entry;
		const last = Number(ticker.last);
		const open = Number(ticker.open24h);
		const high = Number(ticker.high24h);
		const low = Number(ticker.low24h);
		const volume = Number(ticker.vol24h);
		const timestamp = Number(ticker.ts);
		if (![last, open, high, low, volume, timestamp].every(Number.isFinite)) return;

		const change = last - open;
		const changePercent = open > 0 ? (change / open) * 100 : null;
		const status = change > 0 ? 'up' : change < 0 ? 'down' : 'flat';
		const updatedMarket = {
			...market,
			last,
			open_24h: open,
			high_24h: high,
			low_24h: low,
			volume_24h: volume,
			timestamp,
			change,
			change_percent: Number.isFinite(changePercent) ? changePercent : null,
			change_status: status,
			stale: false,
		};
		const trend = Array.isArray(updatedMarket.trend) ? [...updatedMarket.trend] : [];
		if (trend.length > 0) {
			trend[trend.length - 1] = {
				...trend[trend.length - 1],
				close: last,
				timestamp,
				confirmed: false,
			};
			updatedMarket.trend = trend;
		}
		marketState.set(key, updatedMarket);
		renderCard(cards.get(key), updatedMarket, false);
	};

	const clearSocketTimers = () => {
		if (reconnectTimer !== null) {
			window.clearTimeout(reconnectTimer);
			reconnectTimer = null;
		}
		if (heartbeatTimer !== null) {
			window.clearInterval(heartbeatTimer);
			heartbeatTimer = null;
		}
	};

	const closeSocket = () => {
		clearSocketTimers();
		if (socket) {
			socket.onclose = null;
			socket.close();
			socket = null;
		}
	};

	const scheduleReconnect = () => {
		if (destroyed || document.hidden || reconnectTimer !== null) return;
		const delay = Math.min(30000, 1000 * (2 ** reconnectAttempts));
		reconnectAttempts += 1;
		reconnectTimer = window.setTimeout(() => {
			reconnectTimer = null;
			connectSocket();
		}, delay);
	};

	const connectSocket = () => {
		if (!config.webSocketUrl || !('WebSocket' in window) || destroyed || document.hidden || socket || marketState.size === 0) {
			return;
		}

		setStreamState('connecting', '连接中');
		socket = new WebSocket(config.webSocketUrl);
		socket.addEventListener('open', () => {
			reconnectAttempts = 0;
			lastSocketMessage = Date.now();
			const args = Array.from(marketState.values()).map((market) => ({
				channel: 'tickers',
				instId: market.inst_id,
			}));
			socket?.send(JSON.stringify({ op: 'subscribe', args }));
			setStreamState('live', '实时');
			heartbeatTimer = window.setInterval(() => {
				if (socket?.readyState === WebSocket.OPEN && Date.now() - lastSocketMessage >= 20000) {
					socket.send('ping');
				}
			}, 20000);
		});
		socket.addEventListener('message', (event) => {
			lastSocketMessage = Date.now();
			if (event.data === 'pong') return;
			try {
				const payload = JSON.parse(event.data);
				if (payload?.arg?.channel !== 'tickers' || !Array.isArray(payload.data)) return;
				payload.data.forEach(updateLiveTicker);
			} catch (error) {
				// Ignore malformed upstream frames and keep the REST fallback active.
			}
		});
		socket.addEventListener('error', () => {
			socket?.close();
		});
		socket.addEventListener('close', () => {
			socket = null;
			if (heartbeatTimer !== null) {
				window.clearInterval(heartbeatTimer);
				heartbeatTimer = null;
			}
			setStreamState('delayed', '延迟');
			scheduleReconnect();
		});
	};

	const loadMarkets = async () => {
		if (requestInProgress || destroyed || document.hidden) return;
		requestInProgress = true;
		controller = new AbortController();
		const timeout = window.setTimeout(() => controller?.abort(), Number(config.requestTimeout) || 10000);

		try {
			const url = new URL(config.restUrl, window.location.origin);
			url.searchParams.set('symbols', Array.from(cards.keys()).join(','));
			const response = await fetch(url.toString(), {
				headers: { Accept: 'application/json' },
				signal: controller.signal,
				credentials: 'same-origin',
			});
			if (!response.ok) throw new Error('Market request failed');
			const payload = await response.json();
			if (payload?.success !== true || !payload.data) throw new Error('Invalid market response');

			cards.forEach((card, key) => {
				if (payload.data[key]) {
					marketState.set(key, payload.data[key]);
					renderCard(card, payload.data[key], payload.data[key].stale === true);
				} else {
					setStatus(card, config.errorMessage, true);
				}
			});
			connectSocket();
		} catch (error) {
			cards.forEach((card) => {
				if (card.querySelector('[data-okx-content]')?.hidden !== false) {
					setStatus(card, config.errorMessage, true);
				}
			});
		} finally {
			window.clearTimeout(timeout);
			controller = null;
			requestInProgress = false;
		}
	};

	const stopTimer = () => {
		if (timer !== null) {
			window.clearInterval(timer);
			timer = null;
		}
	};

	const startTimer = () => {
		stopTimer();
		if (!document.hidden && !destroyed) {
			timer = window.setInterval(loadMarkets, Math.max(30000, Number(config.refreshInterval) || 30000));
		}
	};

	const handleVisibility = () => {
		if (document.hidden) {
			stopTimer();
			controller?.abort();
			closeSocket();
			setStreamState('delayed', '已暂停');
		} else {
			loadMarkets();
			startTimer();
			connectSocket();
		}
	};

	const destroy = () => {
		destroyed = true;
		stopTimer();
		controller?.abort();
		closeSocket();
		document.removeEventListener('visibilitychange', handleVisibility);
	};

	document.addEventListener('visibilitychange', handleVisibility);
	window.addEventListener('pagehide', destroy, { once: true });
	loadMarkets();
	startTimer();
})();
