FROM php:8.2-apache

# Dépendances système pour les extensions PHP, Stripe et les outils de base de données
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        wget \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        libssl-dev \
        libicu-dev \
        default-mysql-client \
        git \
        unzip \
    && wget -q https://fastdl.mongodb.org/tools/db/mongodb-database-tools-debian12-x86_64-100.9.4.deb \
    && dpkg -i mongodb-database-tools-debian12-x86_64-100.9.4.deb || apt-get install -f -y \
    && rm mongodb-database-tools-debian12-x86_64-100.9.4.deb \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP : PDO MySQL, MongoDB (PECL), intl, zip, xml
# curl et openssl sont déjà présents dans l'image officielle (requis pour Stripe)
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) pdo_mysql intl zip xml opcache \
    && pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb

# Apache : mod_rewrite, document root = public/
RUN a2enmod rewrite \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www!/var/www!g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# Dépendances PHP (copie des manifests en premier pour cache Docker)
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Code applicatif
COPY . .

RUN composer dump-autoload --optimize

# Droits var/ pour cache et logs
RUN chown -R www-data:www-data /var/www/html/var 2>/dev/null || true \
    && mkdir -p /var/www/html/var/cache /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html/var

# Logs vers stderr (Cloud-Ready) — ne pas mettre APACHE_LOG_DIR=/dev/stdout (Apache tente mkdir)
RUN ln -sf /dev/stderr /var/log/apache2/error.log 2>/dev/null || true

EXPOSE 80

COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]