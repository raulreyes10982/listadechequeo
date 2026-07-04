<?php

namespace App\Filament\Resources\ColaboradorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';

    protected static ?string $title = 'Documentos';
    protected static ?string $icon  = 'heroicon-o-paper-clip';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del documento')
                    ->placeholder('Ej: Cédula, Contrato, Certificado EPS')
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('archivo')
                    ->label('Archivo')
                    ->directory('colaboradores/documentos')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(5120) // 5MB
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('tipo', $state->getClientOriginalExtension());
                        }
                    })
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('tipo'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Documento')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => strtoupper($state ?? '—')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subido')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Subir documento')
                    ->modalHeading('Subir nuevo documento'),
            ])
            ->actions([
                // ✅ Descargar documento individual
                Tables\Actions\Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => \Illuminate\Support\Facades\Storage::url($record->archivo))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
