document.querySelectorAll('[data-password-toggle]').forEach((button) => {
	const password = document.getElementById(button.dataset.passwordToggle);

	button.addEventListener('click', () => {
		const isVisible = password.type === 'text';

		password.type = isVisible ? 'password' : 'text';
		button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
	});
});
