#!/bin/bash
chown -R www-data:www-data /var/www/app/repositories

exec "$@"