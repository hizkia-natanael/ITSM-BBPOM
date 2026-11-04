@echo off
title Buka Akses Firewall Helpdesk TIK BBPOM (Port 8090)
echo ========================================================
echo   MEMBUKA AKSES FIREWALL WINDOWS SERVER (PORT 8090)
echo ========================================================
netsh advfirewall firewall delete rule name="Helpdesk BBPOM 8090" >nul 2>&1
netsh advfirewall firewall delete rule name="Helpdesk BBPOM 8085" >nul 2>&1
netsh advfirewall firewall add rule name="Helpdesk BBPOM 8090" dir=in action=allow protocol=TCP localport=8090 profile=any
echo.
echo [BERHASIL] Port 8090 di PC Server sudah dibuka!
echo Sekarang aplikasi bisa diakses dari Laptop/HP lain di:
echo http://192.168.1.254:8090
echo ========================================================
pause
