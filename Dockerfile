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

# 4. DIAGNOSTIK AWAL: Lihat kondisi mpm bawaan sebelum kita apa-apakan
RUN echo "=== DIAGNOSTIK AWAL: MPM YANG TERSEDIA ===" \
    && ls -la /etc/apache2/mods-available/ | grep mpm \
    && echo "=== DIAGNOSTIK AWAL: MPM YANG AKTIF ===" \
    && ls -la /etc/apache2/mods-enabled/ | grep mpm || true

# 5. PEMBERSIHAN MPM SECARA TOTAL DAN BRUTAL
# Kita matikan paksa ketiganya menggunakan a2dismod -f (force)
# Lalu kita hapus bersih semua file konfigurasi MPM di mods-enabled
# Barulah kita nyalakan ulang HANYA prefork.
RUN a2dismod -f mpm_event mpm_worker mpm_prefork || true \
    && rm -f /etc/apache2/mods-enabled/mpm_* \
    && a2enmod mpm_prefork

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

# 15. DIAGNOSTIK FINAL SEBELUM RUNTIME
# Ini akan mencetak hasil persis sebelum image dikunci oleh Docker.
RUN echo "=== DIAGNOSTIK FINAL: MPM YANG AKTIF DI FOLDER ===" \
    && ls -la /etc/apache2/mods-enabled/ | grep mpm \
    && echo "=== DIAGNOSTIK FINAL: APACHE MODULE (DUMP) ===" \
    && apache2ctl -M | grep mpm \
    && echo "=== CEK FILE KONFIGURASI YANG MENGANDUNG KATA mpm_ ===" \
    && grep -R "mpm_" /etc/apache2/ || true

# 16. CMD kembali ke default
CMD ["apache2-foreground"]
