# Apache + PHP que imita la VM de producción (solo para ensayar el despliegue en local).
FROM php:8.4-apache
RUN apt-get update && apt-get install -y --no-install-recommends libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libzip-dev libxml2-dev libonig-dev libmagickwand-dev unzip openssl \
 && docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install mysqli gd intl zip soap mbstring exif opcache \
 && pecl install imagick && docker-php-ext-enable imagick \
 && a2enmod rewrite headers expires ssl \
 && printf 'ServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-enabled/seg.conf \
 && echo 'expose_php=Off' > /usr/local/etc/php/conf.d/seg.ini \
 && openssl req -x509 -nodes -newkey rsa:2048 -days 30 -subj "/CN=dgacweb.uleam.edu.ec" \
      -keyout /etc/ssl/private/ensayo.key -out /etc/ssl/certs/ensayo.crt \
 && rm -rf /var/lib/apt/lists/*
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
