#!/bin/bash
chown -R www-data:www-data /var/www/app/repositories

sleep 10

# async worker for queueing service !!! ONLY FOR DEV !!!
php bin/console messenger:consume async &

exec "$@"