<header class="h-[42px] bg-[#1e293b] text-white">
    <nav class="mx-auto flex h-full items-center justify-between gap-4 px-2.5">
        <a href="/" class="flex shrink-0 items-center gap-3" aria-label="PixelVault home">
            <span class="h-6 w-6 rounded bg-[#d9d9dc]"></span>
            <span class="text-xs font-medium tracking-wide">PixelVault</span>
        </a>

        <label class="hidden h-7 w-full max-w-[328px] items-center gap-2 rounded-md border border-slate-500/40 bg-slate-700 px-2 text-slate-400 sm:flex">
            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"></circle>
                <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
            </svg>
            <input
                type="search"
                aria-label="Search artworks, artists, or collections"
                placeholder="Search artworks, artists, or collections..."
                class="min-w-0 flex-1 bg-transparent text-[9px] text-white outline-none placeholder:text-slate-400"
            >
        </label>

        <div class="flex shrink-0 items-center gap-4 text-[9px]">
            <a href="/" class="transition hover:text-indigo-300">Home</a>
            <a href="#collection" class="transition hover:text-indigo-300">Gallery</a>
            <a href="#collection" class="transition hover:text-indigo-300">Cart</a>
            <a href="{{ route('login') }}" class="rounded border border-slate-500/50 px-1.5 py-0.5 transition hover:bg-slate-700">Login</a>
        </div>
    </nav>
</header>
