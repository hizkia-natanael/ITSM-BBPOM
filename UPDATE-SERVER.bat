@echo off
title Update Server Helpdesk TIK BBPOM
cd /d "%~dp0"

:MENU
cls
echo ========================================================
echo        PILIHAN PEMBARUAN SERVER HELPDESK TIK BBPOM
echo ========================================================
echo.
echo   [1] Update Lengkap (Git Pull + Rebuild + Database + Cache)
echo   [2] Update Saja (Git Pull + Rebuild + Cache, Tanpa Database)
echo   [3] Update Database Saja (Migrasi dan Seeder)
echo   [4] Bersihkan Cache dan Optimize Saja
echo   [5] Batal / Keluar
echo.
echo ========================================================
choice /C 12345 /N /M "Pilih nomor opsi [1-5]: "

if errorlevel 5 goto KELUAR
if errorlevel 4 goto OPSI_CACHE_SAJA
if errorlevel 3 goto OPSI_DB_SAJA
if errorlevel 2 goto OPSI_UPDATE_SAJA
if errorlevel 1 goto OPSI_LENGKAP

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

:DO_PULL
echo.
echo ========================================================
echo   1. MENARIK UPDATE TERBARU DARI GITHUB
echo ========================================================
git pull origin master 2>nul || git pull origin main 2>nul || git pull
goto :eof

:DO_CONTAINER
echo.
echo ========================================================
echo   2. MEMPERBARUI CONTAINER DOCKER HELPDESK TIK
echo ========================================================
docker compose up -d --build
goto :eof

:DO_DATABASE
echo.
echo ========================================================
echo   3. MENJALANKAN MIGRASI DATABASE DAN SEEDER
echo ========================================================
echo Menjalankan migrasi database...
docker exec bbpom_helpdesk_app php artisan migrate --force 2>nul
if %ERRORLEVEL% neq 0 (
    echo [INFO] Docker container tidak terdeteksi, mencoba migrasi langsung via PHP lokal...
    call "%~dp0artisan.bat" migrate --force
)
echo Menjalankan seeder akun penting...
docker exec bbpom_helpdesk_app php artisan db:seed --class=UserSeeder --force 2>nul || call "%~dp0artisan.bat" db:seed --class=UserSeeder --force
goto :eof

:DO_OPTIMIZE
echo.
echo ========================================================
echo   4. MEMBERSIHKAN SELURUH CACHE APLIKASI DAN OPTIMIZE
echo ========================================================
echo Membersihkan cache bootstrap, view, route, config, dan application data...
docker exec bbpom_helpdesk_app php artisan optimize:clear 2>nul
docker exec bbpom_helpdesk_app php artisan cache:clear 2>nul
docker exec bbpom_helpdesk_app php artisan config:cache 2>nul
if %ERRORLEVEL% neq 0 (
    echo [INFO] Docker container tidak terdeteksi, membersihkan cache via PHP lokal...
    call "%~dp0artisan.bat" optimize:clear
    call "%~dp0artisan.bat" cache:clear
    call "%~dp0artisan.bat" config:cache
)
goto :eof

:SELESAI
echo.
echo ========================================================
echo [SELESAI] Proses berhasil dijalankan!
echo   - IP Akses : http://192.168.1.254:8085
echo ========================================================
pause
exit /b 0

:KELUAR
echo Operasi dibatalkan.
exit /b 0
