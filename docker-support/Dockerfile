FROM php:7.2.12-apache
MAINTAINER egidio docile
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install mbstring pdo pdo_mysql mysqli
RUN a2enmod rewrite
RUN a2enmod headers
RUN echo 'PassEnv DATABASE_HOST' > /etc/apache2/conf-enabled/expose-env.conf 
RUN echo 'PassEnv DATABASE_USER' >> /etc/apache2/conf-enabled/expose-env.conf 
RUN echo 'PassEnv DATABASE_PASS' >> /etc/apache2/conf-enabled/expose-env.conf 
RUN echo 'PassEnv DATABASE_NAME' >> /etc/apache2/conf-enabled/expose-env.conf 

