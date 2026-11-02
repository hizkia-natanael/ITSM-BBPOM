@echo off
setlocal
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
"%PHP_BIN%" "%~dp0artisan" %*
endlocal
