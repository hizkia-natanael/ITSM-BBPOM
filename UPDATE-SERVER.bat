@echo off
title Update Otomatis Server Helpdesk TIK BBPOM
cd /d "%~dp0"
echo ========================================================
echo   MENARIK UPDATE TERBARU DARI GITHUB & BUILD DOCKER
echo ========================================================
git pull origin master
docker compose up -d --build
echo.
echo [SELESAI] Server Helpdesk TIK BBPOM berhasil diperbarui!
echo ========================================================
pause
