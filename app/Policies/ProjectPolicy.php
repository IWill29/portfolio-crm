<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Piekļuve projektu sarakstam.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('access crm');
    }

    /**
     * Projekta izveide un labošana.
     */
    public function create(User $user): bool
    {
        return $user->can('access crm');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('access crm');
    }

    /**
     * ✅ FINANŠU DROŠĪBA: Pielāgota metode budžeta redzamībai.
     * Mēs to izmantosime Filament kolonnā, lai paslēptu naudu no Manager.
     */
    public function viewFinancials(User $user): bool
    {
        return $user->can('view financial data');
    }

    /**
     * ✅ DZĒŠANAS DROŠĪBA: Tikai Admin drīkst dzēst projektus.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->can('delete records');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete records');
    }
}
