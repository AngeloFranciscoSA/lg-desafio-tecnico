#!/bin/sh
set -e

cd /var/www

echo "Waiting for database..."
RETRIES=30
until php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306}', '${DB_USERNAME:-root}', '${DB_PASSWORD:-secret}');" 2>/dev/null; do
    RETRIES=$((RETRIES - 1))
    if [ "$RETRIES" -le 0 ]; then
        echo "Database unreachable after 30 attempts" >&2
        exit 1
    fi
    sleep 1
done
echo "Database ready."

if ! grep -qE '^APP_KEY=base64:' .env 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "Running migrations..."
php artisan migrate --force

exec "$@"
