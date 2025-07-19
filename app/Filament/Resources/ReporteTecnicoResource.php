<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use App\Filament\Resources\ReporteTecnicoResource\RelationManagers;
use App\Filament\Resources\ReporteTecnicoResource\Pages;
use App\Models\ReporteTecnico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, Select, DatePicker, Wizard, Wizard\Step};
use Filament\Forms\Components\Textarea;
use Carbon\Carbon;


class ReporteTecnicoResource extends Resource
{
    protected static ?string $model = ReporteTecnico::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Reporte Tecnico';
    protected static ?string $modelLabel = 'Reporte Tecnico';
    protected static ?string $pluralModelLabel = 'Reporte Tecnico';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
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

                Select::make('equipo_id')
                    ->label('Equipo')
                    ->relationship('equipo', 'descripcion')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->nullable(),

                Select::make('tipo_intervencion_id')
                    ->label('Tipo de Intervención')
                    ->relationship('tipoIntervencion', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->nullable(),
                
                Textarea::make('descripcion')
                    ->rows(4)
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
                Tables\Columns\TextColumn::make('equipo.descripcion')
                    ->label('Equipo')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('tipoIntervencion.nombre')
                    ->label('Tipo de Intervención')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->wrap() // Permite que el texto se ajuste en varias líneas si es necesario
                    ->alignment('center')
                    ->sortable()
                    ->searchable(),
                    //->toggleable(isToggledHiddenByDefault: true),
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
                //Tables\Actions\EditAction::make()->label('editar')->modalWidth(MaxWidth::SixExtraLarge),
                Tables\Actions\EditAction::make()->label('editar')->modalWidth('3xl'),
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
            'index' => Pages\ListReporteTecnicos::route('/'),
            //'create' => Pages\CreateReporteTecnico::route('/create'),
            //'edit' => Pages\EditReporteTecnico::route('/{record}/edit'),
        ];
    }
}
