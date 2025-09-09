FROM php:8.2-apache

# Installer les extensions MySQL nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copier tous vos fichiers
COPY . /var/www/html/

# Exposer le port 80
EXPOSE 80
