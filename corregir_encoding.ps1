# Script para corregir encoding en archivos PHP
Write-Host "Corrigiendo encoding..." -ForegroundColor Yellow

$files = Get-ChildItem -Path "app\Filament\Resources" -Filter *.php -Recurse
$correctionMap = @{
    "Ã¡" = "á"; "Ã©" = "é"; "Ã­" = "í"; "Ã³" = "ó"; "Ãº" = "ú"
    "Ã±" = "ñ"; "Ã" = "Á"; "Ã‰" = "É"; "Ã" = "Í"
    "Ã" = "Ó"; "Ãš" = "Ú"; "Ã‘" = "Ñ"
}

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content
    
    foreach ($key in $correctionMap.Keys) {
        $content = $content -replace $key, $correctionMap[$key]
    }
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8
        Write-Host "? Corregido: $($file.Name)" -ForegroundColor Green
    }
}
