# Use imagem oficial PHP-FPM Alpine (menor e mais segura)
FROM php:8.3-fpm-alpine

# Variáveis de ambiente
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/tmp
ENV PHP_OPCACHE_ENABLE=1

# Instalar dependências do sistema
RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    zip \
    unzip \
    git \
    curl \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    supervisor \
    nodejs \
    npm \
    netcat-openbsd

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache \
    intl

# Instalar Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário sail com mesmo UID/GID do host
ARG WWWGROUP=1000
ARG WWWUSER=1000
RUN addgroup -g $WWWGROUP sail \
    && adduser -D -s /bin/sh -G sail -u $WWWUSER sail

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos de configuração
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copiar composer files e instalar dependências (cache layer)
COPY --chown=sail:sail composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Copiar package.json e instalar dependências Node (cache layer)
COPY --chown=sail:sail package*.json ./
RUN npm install

# Copiar código da aplicação
COPY --chown=sail:sail . .

# Gerar autoload otimizado
RUN composer dump-autoload --optimize --classmap-authoritative

# Pular build assets por enquanto (CSS via volume)
# RUN npm run build
RUN npm cache clean --force
RUN npm prune --production

# Criar diretórios necessários com permissões corretas
RUN mkdir -p storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R sail:sail storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copiar entrypoint otimizado
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Configurar usuário não-root
USER sail

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"] 