FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    git zip unzip libicu-dev libzip-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN if [ -f "composer.json" ]; then composer install --no-dev --optimize-autoloader; fi

RUN if [ -d "public" ]; then \
        sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf; \
    fi

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80
