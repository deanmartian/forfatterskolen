# Forfatterskolen Local Development Startup Script (PowerShell)
# =============================================================

$PHP_DIR = "C:\Users\sveni\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe"
$MYSQL_DIR = "C:\Program Files\MySQL\MySQL Server 8.4\bin"

$env:PATH = "$PHP_DIR;$MYSQL_DIR;$env:PATH"

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  Forfatterskolen Local Development" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Start MySQL in background
Write-Host "[1/2] Starting MySQL server..." -ForegroundColor Yellow
Start-Process -FilePath "$MYSQL_DIR\mysqld.exe" -ArgumentList "--datadir=C:\Users\sveni\mysql-data","--port=3306","--console" -WindowStyle Minimized
Start-Sleep -Seconds 3

Write-Host "[2/2] Starting Laravel dev server..." -ForegroundColor Yellow
Write-Host ""
Write-Host "  App URL: http://forfatterskolen.local:8000" -ForegroundColor Green
Write-Host ""
Write-Host "  Make sure your hosts file contains:" -ForegroundColor DarkGray
Write-Host "    127.0.0.1 forfatterskolen.local" -ForegroundColor DarkGray
Write-Host "    127.0.0.1 admin.forfatterskolen.local" -ForegroundColor DarkGray
Write-Host "    127.0.0.1 editor.forfatterskolen.local" -ForegroundColor DarkGray
Write-Host ""

Set-Location "C:\forfatterskolen-ny13.03\forfatterskolen-master"
& "$PHP_DIR\php.exe" artisan serve --host=127.0.0.1 --port=8000
