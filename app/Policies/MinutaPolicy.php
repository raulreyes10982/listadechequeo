<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Minuta;
use Illuminate\Auth\Access\HandlesAuthorization;

class MinutaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_minuta');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Minuta $minuta): bool
    {
        return $user->can('view_minuta');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_minuta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Minuta $minuta): bool
    {
        return $user->can('update_minuta');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Minuta $minuta): bool
    {
        return $user->can('delete_minuta');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_minuta');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Minuta $minuta): bool
    {
        return $user->can('force_delete_minuta');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_minuta');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Minuta $minuta): bool
    {
        return $user->can('restore_minuta');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_minuta');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Minuta $minuta): bool
    {
        return $user->can('replicate_minuta');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_minuta');
    }
}
