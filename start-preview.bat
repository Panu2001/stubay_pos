@echo off
cd /d "%~dp0"
set "PHPRC=%~dp0preview-php.ini"
"C:\Program Files\php-8.5.8\php.exe" -c "%~dp0preview-php.ini" artisan serve --host=127.0.0.1 --port=8000
