<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReporteResource\Pages;
use App\Filament\Resources\ReporteResource\RelationManagers;
use App\Filament\Resources\ReporteResource\RelationManagers\SeguimientosRelationManager;
use App\Models\Reporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Carbon\Carbon;


class ReporteResource extends Resource
{
    protected static ?string $model = Reporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?int $navigationSort = 9;


    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                Forms\Components\Hidden::make('subidopor'),
                Forms\Components\TimePicker::make('hora')
                    ->label('Hora')
                    ->format('H:i')
                    ->default(Carbon::now()->format('H:i'))
                    ->hidden(),
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->format('Y-m-d')
                    ->default(Carbon::now()->format('Y-m-d'))
                    ->hidden(),
                Forms\Components\Select::make('categoria_reporte_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'descripcion')
                    ->preload() // 
                    ->searchable()
                    ->required()
                    ->columnSpan(3),
                Forms\Components\Select::make('tipo_reporte_id')
                    ->label('Tipo de Reporte')
                    ->relationship('tipoReporte', 'descripcion')
                    ->preload() // 
                    ->searchable()
                    ->required()
                    ->columnSpan(3),
                Forms\Components\Select::make('zona_id')
                    ->relationship('zona', 'descripcion')
                    ->preload() // 
                    ->searchable()
                    ->required()
                    ->columnSpan(3),
                Select::make('local_id')
                    ->label('Unidad Privada')
                    ->relationship('local', 'descripcion')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->option_label)
                    ->searchable()
                    //->required()
                    ->columnSpan(3)
                    ->preload(),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(500)
                    ->columnSpan(5),
                FileUpload::make('imagenes')
                    ->label('Imágenes')
                    ->directory('reportes')
                    ->disk('public')
                    ->image()
                    ->multiple()
                    ->maxFiles(3)
                    ->columnSpan(1),
                Forms\Components\Select::make('prioridad_id')
                    ->relationship('prioridad', 'descripcion')
                    ->preload() // 
                    ->searchable()
                    ->required()
                    ->columnSpan(3),
                Forms\Components\Select::make('estado_id')
                    ->relationship('estado', 'descripcion')
                    ->default(null),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subidopor')
                    ->label('Subido por')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/M/Y')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->dateTime('H:i')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('categoria.descripcion')
                    ->label('Reporte')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipoReporte.descripcion')
                    ->label('Tipo de Reporte')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zona.descripcion')
                    ->label('Zona')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('prioridad.descripcion')
                    ->label('Prioridad')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('estado.descripcion')
                    ->label('Estado')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('unidad_privada')
                ->label('Unidad Privada')
                    ->getStateUsing(fn ($record) => $record->local?->option_label)
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->wrap()
                    ->alignment('center')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\EditAction::make()->label('editar')->modalWidth(MaxWidth::SixExtraLarge),
                Tables\Actions\DeleteAction::make()->label('eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
{
    return [
        SeguimientosRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportes::route('/'),
            //'create' => Pages\CreateReporte::route('/create'),
            //'edit' => Pages\EditReporte::route('/{record}/edit'),
        ];
    }
}
