@echo off
cd /d C:\xampp\htdocs\IDMS-2025
echo === %DATE% %TIME% schedule:run ===>> storage\logs\schedule-reportin.log
"C:\xampp\php\php.exe" artisan schedule:run -v >> storage\logs\schedule-reportin.log 2>&1