@extends('layouts.app')

@section('title', $artwork->title.' - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-6xl">
            <a href="{{ route('home') }}#collection" class="mb-5 inline-flex items-center gap-2 text-xs font-medium text-slate-500 transition hover:text-indigo-600">
                <span aria-hidden="true">←</span>
                Back to gallery
            </a>

            <div class="grid overflow-hidden rounded-xl border border-slate-200 bg-white lg:grid-cols-[minmax(0,1.25fr)_minmax(300px,0.75fr)]">
                <div class="flex min-h-[320px] items-center justify-center bg-[#f7f9fc] p-8 sm:min-h-[480px]">
                    @if ($artwork->image_path)
                        <img src="{{ $artwork->image_url }}" alt="{{ $artwork->title }}" class="max-h-[480px] w-full object-contain">
                    @endif
                </div>

                <div class="flex flex-col p-6 sm:p-8 lg:p-10">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Artwork details</p>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $artwork->title }}</h1>
                    <p class="mt-2 text-sm text-slate-500">by {{ $artwork->artist->display_name }}</p>

                    <div class="mt-6 border-y border-slate-100 py-5">
                        <p class="text-sm font-semibold text-slate-900">PHP {{ number_format($artwork->price_amount / 100, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $artwork->category }}</p>
                    </div>

                    <section class="mt-6" aria-labelledby="description-heading">
                        <h2 id="description-heading" class="text-xs font-semibold text-slate-900">About this artwork</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $artwork->description }}</p>
                    </section>

                    @can('update', $artwork)
                        <div class="mt-6 flex items-center gap-4">
                            <a href="{{ route('artworks.edit', $artwork) }}" class="inline-flex h-9 items-center rounded-md border border-indigo-200 px-4 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50">Edit artwork</a>
                            <form method="POST" action="{{ auth()->user()->is_admin ? route('admin.artworks.destroy', $artwork) : route('artworks.destroy', $artwork) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 transition hover:underline">Delete artwork</button>
                            </form>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('cart.items.store', $artwork) }}" class="mt-auto pt-8">
                        @csrf
                        <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Add to cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
