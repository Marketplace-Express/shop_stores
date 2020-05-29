#!/usr/bin/env bash

source ./utilities/progressbar.sh || exit 1

echo "Configuring pdo_mysql and gd extensions ..."
docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd > /dev/null
docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
                                --with-png-dir=/usr/include/ \
                                --with-jpeg-dir=/usr/include/ > /dev/null
echo "Done configuring!"

echo "Installing php extensions ..."

i=0
draw_progress_bar $i 8 "extensions"
for ext in intl gettext gd bcmath zip pdo_mysql opcache sockets; do
  docker-php-ext-install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i 8 "extensions"
done
echo

echo "Installing PECL extensions ..."
i=0
draw_progress_bar $i 3 "extensions"
for ext in redis mongodb xdebug; do
  echo '' | pecl install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i 3 "extensions"
done
echo