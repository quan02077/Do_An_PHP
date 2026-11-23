@echo off
title Cloudflare Tunnel (HTTPS)
echo ========================================================
echo DANG KHOI DONG CLOUDFLARE TUNNEL CHO CONG 8000...
echo Cho vai giay de he thong tao duong link HTTPS...
echo ========================================================
echo.
node "C:\Users\Minh Quan\AppData\Local\npm-cache\_npx\ffda48baa5669fdf\node_modules\untun\dist\cli.mjs" tunnel --port 8000
pause
