FROM php:8.3-apache

# ==========================================
# 1. Install system dependencies
# ==========================================
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

# ==========================================
# 2. Install PHP extensions untuk CI4
# ==========================================
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mbstring pdo_mysql mysqli zip gd

# ==========================================
# 3. Enable Apache rewrite
# ==========================================
RUN a2enmod rewrite

# ==========================================
# 4. SOLUSI FINAL & BRUTAL UNTUK KONFLIK MPM
# ==========================================
# Alih-alih hanya menghapus symlink, kita HANCURKAN file fisik (.so) dari modul event dan worker.
# Jika file fisiknya tidak ada, mustahil bagi Apache untuk memuatnya, tidak peduli 
# konfigurasi apa yang mencoba memanggilnya saat runtime.
RUN rm -f /usr/lib/apache2/modules/mod_mpm_event.so \
    && rm -f /usr/lib/apache2/modules/mod_mpm_worker.so \
    && rm -f /etc/apache2/mods-available/mpm_event.load \
    && rm -f /etc/apache2/mods-available/mpm_worker.load \
    && rm -f /etc/apache2/mods-enabled/mpm_event.load \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.load \
    && a2enmod mpm_prefork || true

# ==========================================
# 5. Set CodeIgniter public directory
# ==========================================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# ==========================================
# 6. Copy project & Install Composer
# ==========================================
WORKDIR /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ==========================================
# 7. Permission CodeIgniter writable
# ==========================================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# ==========================================
# 8. Railway expose port
# ==========================================
EXPOSE 80

# ==========================================
# 9. Runtime Debug Wrapper
# ==========================================
RUN printf '#!/bin/bash\n\
echo "========================================"\n\
echo " APACHE RUNTIME DEBUG (STARTING) "\n\
echo "========================================"\n\
echo "--- Apache Syntax Test ---"\n\
apache2ctl -t || true\n\
echo "--- Loaded MPM Modules ---"\n\
apache2ctl -M 2>/dev/null | grep mpm || true\n\
echo "--- Enabled MPM Files ---"\n\
ls -la /etc/apache2/mods-enabled/ | grep mpm || true\n\
echo "--- ALL MPM LOADMODULE ---"\n\
grep -R "LoadModule.*mpm" /etc/apache2 || true\n\
echo "========================================"\n\
echo " STARTING APACHE "\n\
echo "========================================"\n\
exec apache2-foreground\n' > /usr/local/bin/start-apache.sh \
&& chmod +x /usr/local/bin/start-apache.sh

CMD ["/usr/local/bin/start-apache.sh"]
