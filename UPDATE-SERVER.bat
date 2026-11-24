@echo off
title Update Server Helpdesk TIK BBPOM (ELITE)
cd /d "%~dp0"

:MENU
cls
echo ========================================================
echo      PILIHAN PEMBARUAN SERVER HELPDESK TIK (ELITE)
echo ========================================================
echo   [1] Update Lengkap (Pull + Build + Migrasi + Cache + Worker)
echo   [2] Update Aplikasi Saja (Pull + Build + Cache + Worker)
echo   [3] Update Database Saja (Migrasi dan UserSeeder)
echo   [4] Bersihkan Cache, Optimize & Restart Worker
echo   [5] Restart Semua Container (App, Webserver, Queue, Scheduler)
echo   [6] Status Container Docker (docker compose ps)
echo   [0] Keluar
echo ========================================================
set "pilihan="
set /p pilihan="Pilih nomor opsi [1-6 / 0]: "

if "%pilihan%"=="1" goto OPSI_LENGKAP
if "%pilihan%"=="2" goto OPSI_UPDATE_SAJA
if "%pilihan%"=="3" goto OPSI_DB_SAJA
if "%pilihan%"=="4" goto OPSI_CACHE_SAJA
if "%pilihan%"=="5" goto OPSI_RESTART
if "%pilihan%"=="6" goto OPSI_STATUS
if "%pilihan%"=="0" goto KELUAR
goto MENU

:OPSI_LENGKAP
call :DO_PULL
call :DO_CONTAINER
call :DO_DATABASE
call :DO_OPTIMIZE
goto SELESAI

:OPSI_UPDATE_SAJA
call :DO_PULL
call :DO_CONTAINER
call :DO_OPTIMIZE
goto SELESAI

:OPSI_DB_SAJA
call :DO_DATABASE
call :DO_OPTIMIZE
goto SELESAI

:OPSI_CACHE_SAJA
call :DO_OPTIMIZE
goto SELESAI

:OPSI_RESTART
echo.
echo [RESTART] Merestart app, webserver, queue, scheduler...
docker compose restart
goto SELESAI

:OPSI_STATUS
echo.
echo [STATUS] Daftar container aktif:
docker compose ps
goto SELESAI

:DO_PULL
echo.
echo [1] Menarik update terbaru dari GitHub...
git pull
goto :eof

:DO_CONTAINER
echo.
echo [2] Build image app dan recreate container (app, webserver, queue, scheduler)...
docker compose build app
docker compose up -d --no-build
goto :eof

:DO_DATABASE
echo.
echo [3] Menjalankan migrasi database dan UserSeeder...
docker compose exec -T app php artisan migrate --force 2>nul
if %ERRORLEVEL% neq 0 (
    echo [INFO] Docker container tidak terdeteksi, mencoba via PHP lokal...
    call "%~dp0artisan.bat" migrate --force
)
docker compose exec -T app php artisan db:seed --class=UserSeeder --force 2>nul || call "%~dp0artisan.bat" db:seed --class=UserSeeder --force
goto :eof

:DO_OPTIMIZE
echo.
echo [4] Optimize cache dan restart worker (queue + scheduler)...
docker compose exec -T app php artisan optimize:clear 2>nul
if %ERRORLEVEL% equ 0 (
    docker compose exec -T app php artisan optimize
    docker compose restart queue scheduler
) else (
    echo [INFO] Docker container tidak terdeteksi, membersihkan cache via PHP lokal...
    call "%~dp0artisan.bat" optimize:clear
    call "%~dp0artisan.bat" optimize
)
goto :eof

:SELESAI
echo.
echo ========================================================
echo [SELESAI] Proses berhasil! Akses: http://192.168.1.254:8085
echo ========================================================
pause
goto MENU

:KELUAR
echo Operasi dibatalkan.
exit /b 0
