FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor\
    git \
    unzip \
    p7zip-full \
    libzip-dev \
    libpq-dev \
    postgresql-client \
    pkg-config \
    libicu-dev \
    zlib1g-dev \
    g++ \
    autoconf \
    make \
    --no-install-recommends

RUN docker-php-ext-configure pgsql && \
    docker-php-ext-install pdo_pgsql pgsql zip

WORKDIR /var/www
COPY . .

# Limpar diretórios específicos do Laravel
RUN rm -rf /var/www/storage/* /var/www/bootstrap/cache/*

# Instalar composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Definir permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www \
    && chown -R www-data /var/www/storage

# Copia o arquivo de configuração do Nginx
COPY ./nginx/laravel.conf /etc/nginx/sites-available/default

# Gerar chave do Laravel e rodar as migrações (opcional, pode ser rodado em tempo de execução)
RUN php artisan key:generate

#RUN php artisan key:generate && \
#    php artisan migrate --force

# Expor as portas para Nginx e PHP-FPM
EXPOSE 80 9000

# Comando para iniciar o Nginx e PHP-FPM
CMD service nginx start && php-fpm
