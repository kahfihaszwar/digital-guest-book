#!/bin/sh
set -e

# Build the cached config from the real environment variables on every boot
php artisan config:cache

# The free instance has an ephemeral filesystem, so the SQLite schema is (re)created each deploy
php artisan migrate --force --no-interaction

exec apache2-foreground
