<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NovedadResource\Pages;
use App\Filament\Resources\NovedadResource\RelationManagers;
use App\Models\Novedad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use Filament\Support\Enums\VerticalAlignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Storage;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;




class NovedadResource extends Resource
{

    protected static ?string $model = Novedad::class;

    protected static ?string $navigationGroup = 'Novedades';
    protected static ?string $navigationLabel = 'Registro de Novedades';
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?int $navigationSort = 2;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('subidopor'),// Oculta y asigna el nombre del usuario autenticado
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha reporte')
                    ->format('d/M/Y')
                    ->default(Carbon::now()->format('Y/m/d'))
                    ->hidden(), // Oculta el campo
                Forms\Components\TimePicker::make('hora')
                    ->format('H:i')
                    ->displayFormat('H:i')
                    //->withoutSeconds()
                    ->default(Carbon::now()->format('H:i'))
                    ->hidden(), // Oculta el campo
                Forms\Components\Select::make('tipo_novedad_id')
                    ->label('Tipo novedad')
                    ->relationship('tipoNovedad', 'descripcion') // ← nombre correcto del método de relación
                    ->searchable()
                    ->columnSpanFull()
                    ->required()
                    ->preload(),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Observaciones')
                    ->maxLength(500)
                    ->rows(7)
                    ->maxLength(500)
                    ->columnSpanFull(),
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
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/M/Y')
                    ->searchable()
                    ->sortable()
                    ->alignment('center')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->dateTime('H:i')
                    ->searchable()
                    ->sortable()
                    ->alignment('center')
                    ->toggleable(),               
                Tables\Columns\TextColumn::make('tipoNovedad.descripcion')
                    ->label('Tipo novedad')
                    ->sortable()
                    ->searchable()
                    ->alignment('center')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->sortable()
                    ->wrap() // Permite que el texto se ajuste en varias líneas si es necesario
                    ->searchable()
                    ->alignment('center')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->toggleable(),
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
                Tables\Actions\EditAction::make()->modalWidth('lg'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListNovedads::route('/'),
            //'create' => Pages\CreateNovedad::route('/create'),
            //'edit' => Pages\EditNovedad::route('/{record}/edit'),
        ];
    }
}
