# Menggunakan image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP yang dibutuhkan CodeIgniter 4
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mbstring pdo_mysql mysqli zip gd

# Mengaktifkan mod_rewrite Apache (wajib untuk CodeIgniter)
RUN a2enmod rewrite

# Mengubah DocumentRoot Apache ke folder public CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy file project ke dalam container
COPY . .

# Install dependencies project (tanpa dev untuk production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Mengatur permission untuk folder writable
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# Expose port 80
EXPOSE 80
