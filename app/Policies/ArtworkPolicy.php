<?php

namespace App\Policies;

use App\Models\Artwork;
use App\Models\User;

class ArtworkPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Artwork $artwork): bool
    {
        return $this->isOwnerOrAdmin($user, $artwork);
    }

    public function delete(User $user, Artwork $artwork): bool
    {
        return $this->isOwnerOrAdmin($user, $artwork);
    }

    private function isOwnerOrAdmin(User $user, Artwork $artwork): bool
    {
        return $user->is_admin
            || $artwork->artist?->user_id === $user->id;
    }
}
