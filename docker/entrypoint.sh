#!/bin/sh
set -e

# Hosting menentukan port lewat env PORT (Render: 10000, Zeabur: 8080, dst)
PORT="${PORT:-10000}"
sed -i "s/@APP_PORT@/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Build the cached config from the real environment variables on every boot
php artisan config:cache

# The free instance has an ephemeral filesystem, so the SQLite schema is (re)created each deploy
php artisan migrate --force --no-interaction

echo "MPM yang aktif: $(ls /etc/apache2/mods-enabled/ | grep -i '^mpm' | tr '\n' ' ')"
exec apache2-foreground
