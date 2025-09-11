FROM php:8.2-apache

# Instala extensões PHP necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia os arquivos do projeto para dentro do container
COPY ./src /var/www/html

EXPOSE 80
