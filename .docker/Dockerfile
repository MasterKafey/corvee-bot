FROM php:8.2-cli

ENV TZ Europe/Paris

RUN apt update && apt install -y \
    supervisor \
    git \
    cron \
    libzip-dev \
    tzdata \
    unzip

RUN apt clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip
RUN echo "date.timezone = $TZ" > /usr/local/etc/php/php.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN git clone https://github.com/MasterKafey/corvee-bot.git /app
WORKDIR /app

RUN groupadd -r corvee && useradd -r -g corvee corvee \
    && mkdir -p /app \
    && chown -R corvee:corvee /app

USER corvee
RUN composer install --no-dev --optimize-autoloader
USER root

COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
CMD ["/usr/bin/supervisord"]