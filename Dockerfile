FROM php:8.3-apache

# 1. Install system dependencies
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

# 2. Install PHP extensions untuk CI4
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mbstring pdo_mysql mysqli zip gd

# 3. Enable Apache rewrite
RUN a2enmod rewrite

# 4. Set CodeIgniter DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Working Directory & Composer
WORKDIR /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 6. Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80

# 7. RUNTIME DIAGNOSTIC & AUTO REPAIR WRAPPER
RUN printf '#!/bin/bash\n\
echo "=========================================="\n\
echo "RUNNING CUSTOM START SCRIPT"\n\
echo "=========================================="\n\
\n\
echo "--- 1. BUKTI RUNTIME: SYMLINK MPM ---"\n\
find /etc/apache2 -type l | grep mpm || true\n\
\n\
echo "--- 2. BUKTI RUNTIME: ISI FILE MPM (.load & .conf) ---"\n\
for file in /etc/apache2/mods-enabled/mpm*.load /etc/apache2/mods-enabled/mpm*.conf; do\n\
    if [ -e "$file" ] || [ -L "$file" ]; then\n\
        echo "File: $file"\n\
        cat "$file"\n\
        echo "----------------------------------------"\n\
    fi\n\
done\n\
\n\
echo "--- 3. MELAKUKAN AUTO REPAIR MPM ---"\n\
a2dismod mpm_event || true\n\
a2dismod mpm_worker || true\n\
rm -f /etc/apache2/mods-enabled/mpm_event.*\n\
rm -f /etc/apache2/mods-enabled/mpm_worker.*\n\
a2enmod mpm_prefork\n\
\n\
echo "--- 4. VALIDASI SETELAH REPAIR (apache2ctl -M) ---"\n\
apache2ctl -M 2>/dev/null | grep mpm || true\n\
\n\
echo "=========================================="\n\
echo "STARTING APACHE-FOREGROUND"\n\
echo "=========================================="\n\
exec apache2-foreground\n' > /usr/local/bin/start-apache.sh \
&& chmod +x /usr/local/bin/start-apache.sh

CMD ["/usr/local/bin/start-apache.sh"]
