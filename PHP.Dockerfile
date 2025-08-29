FROM php:8.2-fpm-alpine

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install dependencies for PHP extensions
RUN apk add --no-cache $PHPIZE_DEPS \
    linux-headers \
    mariadb-dev \
    ssmtp \
    bash \
    curl \
    git

# Install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Configure ssmtp
RUN echo "hostname=v.je" > /etc/ssmtp/ssmtp.conf \
    && echo "root=mailer@v.je>" >> /etc/ssmtp/ssmtp.conf \
    && echo "mailhub=maildev:1025" >> /etc/ssmtp/ssmtp.conf \
    && echo "sendmail_path=sendmail -i -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini

# Configure PHP upload size
RUN echo "post_max_size=5000M" > /usr/local/etc/php/conf.d/php-uploadsize.ini \
    && echo "upload_max_filesize=5000M" >> /usr/local/etc/php/conf.d/php-uploadsize.ini \
    && echo "short_open_tag=off" >> /usr/local/etc/php/conf.d/opentags.ini