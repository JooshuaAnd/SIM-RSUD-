FROM php:8.3-apache

# 1. System dependencies
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

# 2. PHP extensions untuk CI4
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mbstring pdo_mysql mysqli zip gd

# 3. Enable Apache rewrite
RUN a2enmod rewrite

# 4. Set CodeIgniter DocumentRoot (Tanpa menyentuh konfigurasi port atau MPM)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# 5. Working Directory & Composer
WORKDIR /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 6. Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80

# 7. EXHAUSTIVE RUNTIME DIAGNOSTIC WRAPPER
RUN printf '#!/bin/bash\n\
echo "=================================================="\n\
echo " INVESTIGASI ROOT CAUSE APACHE RUNTIME"\n\
echo "=================================================="\n\
\n\
echo "--- 1. SEMUA FILE KONFIGURASI APACHE ---"\n\
find /etc/apache2 -type f -name "*.conf" -print || true\n\
\n\
echo "--- 2. GREP SEMUA KEYWORD MPM ---"\n\
grep -Ri "mpm" /etc/apache2 || true\n\
\n\
echo "--- 3. GREP SEMUA LOADMODULE ---"\n\
grep -R "LoadModule" /etc/apache2 || true\n\
\n\
echo "--- 4. SYNTAX TEST (apache2ctl -t) ---"\n\
apache2ctl -t || true\n\
\n\
echo "--- 5. LOADED MODULES (apache2ctl -M) ---"\n\
apache2ctl -M 2>/dev/null | grep mpm || true\n\
\n\
echo "--- 6. VIRTUAL HOSTS (apache2ctl -S) ---"\n\
apache2ctl -S || true\n\
\n\
echo "=================================================="\n\
echo " STARTING APACHE-FOREGROUND"\n\
echo "=================================================="\n\
exec apache2-foreground\n' > /usr/local/bin/start-apache.sh \
&& chmod +x /usr/local/bin/start-apache.sh

# 8. Start Container
CMD ["/usr/local/bin/start-apache.sh"]
