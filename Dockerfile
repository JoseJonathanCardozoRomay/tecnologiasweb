FROM php:8.2-apache

# Instalar extensiones necesarias de PHP para MySQL (PDO) y fileinfo (validación MIME)
RUN docker-php-ext-install pdo pdo_mysql fileinfo

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Crear carpeta para evidencias de reuniones con permisos de escritura para Apache
RUN mkdir -p /var/www/html/uploads/evidencias \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads
