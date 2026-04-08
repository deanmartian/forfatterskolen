#!/usr/bin/env bash
#
# Forfatterskolen — produksjon deploy-script
# Kjøres fra /home/forfatter/public_html på serveren via cPanel-terminalen.
#
# Bruk:   bash deploy.sh
#
# Hva det gjør:
#   1. Henter siste kode fra master
#   2. Bygger composer-autoloaderen på nytt (slik at nye klasser blir kjent)
#   3. Kjører eventuelle nye databasemigrasjoner
#   4. Tømmer alle Laravel-cacher (route, view, config, application)
#
# Hvis noe feiler, stopper scriptet umiddelbart (set -e),
# slik at du ser feilen og kan spørre om hjelp uten at det går halvveis.

set -e
cd "$(dirname "$0")"

echo ""
echo "================================================================"
echo "  Forfatterskolen deploy"
echo "================================================================"
echo ""

echo "==> 1/5  Henter siste kode fra GitHub..."
git pull origin master
echo ""

echo "==> 2/5  Bygger composer-autoloader på nytt..."
php composer.phar dump-autoload --optimize
echo ""

echo "==> 3/5  Kjører databasemigrasjoner..."
php artisan migrate --force
echo ""

echo "==> 4/5  Tømmer Laravel-cacher..."
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
echo ""

echo "==> 5/5  Restarter eventuell queue-worker..."
php artisan queue:restart || true
echo ""

echo "================================================================"
echo "  Ferdig! Deployen var vellykket."
echo "================================================================"
