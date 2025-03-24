FROM php:8.1-apache

# Habilita mod_rewrite
RUN a2enmod rewrite

# Instala extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copia os arquivos para o container
COPY . /var/www/html/

# Define o diretório de trabalho
WORKDIR /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
