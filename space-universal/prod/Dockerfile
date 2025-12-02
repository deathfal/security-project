# Use the official PHP image with Apache
FROM php:8.3-apache AS symfony_php

# Set the working directory
WORKDIR /var/www/space-universal

# Install necessary system packages
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    git \
    zip \
    unzip \
    curl \
    gnupg \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_pgsql

# Enable Apache mod_rewrite for Symfony routing
RUN a2enmod rewrite

# Install Node.js & npm (LTS version)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# Copy composer files first
COPY composer.* symfony.* ./

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies using Composer
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# Copy application source
COPY . /var/www/space-universal

RUN rm -Rf frankenphp/

RUN chown -R www-data:www-data /var/www/space-universal \
    && chmod -R 755 /var/www/space-universal
    
# Ensure var directory exists and set permissions
RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer dump-env prod; \
    composer run-script --no-dev post-install-cmd; \
    chmod +x bin/console; sync

# Install npm dependencies and build assets
RUN npm install && npm run build

# Update DocumentRoot
RUN sed -ri -e 's!/var/www/html!/var/www/space-universal/public!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/conf-available/docker-php.conf

# Insert or replace the <Directory> block
RUN sed -i '/DocumentRoot/a <Directory /var/www/space-universal/public>\nAllowOverride All\nRequire all granted\n</Directory>' \
    /etc/apache2/sites-available/000-default.conf


RUN echo "ServerName cosmic-bazaar.sector18.quest" >> /etc/apache2/apache2.conf

# Create fixtures stage
FROM symfony_php AS fixtures

# Install dev dependencies including fixtures
RUN composer install --prefer-dist --no-scripts \
    && composer require --dev doctrine/doctrine-fixtures-bundle

# Expose port 80
EXPOSE 80

# Start Apache and automatically recompile assets if needed
CMD ["sh", "-c", "npm run build && apache2-foreground"]
