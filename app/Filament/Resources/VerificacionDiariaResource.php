<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificacionDiariaResource\Pages;
use App\Models\VerificacionDiaria;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VerificacionDiariaResource extends Resource
{
    protected static ?string $model = VerificacionDiaria::class;

    protected static ?string $navigationIcon   = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup  = 'Permisos';
    protected static ?string $modelLabel       = 'Personal autorizado';// singular
    protected static ?string $pluralModelLabel = 'Personal autorizado';// plural
    protected static ?string $navigationLabel  = 'Personal autorizado';// menú
    protected static ?int    $navigationSort   = 3;

    

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with(['permiso', 'trabajador'])->hoyNoVerificado()
            )
            ->columns([
                Tables\Columns\TextColumn::make('contratista_o_unidad')
                    ->label('Terceros / Unidad Privada')
                    ->alignCenter()
                    ->sortable()   
                    ->getStateUsing(fn ($record) =>
                        $record->permiso?->contratistas?->descripcion
                        ?? $record->permiso?->local?->option_label
                        ?? '-'
                    ),

                Tables\Columns\TextColumn::make('permiso.tipoPermiso.descripcion')
                    ->label('Tipo Permiso')
                    ->alignCenter()
                    ->sortable()   
                    ->searchable(), 

                Tables\Columns\TextColumn::make('trabajador.nombre')
                    ->label('Nombre')
                    ->alignCenter()
                    ->sortable()   
                    ->searchable(), 

                Tables\Columns\TextColumn::make('trabajador.documento')
                    ->label('Documento')
                    ->alignCenter()
                    ->sortable()   
                    ->searchable(), 

                Tables\Columns\TextColumn::make('dias_autorizados')
                    ->label('Días Autorizados')
                    ->alignCenter()
                    ->sortable()   
                    ->searchable(), 

                Tables\Columns\CheckboxColumn::make('verificado')
                    ->label('Verificación')
                    ->alignCenter()
                    
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('guardar')
                    ->label('Guardar')
                    ->color('success')
                    ->modalHeading('Verificación diaria')
                    ->form([
                        Forms\Components\Checkbox::make('confirmar')
                            ->label('Confirmar verificación de hoy')
                            ->required(),
                    ])
                    ->visible(fn (VerificacionDiaria $record) => ! $record->verificado)
                    ->action(fn (VerificacionDiaria $record, array $data) => static::guardarVerificacion($record, $data)),
            ])
            ->bulkActions([]);
    }

    /*
    |--------------------------------------------------------------------------
    | Custom actions
    |--------------------------------------------------------------------------
    */
    protected static function guardarVerificacion(VerificacionDiaria $record, array $data): void
    {
        if (! ($data['confirmar'] ?? false)) {
            return;
        }

        $record->update([
            'verificado'    => true,
            'fecha'         => now()->toDateString(),
            'hora'          => now()->toTimeString(),
            'verificadopor' => Auth::user()->name ?? 'Sistema',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerificacionDiarias::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    // En App\Models\VerificacionDiaria
public function scopeVerificados($query)
{
    return $query->where('verificado', true);
}
}
