@extends('layouts.app')

@section('title', 'My Artworks - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-64px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Your PixelVault</p>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">My artworks</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage the artworks you have added to the gallery.</p>
                </div>
                <a href="{{ route('artworks.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Add artwork</a>
            </div>

            @if (session('status'))
                <p role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">{{ session('status') }}</p>
            @endif

            @if ($artworks->isEmpty())
                <div class="rounded-xl border border-slate-200 bg-white px-5 py-12 text-center">
                    <h2 class="text-sm font-semibold text-slate-900">You have not added any artworks yet</h2>
                    <p class="mt-2 text-xs text-slate-500">Add your first artwork to see it in the gallery.</p>
                    <a href="{{ route('artworks.create') }}" class="mt-5 inline-flex h-9 items-center rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700">Add your artwork</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    @foreach ($artworks as $artwork)
                        @include('components.artwork-card', ['artwork' => $artwork])
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $artworks->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
