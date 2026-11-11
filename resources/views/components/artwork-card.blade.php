<article class="flex flex-col overflow-hidden rounded-[20px] border border-neutral-300 bg-white">
    <div class="flex h-[158px] items-center justify-center bg-[#f7f9fc]">
        @if ($artwork->image_path)
            <img src="{{ $artwork->image_url }}" alt="{{ $artwork->title }}" class="h-full w-full object-contain">
        @endif
    </div>

    <div class="flex min-h-[74px] flex-col px-2 py-2">
        <div class="flex items-baseline justify-between text-[10px] leading-3">
            <h3 class="font-semibold text-black">{{ $artwork->title }}</h3>
            <span class="font-semibold text-black">PHP {{ number_format($artwork->price_amount / 100, 2) }}</span>
        </div>
        <div class="mt-0.5 text-[10px] leading-3 text-neutral-400">
            <span>{{ $artwork->artist->display_name }}</span>
        </div>
        <div class="mt-auto flex items-center justify-between gap-1 pt-3 text-[10px] leading-3">
            <span class="font-semibold text-black">{{ $artwork->category }}</span>
            <a href="{{ route('artworks.show', $artwork) }}" class="shrink-0 text-indigo-600 transition hover:underline" aria-label="View details for {{ $artwork->title }}">View Details &gt;</a>
        </div>
        @can('update', $artwork)
            <div class="mt-3 flex items-center gap-3 border-t border-neutral-100 pt-2 text-[10px]">
                <a href="{{ route('artworks.edit', $artwork) }}" class="font-medium text-indigo-600 transition hover:underline">Edit</a>
                <form method="POST" action="{{ auth()->user()->is_admin ? route('admin.artworks.destroy', $artwork) : route('artworks.destroy', $artwork) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="font-medium text-red-600 transition hover:underline">Delete</button>
                </form>
            </div>
        @endif
    </div>
</article>