FROM php:7.4-apache

# Instalar extensiones necesarias para CodeIgniter 3
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    zip \
    gd

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Configurar el DocumentRoot para CodeIgniter
RUN sed -i 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar archivo de configuración PHP personalizado
COPY php.ini /usr/local/etc/php/

# Exponer puerto (opcional, ya se define en docker-compose)
EXPOSE 80