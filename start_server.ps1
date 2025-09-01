# Script PowerShell pour maintenir le serveur PHP en vie
param(
    [int]$Port = 8086
)

$ProjectPath = "C:\Users\ASUS TUF\enquetes-satisfaction\public"

Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "  SERVEUR PHP - ENQUETES SATISFACTION" -ForegroundColor Yellow
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "Port: $Port" -ForegroundColor Green
Write-Host "Dossier: $ProjectPath" -ForegroundColor Green
Write-Host "URL: http://localhost:$Port" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Appuyez sur Ctrl+C pour arreter le serveur" -ForegroundColor Red
Write-Host ""

Set-Location $ProjectPath

$restartCount = 0

while ($true) {
    try {
        if ($restartCount -gt 0) {
            Write-Host "Redemarrage du serveur (#$restartCount)..." -ForegroundColor Yellow
        } else {
            Write-Host "Demarrage du serveur..." -ForegroundColor Green
        }
        
        # Démarrer le serveur PHP
        php -S localhost:$Port
        
        # Si on arrive ici, le serveur s'est arrêté
        $restartCount++
        Write-Host "Le serveur s'est arrete. Redemarrage dans 2 secondes..." -ForegroundColor Red
        Start-Sleep -Seconds 2
        
    } catch {
        Write-Host "Erreur: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "Tentative de redemarrage dans 5 secondes..." -ForegroundColor Yellow
        Start-Sleep -Seconds 5
        $restartCount++
    }
}
