FROM php:7.2.12-apache
MAINTAINER arjan vlek
RUN docker-php-ext-install pdo_mysql mysqli; \
    pecl install xdebug \
        && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
        && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
        && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini; \
    a2enmod rewrite; \
    a2enmod headers; \
    echo 'PassEnv DATABASE_HOST' > /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_USER' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_PASS' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_NAME' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv TIMEZONE' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv NEWS_IMAGES_PATH' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv NEWS_IMAGES_RELATIVE_URL' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv NEWS_IMAGE_UPLOADER_AUTH_URL' >> /etc/apache2/conf-enabled/expose-env.conf
