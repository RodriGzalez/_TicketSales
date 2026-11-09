#!/bin/bash
set -e

# Configurar Apache para escuchar en el puerto dinámico que asigna Railway ($PORT)
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
    sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf
fi

# Desactivar todos los MPMs y activar únicamente mpm_prefork
a2dismod -f mpm_event mpm_worker mpm_prefork || true
a2enmod mpm_prefork

# Iniciar Apache en primer plano
exec apache2-foreground