FROM php:8.3-cli

ARG UID
ARG CONTAINER_USER
ARG CONTAINER_ROOT_PASSWORD

RUN groupadd -g ${UID} ${CONTAINER_USER} && useradd -m -u ${UID} -g ${UID} -s /bin/bash ${CONTAINER_USER}

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN apt-get update && \
    apt-get install -y git libicu-dev libzip-dev passwd unzip && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install pdo_mysql zip intl && \
    chown -R ${CONTAINER_USER}:${UID} /var/www/html && \
    echo "root:${CONTAINER_ROOT_PASSWORD}" | chpasswd

USER ${CONTAINER_USER}
