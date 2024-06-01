FROM php:8.1-cli

# Variables d'environnement
ARG GITHUB_TOKEN
ARG DISCORD_BOT_TOKEN
ARG APP_CORVEE_SPREADSHEET_ID
ARG APP_LIST_COURSE_SPREADSHEET_ID
ENV TZ Europe/Paris
ENV DISCORD_BOT_TOKEN $DISCORD_BOT_TOKEN
ENV APP_CORVEE_SPREADSHEET_ID $APP_CORVEE_SPREADSHEET_ID
ENV APP_LIST_COURSE_SPREADSHEET_ID $APP_LIST_COURSE_SPREADSHEET_ID

# Installation des dépendances
RUN apt-get update && apt-get install -y supervisor git cron libzip-dev tzdata && \
    docker-php-ext-install zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    echo "date.timezone = $TZ" > /usr/local/etc/php/php.ini

WORKDIR /app

# Clonage du repo et installation des dépendances
RUN git clone https://${GITHUB_TOKEN}@github.com/MasterKafei/corvee-bot.git . && \
    composer install --no-interaction --no-scripts

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
CMD ["/usr/bin/supervisord"]