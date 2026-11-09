#!/bin/bash
set -e

# Limpiar módulos MPM conflictivos
a2dismod -f mpm_event mpm_worker mpm_prefork || true
a2enmod mpm_prefork

# Ejecutar migraciones automáticamente al iniciar el contenedor
php artisan migrate --force || true

# Mantener Apache corriendo en primer plano (indispensable para Railway)
exec apache2-foreground