#!/bin/bash
set -e

# Asignar puerto 8080 por defecto si la variable PORT no está definida
PORT="${PORT:-8080}"

# Sobrescribir completamente el archivo ports.conf para evitar errores de sintaxis
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Configurar el VirtualHost por defecto para escuchar en el puerto correcto
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Desactivar módulos MPM conflictivos y activar prefork
a2dismod -f mpm_event mpm_worker mpm_prefork || true
a2enmod mpm_prefork

# Iniciar Apache en primer plano
exec apache2-foreground