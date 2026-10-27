@extends('layouts.app')

@section('title', 'My Account - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="mb-7">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Your PixelVault</p>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">My account</h1>
                <p class="mt-1 text-sm text-slate-500">Welcome back, {{ auth()->user()->name }}. Your art collection starts here.</p>
            </div>

            @if (session('status'))
                <p role="status" class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">{{ session('status') }}</p>
            @endif
            <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="grid content-start gap-5">
                    <section aria-labelledby="library-heading" class="rounded-xl border border-slate-200 bg-white p-5 sm:p-7">
                        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-start">
                            <div>
                                <h2 id="library-heading" class="text-base font-semibold text-slate-900">Your library</h2>
                                <p class="mt-1 text-xs text-slate-500">Your completed demo purchases are collected here.</p>
                            </div>
                            <span class="w-fit rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-medium text-slate-500">{{ $purchasedItems->total() }} {{ $purchasedItems->total() === 1 ? 'item' : 'items' }}</span>
                        </div>

                        @if ($purchasedItems->isEmpty())
                            <div class="mt-7 flex flex-col items-center rounded-lg border border-dashed border-slate-200 bg-slate-50/70 px-5 py-10 text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600" aria-hidden="true">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                        <rect x="3.5" y="4.5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.6"/>
                                        <circle cx="9" cy="9.5" r="1.5" stroke="currentColor" stroke-width="1.6"/>
                                        <path d="m4.5 16 5-4 3.5 3 2.5-2 4 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <h3 class="mt-4 text-sm font-semibold text-slate-900">Your library is waiting</h3>
                                <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">After a simulated purchase, the artwork will appear here. No payment is collected, and downloads will be enabled when real artwork files are added.</p>
                                <a href="{{ route('home') }}#collection" class="mt-5 inline-flex h-9 items-center gap-2 rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Browse the gallery
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        @else
                            <div class="mt-5 divide-y divide-slate-100">
                                @foreach ($purchasedItems as $item)
                                    <div class="flex items-center justify-between gap-3 py-3">
                                        <div>
                                            <p class="text-xs font-semibold text-slate-800">{{ $item->title }}</p>
                                            <p class="mt-1 text-[10px] text-slate-400">Download access will be available when artwork files are added.</p>
                                        </div>
                                        <span class="shrink-0 text-xs font-medium text-slate-700">PHP {{ number_format($item->unit_amount / 100, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            {{ $purchasedItems->links() }}
                        @endif
                    </section>

                    <section aria-labelledby="orders-heading" class="rounded-xl border border-slate-200 bg-white p-5 sm:p-7">
                        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                            <div>
                                <h2 id="orders-heading" class="text-base font-semibold text-slate-900">Order history</h2>
                                <p class="mt-1 text-xs text-slate-500">Check the status and details of your 10 most recent orders.</p>
                            </div>
                        </div>
                        @if ($orders->isEmpty())
                            <div class="mt-5 rounded-lg bg-slate-50 px-4 py-5 text-center">
                                <p class="text-xs font-medium text-slate-700">No orders yet</p>
                                <p class="mt-1 text-[11px] text-slate-500">Your completed purchases will appear here.</p>
                            </div>
                        @else
                            <div class="mt-5 divide-y divide-slate-100">
                                @foreach ($orders as $order)
                                    <a href="{{ route('orders.show', $order) }}" class="flex flex-col justify-between gap-2 py-3 transition hover:bg-slate-50 sm:flex-row sm:items-center">
                                        <span>
                                            <span class="block text-xs font-semibold text-slate-800">Order #{{ $order->id }}</span>
                                            <span class="mt-1 block text-[10px] text-slate-400">{{ $order->created_at->format('M j, Y') }}</span>
                                        </span>
                                        <span class="flex items-center justify-between gap-4 sm:justify-end">
                                            <span class="text-[10px] font-medium capitalize text-slate-500">{{ str_replace('_', ' ', $order->status) }}</span>
                                            <span class="text-xs font-semibold text-slate-800">PHP {{ number_format($order->total_amount / 100, 2) }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </div>

                <aside class="grid content-start gap-5">
                    <section aria-labelledby="account-details-heading" class="rounded-xl border border-slate-200 bg-white p-5">
                        <h2 id="account-details-heading" class="text-sm font-semibold text-slate-900">Account details</h2>
                        <dl class="mt-4 grid gap-4">
                            <div>
                                <dt class="text-[10px] font-medium uppercase tracking-wide text-slate-400">Name</dt>
                                <dd class="mt-1 text-xs font-medium text-slate-700">{{ auth()->user()->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-medium uppercase tracking-wide text-slate-400">Email address</dt>
                                <dd class="mt-1 break-all text-xs font-medium text-slate-700">{{ auth()->user()->email }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-5">
                        <h2 class="text-sm font-semibold text-slate-900">Made for collecting</h2>
                        <p class="mt-2 text-xs leading-5 text-slate-600">Discover digital artists, try a simulated purchase, and keep your demo orders together in one place. No real money is charged.</p>
                        <a href="{{ route('home') }}#collection" class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-indigo-700 transition hover:text-indigo-900">
                            Explore PixelVault <span aria-hidden="true">→</span>
                        </a>
                    </section>
                </aside>
            </div>
        </div>
    </section>
@endsection
