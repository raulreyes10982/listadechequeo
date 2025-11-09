<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Puesto de Seguridad - {{ $puesto->codigo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            color: #444;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            width: 180px;
            height: 180px;
        }
        .info {
            margin: 0 auto;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px 15px;
            background-color: #f9f9f9;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info td {
            padding: 6px 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        .info td:first-child {
            font-weight: bold;
            width: 30%;
            color: #555;
        }
        .info tr:last-child td {
            border-bottom: none;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Puesto de Seguridad</h2>
        <p><strong>{{ $puesto->codigo }}</strong></p>
    </div>
    
    <div class="qr-code">
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
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
            <tr>
                <td>Descripción:</td>
                <td>{{ $puesto->descripcion }}</td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
        <p>Token: {{ $puesto->qr_token }}</p>
    </div>
</body>
</html>
