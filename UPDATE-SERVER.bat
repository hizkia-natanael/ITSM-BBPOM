@echo off
title Update Server Helpdesk TIK BBPOM
cd /d "%~dp0"

echo ========================================================
echo   1. MENARIK UPDATE TERBARU DARI GITHUB
echo ========================================================
git pull origin master

echo.
echo ========================================================
echo   2. MEMPERBARUI CONTAINER DOCKER HELPDESK TIK
echo ========================================================
docker compose up -d --build
docker exec bbpom_helpdesk_app php artisan migrate --force
docker exec bbpom_helpdesk_app php artisan db:seed --class=UserSeeder --force
docker exec bbpom_helpdesk_app php artisan optimize:clear

echo.
echo ========================================================
echo [SELESAI] Container Docker berhasil diperbarui!
echo   - IP Lokal : http://192.168.1.254:8085
echo ========================================================
pause
