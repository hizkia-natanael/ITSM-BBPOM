@echo off
title Buka Akses Firewall Helpdesk TIK BBPOM (Port 8085)
echo ========================================================
echo   MEMBUKA AKSES FIREWALL WINDOWS SERVER (PORT 8085)
echo ========================================================
netsh advfirewall firewall delete rule name="Helpdesk BBPOM 8085" >nul 2>&1
netsh advfirewall firewall add rule name="Helpdesk BBPOM 8085" dir=in action=allow protocol=TCP localport=8085 profile=any
echo.
echo [BERHASIL] Port 8085 di PC Server sudah dibuka!
echo Sekarang aplikasi bisa diakses dari Laptop/HP lain di:
echo http://192.168.1.254:8085
echo ========================================================
pause
