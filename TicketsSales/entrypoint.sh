#!/bin/bash
set -e

echo "=== 1. EL ENTRYPOINT HA SIDO EJECUTADO CORRECTAMENTE ==="

# Usar el puerto que Railway asigna dinámicamente (por defecto 8080)
PORT="${PORT:-8080}"
echo "=== 2. Puerto configurado: ${PORT} ==="

# Configurar Apache para escuchar en el puerto correcto de Railway
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Asegurar que Apache use el módulo mpm_prefork para PHP
a2dismod -f mpm_event mpm_worker mpm_prefork || true
a2enmod mpm_prefork
echo "=== 3. Módulos MPM configurados ==="

# Ejecutar las migraciones de base de datos automáticamente
echo "=== 4. Ejecutando migraciones ==="
php artisan migrate --force || true

echo "=== 5. Iniciando Apache en primer plano ==="
# Mantener Apache corriendo en primer plano
exec apache2-foreground