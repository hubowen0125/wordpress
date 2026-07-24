(() => {
	'use strict';

	const header = document.querySelector('.site-header');
	if (!header) {
		return;
	}

	const menuButton = header.querySelector('.menu-toggle');
	const navigation = header.querySelector('#primary-navigation');
	const searchButton = header.querySelector('.search-toggle');
	const searchPanel = header.querySelector('#header-search');

	const closeMenu = () => {
		if (!menuButton || !navigation) return;
		menuButton.setAttribute('aria-expanded', 'false');
		navigation.classList.remove('is-open');
	};

	const closeSearch = () => {
		if (!searchButton || !searchPanel) return;
		searchButton.setAttribute('aria-expanded', 'false');
		searchPanel.hidden = true;
	};

	menuButton?.addEventListener('click', () => {
		const expanded = menuButton.getAttribute('aria-expanded') === 'true';
		menuButton.setAttribute('aria-expanded', String(!expanded));
		navigation?.classList.toggle('is-open', !expanded);
	});

	searchButton?.addEventListener('click', () => {
		const expanded = searchButton.getAttribute('aria-expanded') === 'true';
		searchButton.setAttribute('aria-expanded', String(!expanded));
		if (searchPanel) {
			searchPanel.hidden = expanded;
			if (!expanded) searchPanel.querySelector('input')?.focus();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeMenu();
			closeSearch();
			menuButton?.focus();
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth >= 768) closeMenu();
	});
})();

