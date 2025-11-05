# Use an official PHP image as a base
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && docker-php-ext-enable pdo_mysql pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . /var/www

# Create entrypoint script
RUN echo '#!/bin/bash\n\
    # Set proper permissions for storage and bootstrap/cache\n\
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache\n\
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache\n\
    \n\
    # Create storage subdirectories if they dont exist\n\
    mkdir -p /var/www/storage/logs\n\
    mkdir -p /var/www/storage/framework/cache\n\
    mkdir -p /var/www/storage/framework/sessions\n\
    mkdir -p /var/www/storage/framework/views\n\
    \n\
    # Set permissions for storage subdirectories\n\
    chown -R www-data:www-data /var/www/storage\n\
    chmod -R 775 /var/www/storage\n\
    \n\
    # Install/update composer dependencies if needed\n\
    if [ ! -d "/var/www/vendor" ] || [ "/var/www/composer.json" -nt "/var/www/vendor" ]; then\n\
    composer install --no-dev --optimize-autoloader\n\
    fi\n\
    \n\
    # Start PHP-FPM\n\
    exec php-fpm' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
