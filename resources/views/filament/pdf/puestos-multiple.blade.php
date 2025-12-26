<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Puestos de Seguridad</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 10px; }
        .puesto { page-break-inside: avoid; margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; }
        .qr-code { text-align: center; margin: 8px 0; }
        .info { margin: 8px 0; }
        .info table { width: 100%; border-collapse: collapse; }
        .info td { padding: 3px; border: 1px solid #ddd; }
        .info td:first-child { font-weight: bold; width: 25%; }
        .footer { margin-top: 15px; text-align: center; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Puestos de Seguridad</h2>
    </div>
    
    @foreach($puestos as $puesto)
    <div class="puesto">
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $puesto->qrCode }}" alt="QR Code">
        </div>
        
        <div class="info">
            <table>
                <tr>
                    <td>Código:</td>
                    <td>{{ $puesto->codigo }}</td>
                </tr>
                <tr>
                    <td>Puesto:</td>
                    <td>{{ $puesto->puesto }}</td>
                </tr>
                <tr>
                    <td>Horario:</td>
                    <td>{{ $puesto->inicio_hora }} - {{ $puesto->fin_hora }}</td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            <p>Token: {{ $puesto->qr_token }}</p>
        </div>
    </div>
    @endforeach
    
    <div class="footer">
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
        <p>Total de puestos: {{ count($puestos) }}</p>
    </div>
</body>
</html>
