#!/bin/bash
set -e

# Usar el puerto de Railway o 8080 por defecto si la variable está vacía
PORT="${PORT:-8080}"

# Configurar Apache para escuchar en el puerto dinámico
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Desactivar todos los MPMs y activar únicamente mpm_prefork
a2dismod -f mpm_event mpm_worker mpm_prefork || true
a2enmod mpm_prefork

# Iniciar Apache en primer plano
exec apache2-foreground