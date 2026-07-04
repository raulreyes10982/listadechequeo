<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CambiarPassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Cambiar Contraseña';
    protected static ?string $slug            = 'cambiar-password';
    protected static ?int    $navigationSort  = 99;

    // Ocultar del menú lateral — solo accesible por redirect
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.cambiar-password';

    // ✅ Página de cambio de contraseña — siempre accesible para cualquier
    // usuario autenticado (no depende del permiso de Shield, es de seguridad)
    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('🔑 Cambiar contraseña')
                    ->description('Por seguridad debes establecer una contraseña personal antes de continuar.')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::min(8)->mixedCase()->numbers())
                            ->helperText('Mínimo 8 caracteres, una mayúscula y un número'),

                        TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password')
                            ->helperText('Debe coincidir con la nueva contraseña'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function guardar(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();

        // Actualizar contraseña y desactivar el flag
        $user->update([
            'password'             => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        Notification::make()
            ->title('✅ Contraseña actualizada correctamente')
            ->body('Ya puedes usar el sistema con tu nueva contraseña.')
            ->success()
            ->send();

        // Redirigir al dashboard
        $this->redirect(route('filament.dashboard.pages.dashboard'));
    }
}
