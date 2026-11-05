@echo off
setlocal
cd /d "%~dp0"

where php >nul 2>&1
if %ERRORLEVEL% equ 0 (
    set "PHP_BIN=php"
) else if exist "C:\laragon\bin\php\php-8.4.26-Win32-vs17-x64\php.exe" (
    set "PHP_BIN=C:\laragon\bin\php\php-8.4.26-Win32-vs17-x64\php.exe"
) else if exist "C:\tools\php84\php.exe" (
    set "PHP_BIN=C:\tools\php84\php.exe"
) else (
    set "PHP_BIN=php"
)

echo [1/2] Menjalankan migrasi database...
"%PHP_BIN%" "%~dp0artisan" migrate --force

echo.
echo [2/2] Memulai server aplikasi Helpdesk BBPOM...
"%PHP_BIN%" "%~dp0artisan" serve %*
endlocal
