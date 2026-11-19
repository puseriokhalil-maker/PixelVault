document.querySelectorAll('[data-password-toggle]').forEach((button) => {
	const password = document.getElementById(button.dataset.passwordToggle);

	button.addEventListener('click', () => {
		const isVisible = password.type === 'text';

		password.type = isVisible ? 'password' : 'text';
		button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
	});
});

document.querySelectorAll('[data-profile-menu]').forEach((menu) => {
	const trigger = menu.querySelector('[data-profile-trigger]');
	const dropdown = menu.querySelector('[data-profile-dropdown]');
	let openTimer;
	let closeTimer;

	const openMenu = () => {
		window.clearTimeout(closeTimer);
		dropdown.classList.remove('invisible', 'opacity-0', 'translate-y-1');
		trigger.setAttribute('aria-expanded', 'true');
	};

	const closeMenu = () => {
		dropdown.classList.add('invisible', 'opacity-0', 'translate-y-1');
		trigger.setAttribute('aria-expanded', 'false');
	};

	const scheduleOpen = () => {
		window.clearTimeout(closeTimer);
		openTimer = window.setTimeout(openMenu, 1000);
	};

	const scheduleClose = () => {
		window.clearTimeout(openTimer);
		closeTimer = window.setTimeout(closeMenu, 150);
	};

	menu.addEventListener('mouseenter', scheduleOpen);
	menu.addEventListener('mouseleave', scheduleClose);
	trigger.addEventListener('focus', scheduleOpen);
	trigger.addEventListener('blur', scheduleClose);
	trigger.addEventListener('click', () => {
		const isOpen = trigger.getAttribute('aria-expanded') === 'true';
		window.clearTimeout(openTimer);

		if (isOpen) {
			closeMenu();
		} else {
			openMenu();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeMenu();
			trigger.focus();
		}
	});
});

document.querySelectorAll('[data-social-dock]').forEach((dock) => {
	const toggle = dock.querySelector('[data-social-toggle]');
	const panel = dock.querySelector('[data-social-panel]');

	const setPanelOpen = (isOpen) => {
		panel.classList.toggle('hidden', !isOpen);
		toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		toggle.setAttribute('aria-label', isOpen ? 'Close friends panel' : 'Open friends panel');
	};

	toggle.addEventListener('click', () => {
		setPanelOpen(toggle.getAttribute('aria-expanded') !== 'true');
	});

	dock.querySelectorAll('[data-dock-friend]').forEach((friendRow) => {
		friendRow.addEventListener('contextmenu', (event) => {
			event.preventDefault();
			const contextMenu = friendRow.querySelector('[data-dock-context]');
			contextMenu.classList.toggle('hidden');
		});
	});

	document.querySelectorAll('[data-user-card]').forEach((userCard) => {
		userCard.addEventListener('contextmenu', (event) => {
			event.preventDefault();
			const contextMenu = userCard.querySelector('[data-user-context]');
			contextMenu.classList.toggle('hidden');
		});
	});

	document.addEventListener('click', (event) => {
		if (!dock.contains(event.target)) {
			setPanelOpen(false);
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			setPanelOpen(false);
		}
	});
});
