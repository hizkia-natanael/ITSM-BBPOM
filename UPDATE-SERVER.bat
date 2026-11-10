@echo off
title Update Server Helpdesk TIK BBPOM
cd /d "%~dp0"

echo ========================================================
echo   1. MENARIK UPDATE TERBARU DARI GITHUB
echo ========================================================
git pull origin master 2>nul || git pull origin main 2>nul || git pull

echo.
echo ========================================================
echo   2. MEMPERBARUI CONTAINER DOCKER HELPDESK TIK
echo ========================================================
docker compose up -d --build

echo.
echo ========================================================
echo   3. MENJALANKAN MIGRASI DATABASE & SEEDER
echo ========================================================
echo Menjalankan migrasi database...
docker exec bbpom_helpdesk_app php artisan migrate --force
if %ERRORLEVEL% neq 0 (
    echo [INFO] Docker container tidak terdeteksi, mencoba migrasi langsung via PHP lokal...
    call "%~dp0artisan.bat" migrate --force
)
echo Menjalankan seeder akun penting...
docker exec bbpom_helpdesk_app php artisan db:seed --class=UserSeeder --force 2>nul || call "%~dp0artisan.bat" db:seed --class=UserSeeder --force

echo.
echo ========================================================
echo   4. MEMBERSIHKAN SELURUH CACHE APLIKASI & SERVER
echo ========================================================
echo Membersihkan cache bootstrap, view, route, config, & application data...
docker exec bbpom_helpdesk_app php artisan optimize:clear
docker exec bbpom_helpdesk_app php artisan cache:clear
if %ERRORLEVEL% neq 0 (
    echo [INFO] Docker container tidak terdeteksi, membersihkan cache via PHP lokal...
    call "%~dp0artisan.bat" optimize:clear
    call "%~dp0artisan.bat" cache:clear
)

echo.
echo ========================================================
echo [SELESAI] Server dan Database berhasil diperbarui!
echo   - IP Lokal : http://192.168.1.254:8085
echo ========================================================
pause
