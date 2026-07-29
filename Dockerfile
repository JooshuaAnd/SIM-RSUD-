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

# 3. Disable mpm_event & mpm_worker, lalu pastikan prefork aktif (BUILD TIME)
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork

# 4. Validasi Build Time
RUN echo "=== DIAGNOSTIK BUILD: MPM AKTIF ===" \
    && ls -la /etc/apache2/mods-enabled | grep mpm

# 5. Enable Apache rewrite
RUN a2enmod rewrite

# 6. Set CodeIgniter DocumentRoot (mengubah konfigurasi bawaan secara aman tanpa echo manual)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7. Working Directory & Composer
WORKDIR /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 8. Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80

# 9. RUNTIME SCRIPT (AUTO-HEAL & DIAGNOSTIC)
RUN printf '#!/bin/bash\n\
echo "=========================================="\n\
echo "=== APACHE RUNTIME DIAGNOSTIC & HEAL ==="\n\
echo "=========================================="\n\
echo "[*] Modul MPM di mods-enabled saat runtime:"\n\
ls -la /etc/apache2/mods-enabled | grep mpm || true\n\
\n\
# Auto-Heal: Deteksi jika ada mpm_event atau mpm_worker yang menyusup masuk saat runtime\n\
if ls /etc/apache2/mods-enabled/mpm_*.load | wc -l | grep -v "^1$" > /dev/null; then\n\
    echo "[!] PERINGATAN: Ditemukan lebih dari satu MPM! Melakukan AUTO-HEAL..."\n\
    rm -f /etc/apache2/mods-enabled/mpm_event.load\n\
    rm -f /etc/apache2/mods-enabled/mpm_event.conf\n\
    rm -f /etc/apache2/mods-enabled/mpm_worker.load\n\
    rm -f /etc/apache2/mods-enabled/mpm_worker.conf\n\
    a2enmod mpm_prefork\n\
    echo "[+] AUTO-HEAL selesai. Modul MPM aktif saat ini:"\n\
    ls -la /etc/apache2/mods-enabled | grep mpm\n\
else\n\
    echo "[+] MPM aman (hanya 1 modul aktif)."\n\
fi\n\
\n\
echo "--- LOADED MODULES (apache2ctl -M) ---"\n\
apache2ctl -M 2>/dev/null | grep mpm || true\n\
\n\
echo "=========================================="\n\
echo "=== STARTING APACHE ==="\n\
echo "=========================================="\n\
exec apache2-foreground\n' > /usr/local/bin/start-apache.sh \
&& chmod +x /usr/local/bin/start-apache.sh

CMD ["/usr/local/bin/start-apache.sh"]
