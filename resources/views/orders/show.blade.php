@extends('layouts.app')

@section('title', 'Order #'.$order->id.' - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('dashboard') }}" class="mb-5 inline-flex items-center gap-2 text-xs font-medium text-slate-500 transition hover:text-indigo-600">
                <span aria-hidden="true">←</span>
                Back to your account
            </a>

            <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-8">
                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Order #{{ $order->id }}</p>
                <h1 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Order status</h1>

                @if ($order->status === 'simulated')
                    <p class="mt-4 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-3 text-xs leading-5 text-indigo-800">Simulated purchase complete. This is a demo record only: no real payment was taken and no money was transferred.</p>
                @else
                    <p class="mt-4 rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-700">Historical order status: {{ str_replace('_', ' ', ucfirst($order->status)) }}.</p>
                @endif

                <div class="mt-6 divide-y divide-slate-100 border-y border-slate-100">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-4 py-3 text-xs">
                            <span class="font-medium text-slate-700">{{ $item->title }}</span>
                            <span class="shrink-0 text-slate-600">PHP {{ number_format($item->unit_amount / 100, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-between text-sm font-semibold text-slate-900">
                    <span>{{ $order->status === 'simulated' ? 'Simulated total' : 'Order total' }}</span>
                    <span>PHP {{ number_format($order->total_amount / 100, 2) }}</span>
                </div>
                @if ($order->items->contains(fn ($item): bool => $item->artwork_id === null))
                    <p class="mt-4 text-[11px] leading-5 text-slate-500">Some items are no longer in the catalog. The original purchase details remain saved in this order.</p>
                @endif

                <p class="mt-5 text-[10px] leading-4 text-slate-400">Sample listings only. Downloadable artwork files are not available yet.</p>
            </div>
        </div>
    </section>
@endsection
