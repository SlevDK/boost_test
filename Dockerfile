FROM php:7.4-fpm

# Set working directory
WORKDIR /var/www

RUN apt update && apt install -y \
    wget \
    zip \
    unzip \
    git \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    libjpeg-dev \
    && docker-php-ext-install pdo_pgsql \
    bcmath \
    pdo_mysql \
    opcache \
    && apt clean && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

RUN composer install \
    --no-ansi \
    --no-interaction \
    --no-autoloader \
    --no-dev

# Copy existing application directory contents
COPY . /var/www

# Fix storage and bootstrap folders rights
RUN chown -R www-data:www-data storage/ bootstrap/

# Copy repo php configurator to php additional config path
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
