FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/src/public|g' \
        /etc/apache2/sites-enabled/000-default.conf

RUN echo '<Directory /var/www/html/src/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf