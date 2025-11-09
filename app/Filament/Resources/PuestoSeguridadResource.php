<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuestoSeguridadResource\Pages;
use App\Models\PuestoSeguridad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Filament\Notifications\Notification;

class PuestoSeguridadResource extends Resource
{
    protected static ?string $model = PuestoSeguridad::class;

    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Puestos de Seguridad';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?int $navigationSort = 1;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    //->required()
                    ->columnSpanFull()
                    ->maxLength(250),
                Forms\Components\TextInput::make('puesto')
                    //->required()
                    ->columnSpanFull()
                    ->maxLength(250),
                TimePicker::make('inicio_hora')
                    ->label('Hora de Inicio')
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->native(false),
                TimePicker::make('fin_hora')
                    ->label('Hora de Fin')
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->native(false),
                Forms\Components\TextInput::make('descripcion')
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->maxLength(250),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('puesto')
                    ->label('Puesto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('horas_trabajadas')
                    ->label('Turno')
                    ->getStateUsing(function ($record) {
                        if (!$record->inicio_hora || !$record->fin_hora) {
                            return '—';
                        }

                        try {
                            $inicio = \Carbon\Carbon::parse($record->inicio_hora);
                            $fin = \Carbon\Carbon::parse($record->fin_hora);

                            if ($fin->lessThan($inicio)) {
                                $fin->addDay();
                            }

                            $horas = $inicio->diffInMinutes($fin) / 60;
                            return round($horas, 1) . ' horas';
                        } catch (\Exception $e) {
                            return 'Error';
                        }
                    }),
                TextColumn::make('inicio_hora')
                    ->label('Inicia')
                    ->time(),
                TextColumn::make('fin_hora')
                    ->label('Finaliza')
                    ->time(),
                TextColumn::make('descripcion')
                    ->label('Observación')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('qr_expira')
                    ->label('QR Expira')
                    ->date()
                    ->color(function ($record) {
                        if (!$record->qr_expira) {
                            return 'gray';
                        }
                        return $record->qr_expira->isPast() ? 'danger' : 'success';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
                
                // ✅ Acción para ver QR
                Tables\Actions\Action::make('viewQr')
                    ->label('Ver QR')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading(fn ($record) => 'Código QR - ' . $record->codigo)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function ($record) {
                        $record->generarQrSiNecesario();
                        $qrCode = QrCode::size(200)->generate($record->qr_content);

                        return view('filament.forms.components.qr-view', [
                            'qrCode' => $qrCode,
                            'puesto' => $record,
                        ]);
                    }),
                
                // ✅ Acción para descargar PDF individual con SVG
                Tables\Actions\Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        $record->generarQrSiNecesario();
                        $qrCode = base64_encode(
                            QrCode::format('svg')->size(150)->generate($record->qr_content)
                        );
                        
                        $pdf = Pdf::loadView('filament.pdf.puesto-individual', [
                            'puesto' => $record,
                            'qrCode' => $qrCode
                        ]);
                        
                        return Response::streamDownload(
                            fn () => print($pdf->output()),
                            "puesto-{$record->codigo}.pdf"
                        );
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('PDF generado')
                            ->body('El PDF se ha descargado correctamente.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // ✅ Acción masiva para descargar PDF múltiple con SVG
                    Tables\Actions\BulkAction::make('downloadMultiplePdf')
                        ->label('Descargar PDFs')
                        ->icon('heroicon-o-document-arrow-down')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $puestos = $records->map(function ($record) {
                                $record->generarQrSiNecesario();
                                $record->qrCode = base64_encode(
                                    QrCode::format('svg')->size(100)->generate($record->qr_content)
                                );
                                return $record;
                            });
                            
                            $pdf = Pdf::loadView('filament.pdf.puestos-multiple', [
                                'puestos' => $puestos
                            ]);
                            
                            return Response::streamDownload(
                                fn () => print($pdf->output()),
                                "puestos-seguridad.pdf"
                            );
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('PDFs generados')
                                ->body('Los PDFs se han descargado correctamente.')
                        ),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPuestoSeguridads::route('/'),
            //'create' => Pages\CreatePuestoSeguridad::route('/create'),
            //'edit' => Pages\EditPuestoSeguridad::route('/{record}/edit'),
        ];
    }
}
