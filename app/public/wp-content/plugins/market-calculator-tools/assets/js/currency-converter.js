(() => {
	'use strict';

	const settings = window.mctCurrencyConverter;
	const root = document.querySelector('[data-mct-currency-converter]');

	if (!settings?.endpoint || !root) {
		return;
	}

	const form = root.querySelector('[data-mct-form]');
	const amountInput = root.querySelector('[data-mct-amount]');
	const fromSelect = root.querySelector('[data-mct-from]');
	const toSelect = root.querySelector('[data-mct-to]');
	const submitButton = root.querySelector('[data-mct-submit]');
	const swapButton = root.querySelector('[data-mct-swap]');
	const resetButton = root.querySelector('[data-mct-reset]');
	const status = root.querySelector('[data-mct-status]');
	const error = root.querySelector('[data-mct-error]');
	const resultValue = root.querySelector('[data-mct-result-value]');
	const rateValue = root.querySelector('[data-mct-rate]');
	const reverseValue = root.querySelector('[data-mct-reverse]');
	const sourceValue = root.querySelector('[data-mct-source]');
	const updatedValue = root.querySelector('[data-mct-updated]');

	let rates = null;

	const formatAmount = (value) => new Intl.NumberFormat('en-US', {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	}).format(value);

	const formatInputAmount = (value) => new Intl.NumberFormat('en-US', {
		minimumFractionDigits: 0,
		maximumFractionDigits: 2,
	}).format(value);

	const formatRate = (value) => {
		if (value === 1) {
			return '1';
		}

		return new Intl.NumberFormat('en-US', {
			minimumFractionDigits: 0,
			maximumFractionDigits: 6,
		}).format(value);
	};

	const clearResult = () => {
		resultValue.textContent = '—';
		rateValue.textContent = '—';
		reverseValue.textContent = '—';
	};

	const calculate = () => {
		if (!rates) {
			return;
		}

		const rawAmount = amountInput.value.trim();
		const amount = Number(rawAmount);
		const from = fromSelect.value;
		const to = toSelect.value;
		const fromRate = Number(rates[from]);
		const toRate = Number(rates[to]);

		if (
			rawAmount === '' ||
			!Number.isFinite(amount) ||
			amount < 0 ||
			!Number.isFinite(fromRate) ||
			!Number.isFinite(toRate) ||
			fromRate <= 0 ||
			toRate <= 0
		) {
			error.textContent = settings.strings.invalidAmount;
			clearResult();
			return;
		}

		const currentRate = toRate / fromRate;
		const reverseRate = 1 / currentRate;
		const converted = amount * currentRate;

		if (![currentRate, reverseRate, converted].every(Number.isFinite)) {
			error.textContent = settings.strings.invalidAmount;
			clearResult();
			return;
		}

		error.textContent = '';

		if (from === to) {
			resultValue.textContent = `${formatInputAmount(amount)} ${from} = ${formatInputAmount(amount)} ${to}`;
			rateValue.textContent = '1';
			reverseValue.textContent = '1';
		} else {
			resultValue.textContent = `${formatInputAmount(amount)} ${from} = ${formatAmount(converted)} ${to}`;
			rateValue.textContent = `1 ${from} = ${formatRate(currentRate)} ${to}`;
			reverseValue.textContent = `1 ${to} = ${formatRate(reverseRate)} ${from}`;
		}
	};

	const loadRates = async () => {
		status.textContent = settings.strings.loading;
		submitButton.disabled = true;

		try {
			const response = await fetch(settings.endpoint, {
				credentials: 'same-origin',
				headers: { Accept: 'application/json' },
			});
			const payload = await response.json();
			const receivedRates = payload?.data?.rates;

			if (!response.ok || payload?.success !== true || !receivedRates || typeof receivedRates !== 'object') {
				throw new Error('Invalid rate response');
			}

			rates = receivedRates;
			status.textContent = '';
			submitButton.disabled = false;
			sourceValue.textContent = `${settings.strings.sourceLabel}${payload.source}`;
			updatedValue.textContent = `${settings.strings.updatedLabel}${payload.updated_at}`;
			calculate();
		} catch (requestError) {
			rates = null;
			status.textContent = settings.strings.loadError;
			submitButton.disabled = true;
			clearResult();
		}
	};

	form.addEventListener('submit', (event) => {
		event.preventDefault();
		calculate();
	});

	swapButton.addEventListener('click', () => {
		const previousFrom = fromSelect.value;
		fromSelect.value = toSelect.value;
		toSelect.value = previousFrom;
		calculate();
	});

	resetButton.addEventListener('click', () => {
		window.setTimeout(() => {
			amountInput.value = String(settings.defaults.amount);
			fromSelect.value = settings.defaults.from;
			toSelect.value = settings.defaults.to;
			error.textContent = '';
			calculate();
		}, 0);
	});

	amountInput.addEventListener('input', () => {
		if (error.textContent) {
			error.textContent = '';
		}
	});

	fromSelect.addEventListener('change', calculate);
	toSelect.addEventListener('change', calculate);

	loadRates();
})();
