#!/bin/bash
set -e

# Desactivar todos los MPMs y activar únicamente mpm_prefork en tiempo de ejecución
a2dismod -f mpm_event mpm_worker mpm_prefork || true
a2enmod mpm_prefork

# Iniciar Apache en primer plano
exec apache2-foreground