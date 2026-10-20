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
            <span class="text-[9px] text-neutral-400">9 Original Pieces</span>
        </div>

        <div class="mb-6 flex items-center gap-2.5 overflow-x-auto pb-0.5">
            <button type="button" class="shrink-0 rounded-full bg-indigo-600 px-3.5 py-1.5 text-[10px] font-medium text-white">All</button>
            <button type="button" class="shrink-0 rounded-full border border-slate-200 px-3 py-1.5 text-[10px] text-slate-600 transition hover:bg-slate-50">2D</button>
            <button type="button" class="shrink-0 rounded-full border border-slate-200 px-3 py-1.5 text-[10px] text-slate-600 transition hover:bg-slate-50">3D</button>
            <button type="button" class="shrink-0 rounded-full border border-slate-200 px-3 py-1.5 text-[10px] text-slate-600 transition hover:bg-slate-50">VECTOR</button>
            <button type="button" class="shrink-0 rounded-full border border-slate-200 px-3 py-1.5 text-[10px] text-slate-600 transition hover:bg-slate-50">Pixel Art</button>
        </div>

        <div class="grid grid-cols-1 gap-x-3.5 gap-y-2 sm:grid-cols-2 md:grid-cols-3">
            @for ($i = 0; $i < 9; $i++)
                @include('components.artwork-card')
            @endfor
        </div>
    </section>
@endsection