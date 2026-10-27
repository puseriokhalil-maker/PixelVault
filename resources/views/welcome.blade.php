@extends('layouts.app')

@section('title', 'PixelVault - Discover Digital Artwork')

@section('content')
    <section class="relative min-h-[266px] overflow-hidden bg-[#0e172b] px-6 py-9 text-white sm:px-8">
        <div
            class="pointer-events-none absolute inset-0 opacity-80"
            style="background-image: linear-gradient(to right, #1c2b49 1px, transparent 1px), linear-gradient(to bottom, #1c2b49 1px, transparent 1px); background-size: 26px 26px;"
            aria-hidden="true"
        ></div>
        <div class="relative mx-auto max-w-6xl">
            <div class="mb-2 inline-flex items-center rounded-full border border-indigo-400/30 bg-indigo-950/40 px-2.5 py-1 text-[8px] text-indigo-100">
                <span>✧ &nbsp; CURATED FOR DIGITAL CREATORS</span>
            </div>
            <h1 class="mb-2 max-w-[440px] text-[30px] font-semibold leading-[1.15] tracking-tight sm:text-[32px]">
                Discover digital artwork that moves you.
            </h1>
            <p class="mb-5 max-w-[420px] text-[11px] leading-[1.35] text-slate-300">
                Explore limited digital editions from independent artists around here. Find your next favorite piece, ready to collect.
            </p>
            <a href="#collection" class="inline-flex h-7 items-center gap-1.5 rounded bg-indigo-600 px-4 text-[10px] font-medium text-white transition hover:bg-indigo-500">
                Explore the gallery <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>

    <section id="collection" class="mx-auto w-full max-w-6xl px-6 py-8 sm:px-8">
        <div class="mb-1 text-[9px] font-medium tracking-wide text-indigo-600">THE COLLECTION</div>
        <div class="mb-5 flex items-center justify-between gap-3">
            <h2 class="text-[18px] font-semibold tracking-tight text-slate-900">Featured artworks</h2>
            <span class="text-[9px] text-neutral-400">{{ count($artworks) }} {{ count($artworks) === 1 ? 'artwork' : 'artworks' }}</span>
        </div>

        <form method="GET" action="{{ route('home') }}" class="mb-4 flex gap-2 sm:hidden">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search artworks or artists" aria-label="Search artworks or artists" class="h-9 min-w-0 flex-1 rounded-md border border-slate-200 px-3 text-xs text-slate-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            @if (request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <button type="submit" class="h-9 rounded-md bg-indigo-600 px-3 text-xs font-semibold text-white">Search</button>
        </form>

        <p class="mb-4 text-[10px] text-amber-700">Sample listings and test prices only; no real artwork is being sold yet.</p>

        <div class="mb-6 flex items-center gap-2.5 overflow-x-auto pb-0.5" aria-label="Filter by category">
            <a href="{{ route('home', array_filter(['q' => request('q')])) }}" @class(['shrink-0 rounded-full px-3.5 py-1.5 text-[10px] font-medium', 'bg-indigo-600 text-white' => ! request('category'), 'border border-slate-200 text-slate-600 transition hover:bg-slate-50' => request('category')]) aria-current="{{ request('category') ? 'false' : 'true' }}">All</a>
            @foreach ($categories as $category)
                <a href="{{ route('home', array_filter(['q' => request('q'), 'category' => $category])) }}" @class(['shrink-0 rounded-full px-3 py-1.5 text-[10px] transition', 'bg-indigo-600 font-medium text-white' => request('category') === $category, 'border border-slate-200 text-slate-600 hover:bg-slate-50' => request('category') !== $category]) aria-current="{{ request('category') === $category ? 'true' : 'false' }}">{{ $category }}</a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 gap-x-3.5 gap-y-2 sm:grid-cols-2 md:grid-cols-3">
            @foreach ($artworks as $artwork)
                @include('components.artwork-card', ['artwork' => $artwork])
            @endforeach
        </div>

        @if ($artworks->isEmpty())
            <p class="rounded-lg border border-dashed border-slate-200 px-5 py-10 text-center text-sm text-slate-500">No artworks match your search. Try another name or category.</p>
        @endif
    </section>
@endsection