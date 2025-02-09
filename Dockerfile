# Utiliser PHP avec Apache
FROM php:8.2-apache

# Installer les dépendances système nécessaires pour PDO et PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet Symfony
WORKDIR /var/www/html
COPY . .

# Installer les dépendances via Composer
RUN composer install --no-interaction --optimize-autoloader

# Exposer le port
EXPOSE 8000

# Commande par défaut
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
