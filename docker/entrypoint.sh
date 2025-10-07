#!/bin/sh

# Nettoyer et optimiser Laravel au démarrage
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Lancer supervisord pour nginx et php-fpm
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
