@extends('layouts.app')

@section('title', 'Admin Dashboard - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">PixelVault Admin</p>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Simulated sales</h1>
                <p class="mt-1 text-sm text-slate-500">A record of demo orders created by users.</p>
            </div>

            <p role="note" class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-900">
                Demo figures only. These totals are not money received, payable, or held by PixelVault. No payment processor or bank is connected.
            </p>

            <div class="mb-5 grid gap-4 sm:grid-cols-2">
                <article class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-[10px] font-medium uppercase tracking-wide text-slate-400">Theoretical sales total</p>
                    <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">PHP {{ number_format($simulatedSalesTotal / 100, 2) }}</p>
                    <p class="mt-1 text-[10px] text-slate-500">Sum of simulated order totals</p>
                </article>
                <article class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-[10px] font-medium uppercase tracking-wide text-slate-400">Simulated orders</p>
                    <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ number_format($simulatedOrderCount) }}</p>
                    <p class="mt-1 text-[10px] text-slate-500">No actual transactions</p>
                </article>
            </div>

            <section aria-labelledby="simulated-orders-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 id="simulated-orders-heading" class="text-sm font-semibold text-slate-900">Recent simulated orders</h2>
                </div>
                @if ($orders->isEmpty())
                    <p class="px-5 py-10 text-center text-xs text-slate-500">No simulated orders yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[820px] text-left text-xs">
                            <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 font-medium">Order</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Buyer</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Artist</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Artwork</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Date</th>
                                    <th scope="col" class="px-5 py-3 text-right font-medium">Demo total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-800">#{{ $order->id }}</td>
                                        <td class="px-5 py-4">
                                            <span class="block font-medium text-slate-700">{{ $order->user->name }}</span>
                                            <span class="mt-1 block text-[10px] text-slate-400">{{ $order->user->email }}</span>
                                        </td>
                                        <td class="px-5 py-4">{{ $order->artist?->display_name ?? 'Multiple artists' }}</td>
                                        <td class="px-5 py-4">
                                            @foreach ($order->items as $item)
                                                <span class="block">{{ $item->title }}</span>
                                            @endforeach
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">{{ ($order->completed_at ?? $order->created_at)->format('M j, Y') }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-800">PHP {{ number_format($order->total_amount / 100, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-100 px-5 py-3">
                        {{ $orders->links() }}
                    </div>
                @endif
            </section>
        </div>
    </section>
@endsection
