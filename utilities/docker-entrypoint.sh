#!/usr/bin/env bash

# Executing migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Generate secret key
php bin/console secrets:generate-keys

# Copy PHP extensions configurations to container
cp -a php_extensions/. /usr/local/etc/php/conf.d/
exec "$@"