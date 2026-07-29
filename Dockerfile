FROM php:8.3-apache

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

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mbstring pdo_mysql mysqli zip gd

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Perbaiki path DocumentRoot agar menunjuk ke folder public CodeIgniter
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# Izinkan .htaccess bekerja
RUN sed -i '/<Directory \/var\/www\/html\/public\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80

# WRAPPER DIAGNOSTIK RUNTIME (Dieksekusi saat container Railway mulai berjalan)
RUN echo '#!/bin/bash\n\
echo "=================================================="\n\
echo "=== 1. DIAGNOSTIK RUNTIME: APACHE2CTL -t (SYNTAX CHECK) ==="\n\
apache2ctl -t || true\n\
echo "=================================================="\n\
echo "=== 2. DIAGNOSTIK RUNTIME: APACHE2CTL -M (LOADED MODULES) ==="\n\
apache2ctl -M | grep mpm || true\n\
echo "=================================================="\n\
echo "=== 3. DIAGNOSTIK RUNTIME: APACHE2CTL -S (VIRTUAL HOSTS) ==="\n\
apache2ctl -S || true\n\
echo "=================================================="\n\
echo "=== 4. DIAGNOSTIK RUNTIME: ISI MODS-ENABLED ==="\n\
ls -la /etc/apache2/mods-enabled/ | grep mpm || true\n\
echo "=================================================="\n\
echo "=== 5. DIAGNOSTIK RUNTIME: SEMUA LOADMODULE DI APACHE ==="\n\
grep -R "LoadModule" /etc/apache2/ | grep mpm || true\n\
echo "=================================================="\n\
echo "=== 6. DIAGNOSTIK RUNTIME: SEMUA KONFIGURASI MPM ==="\n\
grep -Ri "mpm_" /etc/apache2/ || true\n\
echo "=================================================="\n\
echo "=== MEMULAI APACHE ==="\n\
exec apache2-foreground\n\
' > /usr/local/bin/start-apache.sh && chmod +x /usr/local/bin/start-apache.sh

CMD ["/usr/local/bin/start-apache.sh"]
