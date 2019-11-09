FROM php:7.3.11-apache

LABEL maintainer="Majid Akbari <majidakbariiii@gmail.com"

ARG WWW_DATA_USER_ID

# Install base packages
RUN apt-get update -y && \
    apt-get install -y \
        git \
        libzip-dev \
        zlib1g-dev \
        libssl-dev && \
        docker-php-ext-install -j$(nproc) zip \
        mbstring \
        pdo \
        pdo_mysql \
        zip

RUN docker-php-ext-configure bcmath --enable-bcmath && \
    docker-php-ext-install pcntl bcmath


RUN  usermod -u ${WWW_DATA_USER_ID} www-data && \
    groupmod -g ${WWW_DATA_USER_ID} www-data


# Installing composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Move some helper commands
COPY ["./commands/*", "/usr/local/bin/"]
RUN chmod +x /usr/local/bin/*

# Config apache and copy entrypoint
COPY config/default.conf /etc/apache2/sites-enabled/000-default.conf
COPY config/expire.conf /etc/apache2/conf-enabled/
COPY ./entrypoint.sh /

RUN \
    printf "\nServerName localhost" >> /etc/apache2/apache2.conf  && \
    a2enmod rewrite && \
    a2enmod expires

# Override workdir to an existing directory
WORKDIR /var/www

ENTRYPOINT ["/entrypoint.sh"]

