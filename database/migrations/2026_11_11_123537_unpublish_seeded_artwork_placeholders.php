<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('artworks')
            ->whereIn('slug', $this->placeholderSlugs())
            ->update(['is_published' => false]);
    }

    public function down(): void
    {
        DB::table('artworks')
            ->whereIn('slug', $this->placeholderSlugs())
            ->update(['is_published' => true]);
    }

    /**
     * @return list<string>
     */
    private function placeholderSlugs(): array
    {
        return [
            'amigo',
            'sample-chromatic-drift',
            'sample-quiet-geometry',
            'sample-digital-bloom',
            'sample-midnight-city',
            'sample-soft-machines',
            'sample-paper-garden',
            'sample-afterglow',
            'sample-archive-01',
        ];
    }
};
