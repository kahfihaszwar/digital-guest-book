#!/bin/sh
# Jalankan situs Buku Tamu secara publik lewat Cloudflare Tunnel (gratis, tanpa kartu).
# Pakai: sh serve-public.sh
set -e
cd "$(dirname "$0")"

CF=".tools/cloudflared.exe"
if [ ! -f "$CF" ]; then
    mkdir -p .tools
    echo "Mengunduh cloudflared..."
    curl -sL -o "$CF" https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe
fi

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --ansi
fi

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    php artisan migrate --force --no-interaction
fi

php artisan serve --host=127.0.0.1 --port=8000 &
SERVER_PID=$!
trap 'kill $SERVER_PID 2>/dev/null' INT TERM EXIT

echo "URL publik muncul di bawah (baris berisi https://....trycloudflare.com)"
"$CF" tunnel --url http://127.0.0.1:8000 --no-autoupdate
