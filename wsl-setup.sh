#!/bin/bash
set -e

echo "=== STEP 1: apt update ==="
sudo apt-get update -y

echo "=== STEP 2: Install PHP 8.3 + extensions ==="
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y \
    php8.3-cli \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-zip \
    php8.3-gd \
    php8.3-bcmath \
    php8.3-intl \
    php8.3-curl \
    php8.3-readline \
    unzip \
    curl

echo "=== STEP 3: Install Composer ==="
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

echo "=== STEP 4: Install MySQL 8 ==="
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server

echo "=== STEP 5: Start MySQL ==="
sudo service mysql start

echo "=== STEP 6: Create database ==="
sudo mysql -e "CREATE DATABASE IF NOT EXISTS forfatterskolen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "=== STEP 7: Verify ==="
php -v
composer --version
mysql --version

echo "=== SETUP COMPLETE ==="
