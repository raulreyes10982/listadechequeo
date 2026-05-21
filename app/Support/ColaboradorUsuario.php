<?php

namespace App\Support;

use App\Models\Colaborador;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ColaboradorUsuario
{
    public static function actual(): ?Colaborador
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return null;
        }

        return $user->resolverColaborador();
    }
}
