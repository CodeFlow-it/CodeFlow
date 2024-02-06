#!/bin/bash
chown -R www-data:www-data /var/www/app/repositories

if [ "$APP_ENV" == "dev" ]; then
    php bin/console messenger:consume async &
fi

exec "$@"