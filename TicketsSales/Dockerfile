FROM php:8.3-apache

# Habilitar mod_rewrite para las rutas de Laravel
RUN a2enmod rewrite

# Solucionar conflicto de múltiples MPMs en Apache
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork

# Instalar dependencias del sistema y extensiones de PHP (incluyendo GD y PDO MySQL)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar los archivos del proyecto
COPY . /var/www/html

# Cambiar la raíz del servidor web Apache hacia la carpeta public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Instalar dependencias de producción mediante Composer
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Asignar permisos correctos a las carpetas de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache