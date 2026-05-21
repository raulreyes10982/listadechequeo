<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuestoSeguridadResource\Pages;
use App\Models\PuestoSeguridad;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PuestoSeguridadResource extends Resource
{
    protected static ?string $model = PuestoSeguridad::class;

    protected static ?string $navigationGroup = 'Programación';

    protected static ?string $navigationLabel = 'Puestos de Seguridad';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 1;

    public static function puedeGestionarQr(?User $user = null): bool{
        // ✅ Garantiza que $user sea una instancia de User, no de Authenticatable
        $user ??= User::find(Auth::id());

        if (! $user) {
            return false;
    }

    if ($user->hasAnyRole(['super_admin', 'administrador', 'supervisor'])) {
        return true;
    }

    return $user->can('generate_qr')
        || $user->can('update_puesto::seguridad')
        || $user->can('view_puesto::seguridad');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->columnSpanFull()
                    ->maxLength(250),
                Forms\Components\TextInput::make('puesto')
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
                        if (! $record->inicio_hora || ! $record->fin_hora) {
                            return '—';
                        }

                        try {
                            $inicio = \Carbon\Carbon::parse($record->inicio_hora);
                            $fin = \Carbon\Carbon::parse($record->fin_hora);

                            if ($fin->lessThan($inicio)) {
                                $fin->addDay();
                            }

                            $horas = $inicio->diffInMinutes($fin) / 60;

                            return round($horas, 1).' horas';
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
                        if (! $record->qr_expira) {
                            return 'gray';
                        }

                        return $record->qr_expira->isPast() ? 'danger' : 'success';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn () => static::puedeGestionarQr())
                    ->action(fn (PuestoSeguridad $record) => static::descargarQrPdf($record)),

                Tables\Actions\EditAction::make()
                ->label('Editar'),

                Tables\Actions\DeleteAction::make()
                ->label('Eliminar'),
                
                /*
                Tables\Actions\Action::make('viewQr')
                    ->label('Ver QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn () => static::puedeGestionarQr())
                    ->modalHeading(fn (PuestoSeguridad $record) => 'Código QR - '.$record->codigo)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (PuestoSeguridad $record) {
                        $record->generarQrSiNecesario();
                        $qrCode = QrCode::size(220)->generate($record->qr_content);

                        return view('filament.forms.components.qr-view', [
                            'qrCode' => $qrCode,
                            'puesto' => $record,
                            'downloadUrl' => route('puestos.qr.descargar', $record),
                        ]);
                    }),
                */
                    /*
                Tables\Actions\Action::make('downloadQr')
                    ->label('Descargar QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn () => static::puedeGestionarQr())
                    ->action(fn (PuestoSeguridad $record) => static::descargarQrImagen($record)),
                */  
               
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('downloadMultiplePdf')
                        ->label('Descargar PDFs')
                        ->icon('heroicon-o-document-arrow-down')
                        ->visible(fn () => static::puedeGestionarQr())
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $puestos = $records->map(function (PuestoSeguridad $record) {
                                $record->generarQrSiNecesario();
                                $record->qrCode = static::qrComoBase64Png($record);
                                $record->qrEsPng = extension_loaded('imagick');

                                return $record;
                            });

                            $pdf = Pdf::loadView('filament.pdf.puestos-multiple', [
                                'puestos' => $puestos,
                            ]);

                            return Response::streamDownload(
                                fn () => print($pdf->output()),
                                'puestos-seguridad.pdf'
                            );
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('PDFs generados')
                                ->body('El archivo PDF se ha descargado correctamente.')
                        ),
                ]),
            ]);
    }

    public static function descargarQrImagen(PuestoSeguridad $puesto): StreamedResponse
    {
        $puesto->generarQrSiNecesario();

        $nombreArchivo = 'qr-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $puesto->codigo ?? (string) $puesto->id);

        if (extension_loaded('imagick')) {
            $contenido = QrCode::format('png')->size(500)->margin(2)->generate($puesto->qr_content);

            return Response::streamDownload(
                fn () => print($contenido),
                "{$nombreArchivo}.png",
                ['Content-Type' => 'image/png']
            );
        }

        $contenido = QrCode::format('svg')->size(500)->margin(2)->generate($puesto->qr_content);

        return Response::streamDownload(
            fn () => print($contenido),
            "{$nombreArchivo}.svg",
            ['Content-Type' => 'image/svg+xml']
        );
    }

    public static function descargarQrPdf(PuestoSeguridad $puesto): StreamedResponse
    {
        $puesto->generarQrSiNecesario();

        $pdf = Pdf::loadView('filament.pdf.puesto-individual', [
            'puesto' => $puesto,
            'qrCode' => static::qrComoBase64Png($puesto),
            'qrEsPng' => extension_loaded('imagick'),
        ]);

        $nombre = 'puesto-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $puesto->codigo ?? (string) $puesto->id).'.pdf';

        return Response::streamDownload(
            fn () => print($pdf->output()),
            $nombre
        );
    }

    /**
     * Imagen QR en base64 para PDF (PNG si hay Imagick, si no SVG).
     */
    public static function qrComoBase64Png(PuestoSeguridad $puesto): string
    {
        if (extension_loaded('imagick')) {
            return base64_encode(
                QrCode::format('png')->size(200)->generate($puesto->qr_content)
            );
        }

        return base64_encode(
            QrCode::format('svg')->size(200)->generate($puesto->qr_content)
        );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPuestoSeguridads::route('/'),
        ];
    }
}
