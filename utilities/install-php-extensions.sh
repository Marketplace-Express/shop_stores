#!/usr/bin/env bash

source ./utilities/progressbar.sh || exit 1


# Define array of php & pecl extensions
php_extensions=(intl gettext gd bcmath zip pdo_mysql opcache sockets)
pecl_extensions=(redis mongodb xdebug)


### START DO NOT EDIT AREA ###
echo "Configuring pdo_mysql and gd extensions ..."
docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd > /dev/null
docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
                                --with-png-dir=/usr/include/ \
                                --with-jpeg-dir=/usr/include/ > /dev/null
echo "Done configuring!"

echo "Installing php extensions ..."

php_extensions_count=${#php_extensions[@]}
i=0
draw_progress_bar $i ${php_extensions_count} "extensions"
for ext in ${php_extensions[*]}; do
  docker-php-ext-install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i ${php_extensions_count} "extensions"
done
echo

echo "Installing PECL extensions ..."
i=0
pecl_extensions_count=${#pecl_extensions[@]}
draw_progress_bar $i ${pecl_extensions_count} "extensions"
for ext in ${pecl_extensions[*]}; do
  echo '' | pecl install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i ${pecl_extensions_count} "extensions"
done
echo
### END OF DO NOT EDIT AREA ###