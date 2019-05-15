# Deployable image of the Oxygen Updater Backend. Suitable for use in production
FROM php:7.2.12-apache
MAINTAINER arjan vlek

# The Apache web server listens at port 80
EXPOSE 80

# 1. Setup the image to contain our environment, required PHP extensions and required Apache modules
RUN apt-get update; \
    apt-get install -y apt-utils zlib1g-dev; \
    docker-php-ext-install pdo_mysql mysqli zip; \
    a2enmod rewrite; \
    a2enmod headers; \
    echo 'PassEnv DATABASE_HOST' > /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_USER' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_PASS' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv DATABASE_NAME' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv TIMEZONE' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv SUBMITTED_UPDATE_FILE_WEBHOOK_ACTION_URL' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv SUBMITTED_UPDATE_FILE_WEBHOOK_AUTHOR_NAME' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv SUBMITTED_UPDATE_FILE_WEBHOOK_URL' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv MISSING_UPDATE_VERSIONS_WEBHOOK_ACTION_URL' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv MISSING_UPDATE_VERSIONS_WEBHOOK_AUTHOR_NAME' >> /etc/apache2/conf-enabled/expose-env.conf; \
    echo 'PassEnv MISSING_UPDATE_VERSIONS_WEBHOOK_URL' >> /etc/apache2/conf-enabled/expose-env.conf

# 2. Copy all project files and dirs to the webroot of the Apache container
COPY ./.well-known /var/www/html/.well-known
COPY ./api /var/www/html/api
COPY ./css /var/www/html/css
COPY ./fonts /var/www/html/fonts
COPY ./img /var/www/html/img

# Routing and rewriting
COPY .htaccess /var/www/html

# Webpages
COPY *.php /var/www/html/
COPY *.html /var/www/html/
COPY *.pdf /var/www/html/

# Robots.txt
COPY robots.txt /var/www/html

# Favicon and theming
COPY favicon.ico /var/www/html
COPY manifest.json /var/www/html
COPY browserconfig.xml /var/www/html

# 3. Install the dependencies for the backend code using Composer
COPY ./composer.json /var/www/html
COPY ./deployment/install-composer.sh /var/www/html

RUN apt-get install -y wget unzip; \
    cd /var/www/html; \
    chmod +x install-composer.sh; \
    ./install-composer.sh; \
    php composer.phar update; \
    chmod -R 777 vendor

# 4. Remove the Composer installer and files
RUN cd /var/www/html; \
    rm composer.json; \
    rm composer.lock; \
    rm composer.phar; \
    rm install-composer.sh

