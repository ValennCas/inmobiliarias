# Imagen para Render: Apache + PHP 8.2 sirviendo el front (/frontend) y el backend (/backend) desde el mismo dominio.
FROM php:8.2-apache

# Extension para hablar con MySQL (curl, openssl y mbstring ya vienen en la imagen) y modulo de headers de Apache
RUN docker-php-ext-install pdo pdo_mysql && a2enmod headers

# Configuracion de produccion de PHP: no muestra errores en pantalla (ensuciarian el JSON), los manda al log
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini $PHP_INI_DIR/conf.d/habita.ini

# Reglas de Apache (archivos bloqueados, raiz del sitio, fotos sin PHP)
COPY docker/apache.conf /etc/apache2/conf-available/habita.conf
RUN a2enconf habita

COPY . /var/www/html/

# hash.php es una herramienta de desarrollo: no va en linea.
# La carpeta de fotos tiene que poder escribirla Apache.
RUN rm -f /var/www/html/backend/hash.php /var/www/html/backend/.env.local \
    && mkdir -p /var/www/html/backend/cargasImagenes/fotosPropiedades \
    && chown -R www-data:www-data /var/www/html/backend/cargasImagenes

# El script de arranque tiene que tener saltos de linea de Linux aunque se haya clonado en Windows
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
