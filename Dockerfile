# 1. Base Image
FROM php:8.3-apache

# 2. Install dependensi OS
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

# 3. Install Ekstensi PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mbstring pdo_mysql mysqli zip gd

# 4. Aktifkan mod_rewrite (wajib CI4)
RUN a2enmod rewrite

# 5. Set DocumentRoot ke /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Mengizinkan .htaccess bekerja (penting untuk CodeIgniter)
RUN sed -i '/<Directory \/var\/www\/html\/public\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 6. Set direktori kerja
WORKDIR /var/www/html

# 7. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader

# 8. Writable Permissions (Wajib CI4)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# 9. EXPOSE Port 80 (Railway otomatis mendeteksi ini)
EXPOSE 80

# 10. WRAPPER SCRIPT UNTUK RUNTIME DIAGNOSTIC & START
# Script ini dieksekusi SAAT CONTAINER START, bukan saat build.
RUN echo '#!/bin/bash\n\
echo "=================================================="\n\
echo "1. CEK MPM YANG AKTIF (APACHE2CTL)"\n\
apache2ctl -M | grep mpm || true\n\
echo "--------------------------------------------------"\n\
echo "2. CEK SYMLINK MPM DI MODS-ENABLED"\n\
ls -la /etc/apache2/mods-enabled/ | grep mpm || true\n\
echo "--------------------------------------------------"\n\
echo "3. CEK FILE LOADMODULE MPM"\n\
grep -R "LoadModule.*mpm" /etc/apache2/ || true\n\
echo "=================================================="\n\
echo "MEMULAI APACHE..."\n\
exec apache2-foreground\n\
' > /usr/local/bin/start-apache.sh && chmod +x /usr/local/bin/start-apache.sh

# 11. CMD
CMD ["/usr/local/bin/start-apache.sh"]
