@extends('layouts.app')

@section('title', 'Owned NFTs - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Your PixelVault</p>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Owned NFTs</h1>
                <p class="mt-1 text-sm text-slate-500">View the artworks you own and choose whether to relist or keep each one private.</p>
            </div>

            @if (session('status'))
                <p role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">{{ session('status') }}</p>
            @endif

            <nav aria-label="User dashboard" class="mb-5 overflow-x-auto rounded-xl border border-slate-200 bg-white p-2">
                <div class="flex min-w-max items-center gap-1">
                    <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Overview</a>
                    <a href="{{ route('dashboard') }}#account-details-heading" class="rounded-md px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">My Profile</a>
                    <a href="{{ route('dashboard') }}#library-heading" class="rounded-md px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">My Purchases</a>
                    <a href="{{ route('owned-nfts.index') }}" aria-current="page" class="rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white">Owned NFTs</a>
                    <a href="{{ route('artworks.mine') }}" class="rounded-md px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">My Listings</a>
                    <a href="{{ route('dashboard') }}#orders-heading" class="rounded-md px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Transaction History</a>
                    <a href="{{ route('dashboard') }}#account-details-heading" class="rounded-md px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Settings</a>
                </div>
            </nav>

            @if ($artworks->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-200 bg-white px-5 py-12 text-center">
                    <h2 class="text-sm font-semibold text-slate-900">You do not own any NFTs yet</h2>
                    <p class="mt-2 text-xs text-slate-500">Purchased artworks will appear here and leave the public gallery.</p>
                    <a href="{{ route('home') }}#collection" class="mt-5 inline-flex h-9 items-center rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700">Browse the gallery</a>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($artworks as $artwork)
                        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                            <div class="flex h-52 items-center justify-center bg-slate-50">
                                @if ($artwork->image_path)
                                    <img src="{{ $artwork->image_url }}" alt="{{ $artwork->title }}" class="h-full w-full object-contain">
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h2 class="text-sm font-semibold text-slate-900">{{ $artwork->title }}</h2>
                                        <p class="mt-1 text-[11px] text-slate-500">by {{ $artwork->artist->display_name }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full {{ $artwork->is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} px-2 py-1 text-[10px] font-medium">
                                        {{ $artwork->is_published ? 'Listed' : 'Not listed' }}
                                    </span>
                                </div>

                                <dl class="mt-4 grid grid-cols-2 gap-3 border-y border-slate-100 py-4 text-[11px]">
                                    <div>
                                        <dt class="text-slate-400">Category</dt>
                                        <dd class="mt-1 font-medium text-slate-700">{{ $artwork->category }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-400">Price</dt>
                                        <dd class="mt-1 font-medium text-slate-700">PHP {{ number_format($artwork->price_amount / 100, 2) }}</dd>
                                    </div>
                                </dl>

                                <p class="mt-4 min-h-16 text-xs leading-5 text-slate-600">{{ $artwork->description }}</p>

                                @if ($artwork->is_published)
                                    <form method="POST" action="{{ route('owned-nfts.unlist', $artwork) }}" class="mt-5">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 w-full items-center justify-center rounded-md border border-slate-300 px-4 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Unlist NFT</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('owned-nfts.resell', $artwork) }}" class="mt-5 grid grid-cols-[1fr_auto] gap-2">
                                        @csrf
                                        <label class="sr-only" for="resell-price-{{ $artwork->id }}">Resale price</label>
                                        <input id="resell-price-{{ $artwork->id }}" name="price" type="number" min="0.01" step="0.01" value="{{ number_format($artwork->price_amount / 100, 2, '.', '') }}" required class="h-9 min-w-0 rounded-md border border-slate-300 px-3 text-xs text-slate-700 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        <button type="submit" class="h-9 rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700">Resell</button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">{{ $artworks->links() }}</div>
            @endif
        </div>
    </section>
@endsection
