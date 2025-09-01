@echo off
title Serveur PHP - Enquetes Satisfaction
color 0A

echo.
echo ==========================================
echo   SERVEUR PHP - ENQUETES SATISFACTION
echo ==========================================
echo   Port: 8088
echo   URL:  http://localhost:8088
echo ==========================================
echo.
echo URLs utiles:
echo   - Page d'accueil: http://localhost:8088/
echo   - Admin:          http://localhost:8088/?controller=Admin&action=dashboard
echo   - Connexion:      http://localhost:8088/?controller=Auth&action=login
echo.
echo Identifiants:
echo   Admin: admin@enquetes.com / admin123
echo   Client: marie.martin@email.com / password123
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.

cd /d "C:\Users\ASUS TUF\enquetes-satisfaction\public"
php -S localhost:8088

echo.
echo Serveur arrete.
pause
echo Le serveur s'est arrete.
pause
