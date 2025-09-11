FROM php:8.2-apache

# instala extensões PHP necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# instala ping, dig, netstat etc (só pra debug)
RUN apt-get update && apt-get install -y iputils-ping net-tools dnsutils && rm -rf /var/lib/apt/lists/*

# copia os arquivos do projeto para dentro do container
COPY ./src /var/www/html

EXPOSE 80
