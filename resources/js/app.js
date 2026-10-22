document.querySelectorAll('[data-password-toggle]').forEach((button) => {
	const password = document.getElementById(button.dataset.passwordToggle);

	button.addEventListener('click', () => {
		const isVisible = password.type === 'text';

		password.type = isVisible ? 'password' : 'text';
		button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
	});
});

const initialArtworks = document.querySelector('[data-initial-artworks]');

if (initialArtworks) {
	const storageKey = 'pixelvault.dashboard.artworks.v1';
	const pageSize = 6;
	const themes = ['neon', 'drift', 'geometry', 'bloom', 'city', 'machines'];
	const rows = document.querySelector('[data-artwork-rows]');
	const count = document.querySelector('[data-artwork-count]');
	const pageIndicator = document.querySelector('[data-page-indicator]');
	const search = document.querySelector('[data-artwork-search]');
	const categoryFilter = document.querySelector('[data-artwork-category]');
	const statusFilter = document.querySelector('[data-artwork-status]');
	const dialog = document.querySelector('[data-artwork-dialog]');
	const form = document.querySelector('[data-artwork-form]');
	let artworks = JSON.parse(localStorage.getItem(storageKey) || initialArtworks.textContent);
	let currentPage = 1;

	const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;',
	})[character]);

	const render = () => {
		const query = search.value.trim().toLowerCase();
		const filtered = artworks.filter((artwork) => {
			const searchable = `${artwork.id} ${artwork.title} ${artwork.artist} ${artwork.category}`.toLowerCase();

			return searchable.includes(query)
				&& (!categoryFilter.value || artwork.category === categoryFilter.value)
				&& (!statusFilter.value || artwork.status === statusFilter.value);
		});
		const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
		currentPage = Math.min(currentPage, totalPages);
		const start = (currentPage - 1) * pageSize;
		const visible = filtered.slice(start, start + pageSize);

		rows.innerHTML = visible.map((artwork) => {
			const safeTheme = themes.includes(artwork.theme) ? artwork.theme : themes[0];
			const statusClass = artwork.status === 'In review'
				? 'bg-amber-100 text-amber-700'
				: 'bg-emerald-100 text-emerald-700';
			const price = Number(artwork.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

			return `<tr class="h-[62px]">
				<td class="px-4 text-slate-400">${escapeHtml(artwork.id)}</td>
				<td class="px-3"><div class="artwork-thumb artwork-thumb--${safeTheme}" role="img" aria-label="Artwork thumbnail for ${escapeHtml(artwork.title)}"></div></td>
				<td class="px-3"><div class="font-semibold text-slate-800">${escapeHtml(artwork.title)}</div><div class="mt-0.5 text-[10px] text-slate-400">by ${escapeHtml(artwork.artist)}</div></td>
				<td class="px-3">${escapeHtml(artwork.category)}</td>
				<td class="px-3 font-semibold text-slate-800">$${price}</td>
				<td class="px-3"><span class="inline-flex min-w-[108px] items-center rounded-full px-2.5 py-1 text-[10px] font-medium ${statusClass}">${escapeHtml(artwork.status)}</span></td>
			</tr>`;
		}).join('');

		if (visible.length === 0) {
			rows.innerHTML = '<tr><td colspan="6" class="px-4 py-10 text-center text-xs text-slate-400">No artworks match these filters.</td></tr>';
		}

		count.textContent = filtered.length
			? `Showing ${start + 1}-${Math.min(start + pageSize, filtered.length)} of ${filtered.length} artworks`
			: 'Showing 0 artworks';
		pageIndicator.textContent = `${currentPage} / ${totalPages}`;
		document.querySelector('[data-page-previous]').disabled = currentPage === 1;
		document.querySelector('[data-page-next]').disabled = currentPage === totalPages;
	};

	search.addEventListener('input', () => {
		currentPage = 1;
		render();
	});
	[categoryFilter, statusFilter].forEach((filter) => filter.addEventListener('change', () => {
		currentPage = 1;
		render();
	}));
	document.querySelector('[data-page-previous]').addEventListener('click', () => {
		currentPage -= 1;
		render();
	});
	document.querySelector('[data-page-next]').addEventListener('click', () => {
		currentPage += 1;
		render();
	});
	document.querySelector('[data-open-artwork-dialog]').addEventListener('click', () => dialog.showModal());
	document.querySelectorAll('[data-close-artwork-dialog]').forEach((button) => {
		button.addEventListener('click', () => dialog.close());
	});
	dialog.addEventListener('click', (event) => {
		if (event.target === dialog) {
			dialog.close();
		}
	});
	form.addEventListener('submit', (event) => {
		event.preventDefault();
		const formData = new FormData(form);
		const highestId = artworks.reduce((highest, artwork) => {
			const number = Number(artwork.id.replace('PV-', ''));

			return Number.isFinite(number) ? Math.max(highest, number) : highest;
		}, 1048);
		const newArtwork = {
			id: `PV-${highestId + 1}`,
			title: formData.get('title').trim(),
			artist: formData.get('artist').trim(),
			category: formData.get('category'),
			price: Number(formData.get('price')),
			status: 'In review',
			theme: themes[artworks.length % themes.length],
		};

		artworks = [newArtwork, ...artworks];
		localStorage.setItem(storageKey, JSON.stringify(artworks));
		form.reset();
		dialog.close();
		search.value = '';
		categoryFilter.value = '';
		statusFilter.value = '';
		currentPage = 1;
		render();
	});

	render();
}
