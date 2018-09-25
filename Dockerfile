FROM php:8.3.28-apache

# Arguments defined in docker-compose.yml
ARG user=pexinho
ARG uid=1000

# Install system dependencies
RUN apt-get update && \
  apt-get install -y locales locales-all \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  libzip-dev \
  zip \
  unzip \
  vim

  # Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ENV LC_ALL en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath sockets opcache gd zip && a2enmod rewrite

# Set working directory
WORKDIR /var/www/html
COPY composer.* Docker.sh ./

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"
ADD opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

EXPOSE 80
ENTRYPOINT ["bash", "Docker.sh"]
