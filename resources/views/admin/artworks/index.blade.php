@extends('layouts.app')

@section('title', 'Manage Artworks - PixelVault Admin')

@section('content')
    <section class="min-h-[calc(100vh-64px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">PixelVault Admin</p>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Manage artworks</h1>
                    <p class="mt-1 text-sm text-slate-500">Edit or remove any artwork in the gallery.</p>
                </div>
                <a href="{{ route('admin.artworks.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-indigo-600 px-4 text-xs font-semibold text-white transition hover:bg-indigo-700">Add artwork</a>
            </div>

            @if (session('status'))
                <p role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">{{ session('status') }}</p>
            @endif

            @if ($artworks->isEmpty())
                <div class="rounded-xl border border-slate-200 bg-white px-5 py-12 text-center">
                    <h2 class="text-sm font-semibold text-slate-900">No artworks have been added yet</h2>
                    <p class="mt-2 text-xs text-slate-500">When users add artworks, they will appear here for management.</p>
                </div>
            @else
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[700px] text-left text-xs">
                            <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 font-medium">Artwork</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Artist</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Status</th>
                                    <th scope="col" class="px-5 py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach ($artworks as $artwork)
                                    <tr>
                                        <td class="px-5 py-4">
                                            <span class="block font-medium text-slate-800">{{ $artwork->title }}</span>
                                            <span class="mt-1 block text-[10px] text-slate-400">{{ $artwork->category }} · PHP {{ number_format($artwork->price_amount / 100, 2) }}</span>
                                        </td>
                                        <td class="px-5 py-4">{{ $artwork->artist->display_name }}</td>
                                        <td class="px-5 py-4">{{ $artwork->is_published ? 'Published' : 'Unpublished' }}</td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center justify-end gap-4">
                                                <a href="{{ route('admin.artworks.edit', $artwork) }}" class="font-medium text-indigo-600 transition hover:underline">Edit</a>
                                                <form method="POST" action="{{ route('admin.artworks.destroy', $artwork) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-red-600 transition hover:underline">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-100 px-5 py-3">
                        {{ $artworks->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
