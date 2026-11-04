@echo off
title Update & Aktifkan Domain Server Helpdesk TIK BBPOM
cd /d "%~dp0"
echo ========================================================
echo   1. MENARIK UPDATE TERBARU DARI GITHUB
echo ========================================================
git pull origin master

echo.
echo ========================================================
echo   2. MENGAKTIFKAN DOMAIN /ELITE DI APACHE & .ENV
echo ========================================================
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0docker\setup-domain.ps1"

echo.
echo ========================================================
echo   3. MEMPERBARUI CONTAINER DOCKER HELPDESK TIK
echo ========================================================
docker compose up -d --build
docker exec bbpom_helpdesk_app php artisan migrate --force
docker exec bbpom_helpdesk_app php artisan db:seed --class=UserSeeder --force
docker exec bbpom_helpdesk_app php artisan optimize:clear

echo.
echo ========================================================
echo [SELESAI] Aplikasi siap diakses dari jaringan mana saja:
echo   - Domain Publik : https://lawangsewu.bbpomsemarang.id/elite
echo   - IP Lokal      : http://192.168.1.254:8090
echo ========================================================
pause
