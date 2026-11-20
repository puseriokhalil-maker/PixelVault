@php
    $friends = auth()->user()->friends()->get();
@endphp

<aside data-social-dock class="fixed bottom-5 right-5 z-40">
    <div data-social-panel class="absolute bottom-14 right-0 hidden w-72 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <div>
                <h2 class="text-xs font-semibold text-slate-900">Friends</h2>
                <p class="mt-0.5 text-[10px] text-slate-400">{{ $friends->count() }} {{ $friends->count() === 1 ? 'friend' : 'friends' }}</p>
            </div>
            <a href="{{ route('friends.index') }}" class="text-[10px] font-semibold text-indigo-600">Add friend</a>
        </div>
        <div class="max-h-72 overflow-y-auto p-2">
            @forelse ($friends as $friend)
                <div data-dock-friend="{{ $friend->id }}" class="relative flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-slate-50">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-[10px] font-semibold text-white">
                        {{ mb_strtoupper(mb_substr($friend->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-slate-800">{{ $friend->name }}</p>
                        <p class="mt-0.5 text-[10px] text-slate-400">Friends</p>
                    </div>
                    <div data-dock-context="{{ $friend->id }}" class="absolute left-2 top-10 hidden w-32 rounded-md border border-slate-200 bg-white p-1 text-[10px] shadow-lg">
                        <a href="{{ route('friends.index') }}#friend-{{ $friend->id }}" class="block rounded px-2 py-1.5 text-indigo-700 hover:bg-indigo-50">Trade NFTs</a>
                        <button type="button" class="block w-full rounded px-2 py-1.5 text-left text-slate-500 hover:bg-slate-50">Block</button>
                    </div>
                </div>
            @empty
                <p class="px-3 py-7 text-center text-[11px] text-slate-500">No friends yet.</p>
            @endforelse
        </div>
    </div>

    <button type="button" data-social-toggle aria-expanded="false" aria-label="Open friends panel" class="ml-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white shadow-xl transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="17" cy="9" r="2" stroke="currentColor" stroke-width="1.7"/>
            <path d="M3.5 19c.7-3.2 2.7-5 5.5-5s4.8 1.8 5.5 5M14 15c.9-1.4 2.1-2.1 3.5-2.1 2 0 3.4 1.3 4 3.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
    </button>
</aside>
