FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql \
    && apt-get update && apt-get install -y ssl-cert openssl \
    && make-ssl-cert generate-default-snakeoil --force-overwrite \
    && a2enmod ssl \
    && a2enmod socache_shmcb \
    && a2enmod rewrite
COPY custom-ssl.conf /etc/apache2/sites-available/custom-ssl.conf
RUN a2ensite custom-ssl