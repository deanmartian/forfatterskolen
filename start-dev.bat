@echo off
REM Forfatterskolen Local Development Startup Script
REM ================================================

SET PHP_DIR=C:\Users\sveni\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe
SET MYSQL_DIR=C:\Program Files\MySQL\MySQL Server 8.4\bin
SET PATH=%PHP_DIR%;%MYSQL_DIR%;%PATH%

cd /d "%~dp0"

echo [1/3] Starting MySQL server...
start /B "" "%MYSQL_DIR%\mysqld.exe" --datadir="C:\Users\sveni\mysql-data" --port=3306 --console
timeout /t 3 /nobreak > nul

echo [2/3] Starting Laravel dev server on http://127.0.0.1:8000 ...
echo.
echo ================================================
echo   Forfatterskolen is running at:
echo   http://127.0.0.1:8000
echo.
echo   NOTE: Routes use domain-based routing.
echo   Add to C:\Windows\System32\drivers\etc\hosts:
echo     127.0.0.1 forfatterskolen.local
echo     127.0.0.1 admin.forfatterskolen.local
echo     127.0.0.1 editor.forfatterskolen.local
echo ================================================
echo.

php artisan serve --host=127.0.0.1 --port=8000
