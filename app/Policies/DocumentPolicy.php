<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Noteic, vai lietotājs drīkst redzēt dokumentu sarakstu.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('access crm');
    }

    /**
     * Noteic, vai lietotājs drīkst pievienot jaunu dokumentu.
     */
    public function create(User $user): bool
    {
        return $user->can('access crm');
    }

    /**
     * Noteic, vai lietotājs drīkst labot dokumenta aprakstu.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->can('access crm');
    }

    /**
     * ✅ DROŠĪBAS FILTRS: Tikai Admin (kam ir 'delete records') drīkst dzēst dokumentus.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->can('delete records');
    }

    /**
     * Noteic, vai lietotājs drīkst veikt masveida dokumentu dzēšanu.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete records');
    }
}
