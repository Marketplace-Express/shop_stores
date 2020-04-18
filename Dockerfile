#
# Shop vendors service Dockerfile
# Copyrights to Wajdi Jurry <github.com/wajdijurry>
#
FROM php:7.3-fpm

LABEL maintainer="Wajdi Jurry <github.com/wajdijurry>"

# Update apt repos
RUN set -xe && apt-get -y update

# Install required tools and dependencies
RUN apt-get install -y wget libfreetype6-dev libpng-dev libjpeg-dev libcurl4-gnutls-dev libyaml-dev libicu-dev libzip-dev unzip git

# Install required PHP extensions
RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
                                --with-png-dir=/usr/include/ \
                                --with-jpeg-dir=/usr/include/

# Install extra extensions
RUN docker-php-ext-install intl gettext gd bcmath zip pdo_mysql opcache && \
    echo '' | pecl install redis mongodb xdebug

# Download Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony && \
    addgroup --force-badname _www; \
    adduser --no-create-home --force-badname --disabled-login --disabled-password --system _www; \
    addgroup _www _www && \
    symfony check:requirements

# Return working directory to its default state
WORKDIR /src

# Copy project files to container
ADD . ./

# Install composer & dependencies
RUN rm -rf vendor composer.lock && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer clearcache && \
    composer config -g github-oauth.github.com 3f6fd65b0d7958581f549b862ee49af9db1bcdf1 && \
    composer install

# Run migrations
RUN php bin/console doctrine:migrations:migrate --no-interaction

ENTRYPOINT ["/bin/bash", "utilities/docker-entrypoint.sh"]