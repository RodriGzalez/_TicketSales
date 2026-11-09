#!/bin/bash
set -e

# Usar el puerto que Railway asigna dinámicamente (por defecto 8080)
PORT="${PORT:-8080}"

# Configurar Apache para escuchar en el puerto correcto
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Asegurar que Apache use mpm_prefork
a2dismod -f mpm_event mpm_worker mpm_prefork || true
a2enmod mpm_prefork

# Ejecutar migraciones automáticamente de forma segura
php artisan migrate --force || true

# Mantener Apache corriendo en primer plano (indispensable para evitar el 502)
exec apache2-foreground