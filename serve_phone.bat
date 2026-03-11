@echo off
chcp 65001 >nul
echo ========================================
echo   KILYSAGRI - Serveur pour Telephone
echo ========================================
echo.

REM Afficher l'adresse IP de l'ordinateur
echo Determination de votre adresse IP...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do set IP=%%a
set IP=%IP: =%

if "%IP%"=="" (
    echo ERREUR: Impossible de trouver l'adresse IP
    echo Verifiez votre connexion reseau
    pause
    exit /b 1
)

echo.
echo ========================================
echo   INSTRUCTIONS
echo ========================================
echo 1. Connectez votre telephone au MEME WIFI que cet ordinateur
echo 2. Sur votre telephone, ouvrez:  http://%IP%:8000
echo 3. L'application devrait s'afficher
echo.
echo ========================================
echo   Demarrage du serveur...
echo ========================================
echo.

REM Demarrer le serveur sur toutes les interfaces (0.0.0.0)
cd /d c:\Users\HP\KilysAgri
php -S 0.0.0.0:8000 -t public

pause

