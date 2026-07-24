(() => {
	'use strict';

	const config = window.marketPulseMarkets;
	if (!config?.symbols) {
		return;
	}

	const iframeBase = 'https://s.tradingview.com/embed-widget/';
	const chartLibrary = 'https://s3.tradingview.com/tv.js';
	let libraryPromise;
	let currentSymbol = 'BTC';
	let chartInstance = null;

	const widgetUrl = (widget, options) =>
		`${iframeBase}${widget}/?locale=zh_CN#${encodeURIComponent(JSON.stringify(options))}`;

	const mountIframe = (container, widget, options, title) => {
		if (!container) return;
		const iframe = document.createElement('iframe');
		iframe.title = title;
		iframe.src = widgetUrl(widget, options);
		iframe.loading = 'lazy';
		iframe.referrerPolicy = 'origin';
		iframe.allowFullscreen = true;
		iframe.tabIndex = -1;
		iframe.setAttribute('frameborder', '0');
		iframe.addEventListener('load', () => {
			container.classList.add('is-loaded');
			container.querySelector('.widget-status')?.remove();
		}, { once: true });
		container.appendChild(iframe);

		window.setTimeout(() => {
			if (!container.classList.contains('is-loaded')) {
				const status = container.querySelector('.widget-status');
				if (status) status.textContent = config.errorText;
			}
		}, 15000);
	};

	const tickerSymbols = Object.values(config.symbols).map((market) => ({
		proName: market.symbol,
		title: `${market.name} / ${market.code}`,
	}));

	mountIframe(
		document.querySelector('#market-ticker-tape'),
		'ticker-tape',
		{
			symbols: tickerSymbols,
			showSymbolLogo: true,
			isTransparent: true,
			displayMode: 'adaptive',
			colorTheme: 'dark',
		},
		'BTC、ETH、黄金和白银行情滚动条'
	);

	document.querySelectorAll('[data-market-card]').forEach((container) => {
		const market = config.symbols[container.dataset.marketCard];
		if (!market) return;
		mountIframe(
			container,
			'mini-symbol-overview',
			{
				symbol: market.symbol,
				width: '100%',
				height: '100%',
				locale: 'zh_CN',
				dateRange: '1D',
				colorTheme: 'dark',
				isTransparent: true,
				autosize: true,
				largeChartUrl: '',
				noTimeScale: false,
				chartOnly: false,
			},
			`${market.name} ${market.code} 实时价格与趋势图`
		);
	});

	const loadChartLibrary = () => {
		if (window.TradingView) return Promise.resolve(window.TradingView);
		if (libraryPromise) return libraryPromise;
		libraryPromise = new Promise((resolve, reject) => {
			const script = document.createElement('script');
			script.src = chartLibrary;
			script.async = true;
			script.onload = () => resolve(window.TradingView);
			script.onerror = reject;
			document.head.appendChild(script);
		});
		return libraryPromise;
	};

	const setChartStatus = (text) => {
		const shell = document.querySelector('.advanced-chart-shell');
		if (!shell) return;
		let status = shell.querySelector('.advanced-chart-status');
		if (!status) {
			status = document.createElement('p');
			status.className = 'advanced-chart-status widget-status';
			shell.appendChild(status);
		}
		status.textContent = text;
		status.hidden = false;
	};

	const renderChart = async (key) => {
		const container = document.querySelector('#market-advanced-chart');
		const market = config.symbols[key];
		if (!container || !market) return;
		currentSymbol = key;
		setChartStatus(config.loadingText);
		container.replaceChildren();

		try {
			const TradingView = await loadChartLibrary();
			if (!TradingView?.widget || currentSymbol !== key) return;
			chartInstance = new TradingView.widget({
				autosize: true,
				symbol: market.symbol,
				interval: '60',
				timezone: 'Etc/UTC',
				theme: 'dark',
				style: '1',
				locale: 'zh_CN',
				enable_publishing: false,
				allow_symbol_change: true,
				withdateranges: true,
				hide_side_toolbar: false,
				details: true,
				hotlist: false,
				calendar: false,
				studies: ['STD;MACD', 'STD;RSI'],
				container_id: 'market-advanced-chart',
			});
			window.setTimeout(() => {
				const status = document.querySelector('.advanced-chart-status');
				if (status && currentSymbol === key) status.hidden = true;
			}, 2500);
		} catch (error) {
			chartInstance = null;
			setChartStatus(config.errorText);
		}
	};

	const activateSymbol = (key, scroll = false) => {
		if (!config.symbols[key]) return;
		document.querySelectorAll('[data-market-symbol]').forEach((button) => {
			const active = button.dataset.marketSymbol === key;
			button.classList.toggle('is-active', active);
			button.setAttribute('aria-pressed', String(active));
		});
		renderChart(key);
		if (scroll) {
			document.querySelector('.chart-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	};

	document.querySelectorAll('[data-market-symbol]').forEach((button) => {
		button.addEventListener('click', () => activateSymbol(button.dataset.marketSymbol));
	});
	document.querySelectorAll('[data-chart-target]').forEach((button) => {
		button.addEventListener('click', () => activateSymbol(button.dataset.chartTarget, true));
	});

	renderChart(currentSymbol);
})();
