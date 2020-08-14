#!/usr/bin/env bash

if [ ! -f .env ]; then
    cp .env.example .env
fi

# Executing migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Generate secret key
php bin/console regenerate-app-secret

# Copy PHP extensions configurations to container
cp -a php_extensions/. /usr/local/etc/php/conf.d/
exec "$@"