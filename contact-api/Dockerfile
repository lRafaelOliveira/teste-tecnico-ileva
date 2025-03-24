FROM php:8.2-apache

# Instala extensões do PHP necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Define o diretório raiz do Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Altera o VirtualHost para usar /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Copia arquivos do projeto para o container
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html
