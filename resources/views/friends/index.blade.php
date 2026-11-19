@extends('layouts.app')

@section('title', 'Friends - PixelVault')

@section('content')
    <section class="min-h-[calc(100vh-50px)] bg-[#f7f9fc] px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-5xl">
            <div class="mb-6">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Community</p>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Friends</h1>
                <p class="mt-1 text-sm text-slate-500">Find existing PixelVault users and manage your friends.</p>
            </div>

            @if (session('status'))
                <p role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">{{ session('status') }}</p>
            @endif

            <div class="grid gap-5 lg:grid-cols-[280px_minmax(0,1fr)]">
                <aside class="rounded-xl border border-slate-200 bg-white p-4">
                    <h2 class="text-sm font-semibold text-slate-900">Find users</h2>
                    <form method="GET" action="{{ route('friends.index') }}" class="mt-3">
                        <label for="friend-search" class="sr-only">Search users</label>
                        <input id="friend-search" name="search" value="{{ $search }}" placeholder="Name or email" class="h-9 w-full rounded-md border border-slate-300 px-3 text-xs text-slate-700 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <button type="submit" class="mt-2 h-9 w-full rounded-md bg-indigo-600 text-xs font-semibold text-white transition hover:bg-indigo-700">Search</button>
                    </form>

                    <div class="mt-4 grid gap-2">
                        @forelse ($searchResults as $foundUser)
                            <div data-user-card="{{ $foundUser->id }}" class="relative rounded-lg border border-slate-200 px-3 py-3">
                                <p class="text-xs font-semibold text-slate-800">{{ $foundUser->name }}</p>
                                <p class="mt-1 truncate text-[10px] text-slate-400">{{ $foundUser->email }}</p>
                                <form method="POST" action="{{ route('friends.store', $foundUser) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-semibold text-indigo-600 transition hover:text-indigo-800">Add friend</button>
                                </form>
                                <div data-user-context="{{ $foundUser->id }}" class="absolute left-full top-2 z-20 hidden w-28 rounded-md border border-slate-200 bg-white p-1 text-[11px] shadow-lg">
                                    <form method="POST" action="{{ route('friends.store', $foundUser) }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded px-2 py-1.5 text-left text-indigo-700 hover:bg-indigo-50">Add friend</button>
                                    </form>
                                    <button type="button" class="block w-full rounded px-2 py-1.5 text-left text-slate-600 hover:bg-slate-50">Block</button>
                                </div>
                            </div>
                        @empty
                            @if ($search !== '')
                                <p class="rounded-lg bg-slate-50 px-3 py-5 text-center text-xs text-slate-500">No users found.</p>
                            @else
                                <p class="text-xs leading-5 text-slate-500">Enter a name or email to search existing users.</p>
                            @endif
                        @endforelse
                    </div>
                </aside>

                <section class="rounded-xl border border-slate-200 bg-white p-5">
                    <h2 class="text-sm font-semibold text-slate-900">My friends</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @forelse ($friends as $friend)
                            <article id="friend-{{ $friend->id }}" data-friend-card="{{ $friend->id }}" class="rounded-lg border border-slate-200 p-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white">
                                        {{ mb_strtoupper(mb_substr($friend->name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-semibold text-slate-800">{{ $friend->name }}</p>
                                        <p class="mt-1 truncate text-[10px] text-slate-400">{{ $friend->email }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <form method="POST" action="{{ route('friends.trade', $friend) }}" class="flex items-center gap-2">
                                        @csrf
                                        <select name="offered_artwork_id" aria-label="NFT to offer" class="h-8 max-w-28 rounded-md border border-slate-300 px-2 text-[10px]">
                                            @foreach (auth()->user()->ownedArtworks()->get() as $ownedArtwork)
                                                <option value="{{ $ownedArtwork->id }}">{{ $ownedArtwork->title }}</option>
                                            @endforeach
                                        </select>
                                        <select name="requested_artwork_id" aria-label="NFT to request" class="h-8 max-w-28 rounded-md border border-slate-300 px-2 text-[10px]">
                                            @foreach ($friend->ownedArtworks()->get() as $friendArtwork)
                                                <option value="{{ $friendArtwork->id }}">{{ $friendArtwork->title }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="h-8 rounded-md bg-indigo-600 px-3 text-[10px] font-semibold text-white">Trade</button>
                                    </form>
                                    <form method="POST" action="{{ route('friends.destroy', $friend) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] font-medium text-slate-400 hover:text-red-600">Remove</button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <p class="col-span-full rounded-lg bg-slate-50 px-4 py-8 text-center text-xs text-slate-500">You have not added any friends yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
