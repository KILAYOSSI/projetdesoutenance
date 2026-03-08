@echo off
git config user.email "contact@kilysagri.com"
git config user.name "KILAYOSSI"
git remote add origin https://github.com/KILAYOSSI/projetdesoutenance.git
git add .
git commit -m "Update project"
git branch -M main
git push -u origin main
pause

