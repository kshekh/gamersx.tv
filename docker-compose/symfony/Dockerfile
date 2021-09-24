FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install system dependencies
RUN apt-get update -y && apt-get install -y \
  git \
  libmcrypt-dev \
  zip \
  libzip-dev \
  unzip

RUN pecl install apcu \
  && docker-php-ext-enable apcu \
  && docker-php-ext-install zip \
  && pecl clear-cache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Symfony commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www
USER $user
