# Usa a imagem oficial PHP com Apache
FROM php:8.2-apache

# Instala dependências para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copia o código da aplicação
COPY ./src /var/www/html/

# Expondo a porta 80
EXPOSE 80
