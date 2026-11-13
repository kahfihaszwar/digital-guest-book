#!/bin/sh
set -e

# Hosting menentukan port lewat env PORT (Render: 10000, Zeabur: 8080, dst)
PORT="${PORT:-10000}"
sed -i "s/@APP_PORT@/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# SQLite biasanya diarahkan ke volume terpasang (DB_DATABASE), jadi siapkan file + izin tulisnya
DB_FILE="${DB_DATABASE:-database/database.sqlite}"
case "$DB_FILE" in /*) ;; *) DB_FILE="/var/www/html/$DB_FILE" ;; esac
mkdir -p "$(dirname "$DB_FILE")" storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
[ -f "$DB_FILE" ] || touch "$DB_FILE"
chown -R www-data:www-data "$(dirname "$DB_FILE")" storage bootstrap/cache
chmod -R ug+rwX "$(dirname "$DB_FILE")" storage bootstrap/cache

# Build the cached config from the real environment variables on every boot
php artisan config:cache

# The free instance has an ephemeral filesystem, so the SQLite schema is (re)created each deploy
php artisan migrate --force --no-interaction

# Hanya satu MPM yang boleh aktif, kalau tidak Apache gagal start (AH00534)
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf

if [ ! -e /etc/apache2/mods-enabled/mpm_prefork.load ]; then
    ln -sf ../mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
fi

echo "MPM yang aktif: $(ls /etc/apache2/mods-enabled/ | grep -i '^mpm' | tr '\n' ' ')"
exec apache2-foreground
