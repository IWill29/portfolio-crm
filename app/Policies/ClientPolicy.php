<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Noteic, vai lietotājs drīkst redzēt klientu sarakstu.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('access crm');
    }

    /**
     * Noteic, vai lietotājs drīkst skatīt konkrētu klientu.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->can('access crm');
    }

    /**
     * Noteic, vai lietotājs drīkst izveidot jaunu klientu.
     */
    public function create(User $user): bool
    {
        return $user->can('access crm');
    }

    /**
     * Noteic, vai lietotājs drīkst labot klientu.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->can('access crm');
    }

    /**
     * ✅ DROŠĪBAS FILTRS: Tikai Admin (kam ir 'delete records') drīkst dzēst.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->can('delete records');
    }

    /**
     * Noteic, vai lietotājs drīkst veikt masveida dzēšanu Filament tabulā.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete records');
    }
}
