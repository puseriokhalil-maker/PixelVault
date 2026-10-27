@extends('layouts.app')

@section('title', 'Your Cart - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-5xl">
            <div class="mb-6">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">PixelVault</p>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Your cart</h1>
                <p class="mt-1 text-sm text-slate-500">Review your sample listings before completing a simulated purchase.</p>
            </div>

            @if (session('status'))
                <p role="status" class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">{{ session('status') }}</p>
            @endif
            @if ($errors->has('cart'))
                <p role="alert" class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">{{ $errors->first('cart') }}</p>
            @endif

            @if ($items->isEmpty())
                <div class="rounded-xl border border-slate-200 bg-white px-5 py-12 text-center">
                    <h2 class="text-sm font-semibold text-slate-900">Your cart is empty</h2>
                    <p class="mt-2 text-xs text-slate-500">Browse the sample collection and add a piece to test the cart.</p>
                    <a href="{{ route('home') }}#collection" class="mt-5 inline-flex h-9 items-center rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700">Browse artworks</a>
                </div>
            @else
                <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_300px]">
                    <section aria-label="Cart items" class="divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white">
                        @foreach ($items as $artwork)
                            <article class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:p-5">
                                <div class="flex h-24 w-full items-center justify-center rounded-md bg-[#e09145] sm:h-20 sm:w-28 sm:shrink-0" role="img" aria-label="Artwork preview placeholder">
                                    <span class="text-xs font-semibold text-black">PLACEHOLDER</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 class="truncate text-sm font-semibold text-slate-900">{{ $artwork->title }}</h2>
                                    <p class="mt-1 text-xs text-slate-500">by {{ $artwork->artist->display_name }} · {{ $artwork->category }}</p>
                                    <span class="mt-2 inline-block rounded bg-amber-50 px-2 py-1 text-[9px] font-medium text-amber-800">TEST LISTING</span>
                                </div>
                                <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                    <p class="text-sm font-semibold text-slate-900">PHP {{ number_format($artwork->price_amount / 100, 2) }}</p>
                                    <form method="POST" action="{{ route('cart.items.destroy', $artwork) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-slate-500 underline-offset-2 hover:text-red-600 hover:underline">Remove</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    <aside class="h-fit rounded-xl border border-slate-200 bg-white p-5">
                        <h2 class="text-sm font-semibold text-slate-900">Order summary</h2>
                        <dl class="mt-4 grid gap-3 text-xs">
                            <div class="flex justify-between gap-3 text-slate-500">
                                <dt>Subtotal</dt>
                                <dd>PHP {{ number_format($subtotal / 100, 2) }}</dd>
                            </div>
                            <div class="flex justify-between gap-3 border-t border-slate-100 pt-3 font-semibold text-slate-900">
                                <dt>Total</dt>
                                <dd>PHP {{ number_format($subtotal / 100, 2) }}</dd>
                            </div>
                        </dl>
                        <form method="POST" action="{{ route('checkout.store') }}" class="mt-5">
                            @csrf
                            <button type="submit" class="h-10 w-full rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Complete simulated purchase</button>
                        </form>
                        <p class="mt-3 text-center text-[10px] leading-4 text-slate-400">Demonstration only: no payment details are requested and no money is collected or transferred.</p>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
