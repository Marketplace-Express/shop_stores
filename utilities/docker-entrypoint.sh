#!/usr/bin/env bash

# Executing migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Copy PHP extensions configurations to container
cp -a php_extensions/. /usr/local/etc/php/conf.d/
exec "$@"