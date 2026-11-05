@echo off
setlocal
cd /d "%~dp0"
call "%~dp0serve.bat" %*
endlocal
