@echo off
echo ========================================
echo Saving and Uploading changes to GitHub...
echo ========================================

:: Add all changes
git add .

:: Commit with the current date and time
git commit -m "Auto-backup on %date% at %time%"

:: Push to main branch
git push origin main

echo ========================================
echo Done! All changes are uploaded.
echo ========================================
pause
