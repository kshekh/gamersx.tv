################################################
# Stage 1 [Composer] - Install PHP Dependencies
################################################

# Build php depenedencies from a composer stage so we can 
# drop all the composer stuff from the final container build

FROM composer as vendor

WORKDIR /app

COPY composer.json composer.lock /app/

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --quiet

##########################
# Worker Stage
##########################

# Redis cli install 
# gpg: command not found

# Build a container to be used for development scripts
FROM php:7.4-cli as worker

RUN apt-get -y update && apt-get -y upgrade

RUN apt-get install -y wget \
    curl \
    git \
    grep \
    # build-base \
    libmemcached-dev \
    libmcrypt-dev \
    libxml2-dev \
    # imagemagick-dev \
    # pcre-dev \
    libtool \
    make \
    autoconf \
    g++ \
    # cyrus-sasl-dev \
    libgsasl-dev \
    zip \
    unzip \
    zlib1g-dev \
    libzip-dev \
    supervisor \
    mariadb-client


WORKDIR /root

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN HASH=`curl -sS https://composer.github.io/installer.sig`
RUN php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer

RUN docker-php-ext-configure pcntl --enable-pcntl
RUN docker-php-ext-install pcntl

# RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip
RUN docker-php-ext-enable zip

RUN apt-get update \
  && docker-php-ext-install pdo_mysql mysqli

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli

# RUN apt-get update \
#   && apt-get install -y libmemcached-dev zlib1g-dev \
#   && pecl install memcached-3.0.3 \
#   && docker-php-ext-enable memcached opcache

WORKDIR /app

COPY . /app/

COPY --from=vendor --chown=root:root /app/vendor/. /var/www/html/vendor

# COPY --from=vendor /app/vendor/. /app/vendor

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

COPY --chown=root:root ./docker/worker/entrypoint /entrypoint
RUN chmod +x /entrypoint

# RUN chmod +x /app/artisan

ENTRYPOINT ["/entrypoint"]

###########################################
# Stage 2 [PHP CLI] - Build Sonata Bundles
###########################################

# - Build the static Sonata bundles with Symfony for the web server
# - Prepare the backend scripts for the upstream task.

FROM php:7.4-cli as backend

RUN docker-php-source extract \
    && docker-php-source delete

RUN mkdir /app

# Copy the site files
COPY . /app/

WORKDIR /app

# Copy php dependencies from Stage 1
COPY --from=vendor /app/vendor/. /app/vendor

# Build the bundles
RUN php bin/console assets:install

#############################################
# Stage 3 [PHP Fpm] - Serve the App Upstream
#############################################

# - Install all of the server software that PHP requires.
# - Add the web user and group.
# - Copy all of the PHP scripts from Stage 2.
# - Fix the permissions.
# - Serve PHP scripts upstream to nginx with Fpm. 

FROM php:7.4-fpm-alpine as app

# Install apt packages
RUN apk upgrade --update && apk add git\
  libmcrypt-dev \
  zip \
  libzip-dev \
  unzip

RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
        && pecl install apcu \
        && docker-php-ext-enable apcu \
        && pecl clear-cache \
        && apk del .build-dependencies

RUN docker-php-ext-install mysqli pdo_mysql

# Copy over the config files that we packaged in
COPY ./docker/php/php.ini /usr/local/etc/php/
COPY ./docker/php/php-fpm.conf /usr/local/etc/
COPY ./docker/php/entrypoint.sh /usr/local/bin/

# Add the user that nginx will be expecting
ARG UID
RUN addgroup --gid ${UID} nginx
RUN adduser --system --no-create-home -D --uid ${UID} --ingroup nginx ec2-user

WORKDIR /var/www/html

# Get latest Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# or
# RUN curl -s https://getcomposer.org/installer | php
# RUN alias composer='php composer.phar'

# Copy the site files 
COPY --from=backend --chown=ec2-user:nginx /app/. /var/www/html

RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip
RUN docker-php-ext-enable zip

# Run the commands for the app container
CMD ["/usr/local/bin/docker-php-entrypoint","php-fpm","-F"]


#######################################
# Stage 4 [Node] - Build the Front End
#######################################

# - Copy the assets folder from Stage 2.
# - Install the node modules.
# - Build the remaining static front end assets.

FROM node:lts-alpine as frontend
ARG user

WORKDIR /app
COPY package.json postcss.config.js tailwind.config.js webpack.config.js yarn.lock /app/

RUN mkdir -p /app/assets
COPY --from=backend /app/assets/. /app/assets

RUN yarn install && yarn build

###############################
# Stage 5 [Nginx] - Web Server
###############################

# - Copy all of the static files from Stages 2 and 4.
# - Create the same user that exists in Stage 3.
# - Copy the config files and fix the file permissions.
# - Serve the website with Nginx.

FROM nginx:alpine as web

ARG UPSTREAM
ARG UID

RUN apk add --upgrade brotli-libs

# Copy nginx config from files we packaged in
COPY ./docker/nginx/nginx.conf /etc/nginx/
COPY ./docker/nginx/default.conf /etc/nginx/conf.d
COPY ./docker/nginx/${UPSTREAM}.conf /etc/nginx/conf.d

# Add the user that php expects to the nginx group
RUN adduser --system --no-create-home -D --uid ${UID} --ingroup nginx ec2-user

# # Create the site directory
RUN mkdir -p /var/www/html/assets
RUN mkdir -p /var/www/html/public/build

# Copy just the files that we need and leave the surreal stage behind for the push
WORKDIR /var/www/html

# COPY --from=backend --chown=ec2-user:nginx /app/assets/. /var/www/html/assets
COPY --from=backend --chown=ec2-user:nginx /app/public/. /var/www/html/public
COPY --from=frontend --chown=ec2-user:nginx /app/public/build/. /var/www/html/public/build

