#!/bin/sh
# Render le indica en que puerto tiene que escuchar el servicio con la variable PORT (10000 por defecto)
set -e
PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
exec apache2-foreground
