@extends('layouts.app')

@section('title', ($artwork->exists ? 'Edit' : 'Add').' Artwork - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-64px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route(auth()->user()->is_admin ? 'admin.artworks.index' : 'artworks.mine') }}" class="mb-5 inline-flex items-center gap-2 text-xs font-medium text-slate-500 transition hover:text-indigo-600">
                <span aria-hidden="true">←</span>
                Back to my artworks
            </a>

            <div class="mb-6">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Your PixelVault</p>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $artwork->exists ? 'Edit your artwork' : 'Add your artwork' }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    @if (auth()->user()->is_admin)
                        Update artwork details or manage any artwork in the gallery.
                    @else
                        Share your artwork with the gallery. You can only edit or remove your own pieces.
                    @endif
                </p>
            </div>

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-800">
                    <p class="font-semibold">Please correct the highlighted fields.</p>
                </div>
            @endif

            <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 sm:p-7">
                @csrf
                @if ($artwork->exists)
                    @method('PUT')
                @endif

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title" class="mb-1.5 block text-xs font-medium text-slate-700">Artwork title</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $artwork->title) }}" maxlength="255" required class="h-10 w-full rounded-md border border-slate-300 px-3 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="mb-1.5 block text-xs font-medium text-slate-700">Category</label>
                        <select id="category" name="category" required class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                            <option value="">Choose a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category', $artwork->category) === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="mb-1.5 block text-xs font-medium text-slate-700">Price (PHP)</label>
                        <input id="price" name="price" type="number" value="{{ old('price', $artwork->exists ? number_format($artwork->price_amount / 100, 2, '.', '') : '') }}" min="0.01" max="99999999.99" step="0.01" required class="h-10 w-full rounded-md border border-slate-300 px-3 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        @error('price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="mb-1.5 block text-xs font-medium text-slate-700">Description</label>
                        <textarea id="description" name="description" rows="4" maxlength="5000" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('description', $artwork->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="image" class="mb-1.5 block text-xs font-medium text-slate-700">Artwork image</label>
                        @if ($artwork->image_path)
                            <img src="{{ $artwork->image_url }}" alt="{{ $artwork->title }}" class="mb-3 max-h-48 w-full rounded-md bg-slate-50 object-contain">
                        @endif
                        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(! $artwork->exists) class="block w-full rounded-md border border-slate-300 bg-white text-sm text-slate-600 file:mr-3 file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-medium file:text-slate-700">
                        <p class="mt-1 text-[11px] text-slate-500">
                            JPG, PNG, or WebP. Maximum file size: 5 MB.
                            @if ($artwork->exists)
                                Leave empty to keep the current image.
                            @endif
                        </p>
                        @error('image')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ route(auth()->user()->is_admin ? 'admin.artworks.index' : 'artworks.mine') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-slate-300 px-4 text-xs font-medium text-slate-600 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-indigo-600 px-5 text-xs font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ $artwork->exists ? 'Save changes' : 'Add to gallery' }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
