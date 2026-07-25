# Imagen base oficial: PHP 8.3 + Apache
FROM php:8.3-apache

# Dependencias del sistema: libxslt (XSLT de la app) y cliente mysql (conexionXML)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libxslt1-dev default-mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql xsl \
    && apt-get purge -y libxslt1-dev && apt-get autoremove -y \
    && apt-get install -y --no-install-recommends libxslt1.1 \
    && rm -rf /var/lib/apt/lists/*

# Código de la app de inventario (RetaCantabria)
COPY src/ /var/www/html/

EXPOSE 80
# La imagen base ya arranca Apache en primer plano (un proceso por contenedor)
