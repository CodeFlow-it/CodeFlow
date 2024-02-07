#!/bin/bash
chown -R www-data:www-data /var/www/app/repositories
chown -R www-data:www-data /var/www/app/reports

if [ "$APP_ENV" == "dev" ]; then
    php bin/console messenger:consume async &
fi

exec "$@"