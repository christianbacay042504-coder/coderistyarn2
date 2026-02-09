@echo off
echo Importing SJDM Tours database...
mysql -u root -p sjdm_tours < sjdm_tours.sql
echo.
echo Database import completed!
pause
