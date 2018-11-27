FROM php:7.2.12-apache
MAINTAINER egidio docile
RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN a2enmod rewrite
RUN a2enmod headers

RUN echo 'PassEnv DATABASE_HOST' > /etc/apache2/conf-enabled/expose-env.conf 
RUN echo 'PassEnv DATABASE_USER' >> /etc/apache2/conf-enabled/expose-env.conf 
RUN echo 'PassEnv DATABASE_PASS' >> /etc/apache2/conf-enabled/expose-env.conf 
RUN echo 'PassEnv DATABASE_NAME' >> /etc/apache2/conf-enabled/expose-env.conf 
