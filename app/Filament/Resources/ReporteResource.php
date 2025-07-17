<?php

namespace App\Filament\Resources;

use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReporteResource\RelationManagers;
use App\Filament\Resources\ReporteResource\Pages;
use App\Models\Reporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Carbon\Carbon;


class ReporteResource extends Resource
{
    
    protected static ?string $model = Reporte::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?int $navigationSort = 3;

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
                Tables\Columns\TextColumn::make('tipoReporte.descripcion')
                    ->label('Tipo de Reporte')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),
                /*Tables\Columns\TextColumn::make('categoriaReporte.descripcion')
                    ->label('Categoria')
                    ->sortable()
                    ->searchable()
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),*/
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->sortable()
                    ->searchable()
                    ->wrap() // Permite que el texto se ajuste en varias líneas si es necesario
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('imagenes')
                    ->label('Imágenes')
                    ->getStateUsing(function ($record) {
                        // Asume que 'imagenes' es un string o un array
                        $imagePath = is_array($record->imagenes) ? collect($record->imagenes)->first() : $record->imagenes;
                        return $imagePath ? asset('storage/' . $imagePath) : null;
                    })
                    ->url(function ($record) {
                        // Genera la URL para la imagen
                        $imagePath = is_array($record->imagenes) ? collect($record->imagenes)->first() : $record->imagenes;
                        return $imagePath ? asset('storage/' . $imagePath) : null;
                    }, shouldOpenInNewTab: true) // Abre el enlace en una nueva pestaña
                    ->sortable()
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('editar')->modalWidth('3xl'),
                //Tables\Actions\EditAction::make()->label('editar')->modalWidth(MaxWidth::SixExtraLarge),
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
            //
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
