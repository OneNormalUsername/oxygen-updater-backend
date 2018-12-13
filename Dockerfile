FROM php:7.2.12-apache
MAINTAINER arjan vlek
RUN docker-php-ext-install pdo pdo_mysql mysqli; \
    a2enmod rewrite; \
    a2enmod headers; \
    echo 'PassEnv DATABASE_HOST' > /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_USER' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_PASS' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_NAME' >> /etc/apache2/conf-enabled/expose-env.conf
