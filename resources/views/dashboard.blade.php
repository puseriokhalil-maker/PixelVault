@extends('layouts.app')

@section('title', 'Dashboard - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-5 sm:px-6 sm:py-7">
        <div class="mx-auto max-w-[1180px]">
            <div class="mb-6">
                <div class="mb-1 text-[10px] font-medium text-indigo-600">Catalog / Artworks</div>
                <h1 class="text-[22px] font-bold leading-tight text-slate-900">Artwork Management</h1>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="flex items-start justify-between">
                        <p class="pt-1 text-[10px] text-slate-500">Total Sales</p>
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-slate-100 text-slate-500" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 7.5h13a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a3 3 0 0 1-3-3V6a2 2 0 0 1 2-2h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16 12h4v4h-4a2 2 0 0 1 0-4Z" stroke="currentColor" stroke-width="1.8"/></svg>
                        </span>
                    </div>
                    <p class="mt-1 text-[28px] font-bold leading-none text-slate-900">$48,240</p>
                    <p class="mt-2 text-[9px] text-slate-400"><span class="rounded bg-emerald-100 px-1.5 py-0.5 font-semibold text-emerald-700">+12.5%</span><span class="ml-1.5">vs last month</span></p>
                </article>

                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="flex items-start justify-between">
                        <p class="pt-1 text-[10px] text-slate-500">Active Listings</p>
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-slate-100 text-slate-500" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><circle cx="9" cy="9" r="1.5" stroke="currentColor" stroke-width="1.6"/><path d="m5 17 5-5 3 3 2-2 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </div>
                    <p class="mt-1 text-[28px] font-bold leading-none text-slate-900">128</p>
                    <p class="mt-2 text-[9px] text-slate-400"><span class="rounded bg-emerald-100 px-1.5 py-0.5 font-semibold text-emerald-700">+8 this month</span></p>
                </article>

                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <div class="flex items-start justify-between">
                        <p class="pt-1 text-[10px] text-slate-500">Pending Review</p>
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-amber-100 text-amber-600" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                    </div>
                    <p class="mt-1 text-[28px] font-bold leading-none text-amber-600">06</p>
                    <p class="mt-2 text-[9px] font-medium text-amber-600">Needs attention</p>
                </article>
            </div>

            <section aria-labelledby="artworks-heading">
                <div class="mb-3 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                    <div>
                        <h2 id="artworks-heading" class="text-[14px] font-bold text-slate-900">All artworks</h2>
                        <p class="text-[10px] text-slate-400">Manage your digital artwork inventory</p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <label class="flex h-9 items-center gap-2 rounded-md border border-slate-200 bg-white px-3 text-slate-400 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <input type="search" data-artwork-search placeholder="Search artworks" aria-label="Search artworks" class="min-w-0 bg-transparent text-xs text-slate-700 outline-none placeholder:text-slate-400">
                        </label>
                        <select data-artwork-category aria-label="Filter by category" class="h-9 rounded-md border border-slate-200 bg-white px-3 text-xs text-slate-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            <option value="">All categories</option>
                            <option>2D</option>
                            <option>3D</option>
                            <option>Vector</option>
                            <option>Pixel Art</option>
                        </select>
                        <select data-artwork-status aria-label="Filter by status" class="h-9 rounded-md border border-slate-200 bg-white px-3 text-xs text-slate-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            <option value="">All statuses</option>
                            <option>Active</option>
                            <option>In review</option>
                        </select>
                        <button type="button" data-open-artwork-dialog class="inline-flex h-9 items-center justify-center gap-2 rounded-md bg-indigo-600 px-3 text-xs font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Add Artwork
                        </button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px] border-collapse text-left">
                            <thead class="bg-slate-50 text-[9px] font-semibold uppercase text-slate-500">
                                <tr>
                                    <th scope="col" class="w-[8%] px-4 py-3">#</th>
                                    <th scope="col" class="w-[10%] px-3 py-3">Thumbnail</th>
                                    <th scope="col" class="w-[27%] px-3 py-3">Title</th>
                                    <th scope="col" class="w-[15%] px-3 py-3">Category</th>
                                    <th scope="col" class="w-[12%] px-3 py-3">Price</th>
                                    <th scope="col" class="px-3 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody data-artwork-rows class="divide-y divide-slate-200 text-[11px] text-slate-600"></tbody>
                        </table>
                    </div>
                    <div class="flex flex-col gap-2 border-t border-slate-200 bg-slate-50 px-4 py-2.5 text-[10px] text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                        <p data-artwork-count aria-live="polite"></p>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <button type="button" data-page-previous aria-label="Previous page" class="flex h-7 w-7 items-center justify-center rounded border border-slate-200 bg-white text-slate-500 disabled:cursor-not-allowed disabled:opacity-40">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <span data-page-indicator class="min-w-12 text-center"></span>
                            <button type="button" data-page-next aria-label="Next page" class="flex h-7 w-7 items-center justify-center rounded border border-slate-200 bg-white text-slate-500 disabled:cursor-not-allowed disabled:opacity-40">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <dialog data-artwork-dialog class="m-auto w-[min(440px,calc(100%-2rem))] rounded-xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/40">
        <form data-artwork-form class="p-5 sm:p-6">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Add artwork</h2>
                    <p class="mt-1 text-xs text-slate-500">Add a piece to your inventory.</p>
                </div>
                <button type="button" data-close-artwork-dialog aria-label="Close dialog" class="flex h-8 w-8 items-center justify-center rounded text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <div class="grid gap-4">
                <label class="grid gap-1.5 text-xs font-medium text-slate-700">Title
                    <input name="title" required maxlength="80" class="h-10 rounded-md border border-slate-200 px-3 font-normal outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                </label>
                <label class="grid gap-1.5 text-xs font-medium text-slate-700">Artist
                    <input name="artist" required maxlength="60" class="h-10 rounded-md border border-slate-200 px-3 font-normal outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="grid gap-1.5 text-xs font-medium text-slate-700">Category
                        <select name="category" required class="h-10 rounded-md border border-slate-200 bg-white px-3 font-normal outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            <option value="2D">2D</option>
                            <option value="3D">3D</option>
                            <option value="Vector">Vector</option>
                            <option value="Pixel Art">Pixel Art</option>
                        </select>
                    </label>
                    <label class="grid gap-1.5 text-xs font-medium text-slate-700">Price (USD)
                        <input name="price" type="number" min="0.01" max="999999" step="0.01" required class="h-10 rounded-md border border-slate-200 px-3 font-normal outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                    </label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-close-artwork-dialog class="h-9 rounded-md border border-slate-200 px-3 text-xs font-medium text-slate-600 transition hover:bg-slate-50">Cancel</button>
                <button type="submit" class="h-9 rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700">Add artwork</button>
            </div>
        </form>
    </dialog>

    <script type="application/json" data-initial-artworks>@json($artworks)</script>
@endsection