# Utilizăm o imagine oficială PHP cu Apache
FROM php:8.1-apache

# Instalăm extensiile necesare pentru Symfony și Doctrine
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl opcache pdo pdo_mysql

# Instalăm Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setăm directorul de lucru
WORKDIR /var/www/html

# Copiem fișierele aplicației
COPY . .

# Instalăm dependențele
RUN composer install --no-interaction --optimize-autoloader

# Setăm permisiunile
RUN chown -R www-data:www-data /var/www/html/var
RUN chmod -R 777 /var/www/html/var

# Activăm modulul de rescriere a URL-urilor pentru Apache
RUN a2enmod rewrite

# Configurăm vhost pentru Symfony
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Expunem portul 80
EXPOSE 80

# Comanda de start pentru container
CMD ["apache2-foreground"]
