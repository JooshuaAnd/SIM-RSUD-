# 1. Gunakan PHP 8.3 Apache sesuai dengan environment lokal Anda
FROM php:8.3-apache

# 2. Install dependencies sistem yang dibutuhkan untuk ekstensi PHP
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

# 3. Install ekstensi PHP yang dibutuhkan CodeIgniter 4
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mbstring pdo_mysql mysqli zip gd

# 4. PEMBERSIHAN MPM (Mencegah "More than one MPM loaded")
# Alih-alih a2dismod, kita hapus paksa file symlink mpm_ apapun yang terlanjur ter-copy/aktif,
# lalu mengaktifkan ulang HANYA prefork (yang diwajibkan oleh PHP).
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
    && rm -f /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork

# 5. DIAGNOSTIK BUILD (Hasilnya akan muncul di Railway Deploy Logs)
RUN echo "=== CEK MODUL MPM YANG AKTIF DI FOLDER ===" \
    && ls -la /etc/apache2/mods-enabled/ | grep mpm \
    && echo "=== CEK APACHE MODULE (DUMP) ===" \
    && apache2ctl -M || true

# 6. Aktifkan mod_rewrite Apache (wajib untuk routing CodeIgniter 4)
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 7. Ubah DocumentRoot Apache ke folder public CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 8. Set working directory
WORKDIR /var/www/html

# 9. Install Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 10. Copy seluruh file project ke dalam container
COPY . .

# 11. Install dependencies project (tanpa dev untuk production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 12. Mengatur kepemilikan dan permission agar CodeIgniter bisa menulis ke folder writable
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# 13. Definisikan default PORT. Railway menggunakan dinamis.
ENV PORT=8080
EXPOSE 8080

# 14. Masukkan PORT ke envvars Apache agar bisa dibaca di sites-available & ports.conf
RUN echo 'export PORT=${PORT:-8080}' >> /etc/apache2/envvars
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 15. CMD kembali ke default
CMD ["apache2-foreground"]
