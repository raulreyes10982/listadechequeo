<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reporte;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportePolicy
{
    use HandlesAuthorization;

    /**
     * Determinar si el usuario puede ver cualquier modelo.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_reporte');
    }

    /**
     * Determinar si el usuario puede ver el modelo.
     */
    public function view(User $user, Reporte $reporte): bool
    {
        return $user->can('view_reporte');
    }

    /**
     * Determinar si el usuario puede crear modelos.
     */
    public function create(User $user): bool
    {
        return $user->can('create_reporte');
    }

    /**
     * Determinar si el usuario puede actualizar el modelo.
     */
    public function update(User $user, Reporte $reporte): bool
    {
        return $user->can('update_reporte');
    }

    /**
     * Determinar si el usuario puede eliminar el modelo.
     */
    public function delete(User $user, Reporte $reporte): bool
    {
        return $user->can('delete_reporte');
    }

    /**
     * Determinar si el usuario puede eliminar múltiples registros.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_reporte');
    }

    /**
     * Determinar si el usuario puede eliminar permanentemente el modelo.
     */
    public function forceDelete(User $user, Reporte $reporte): bool
    {
        return $user->can('force_delete_reporte');
    }

    /**
     * Determinar si el usuario puede eliminar permanentemente múltiples registros.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_reporte');
    }

    /**
     * Determinar si el usuario puede restaurar el modelo.
     */
    public function restore(User $user, Reporte $reporte): bool
    {
        return $user->can('restore_reporte');
    }

    /**
     * Determinar si el usuario puede restaurar múltiples registros.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_reporte');
    }

    /**
     * Determinar si el usuario puede replicar el modelo.
     */
    public function replicate(User $user, Reporte $reporte): bool
    {
        return $user->can('replicate_reporte');
    }

    /**
     * Determinar si el usuario puede reordenar los registros.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_reporte');
    }
}