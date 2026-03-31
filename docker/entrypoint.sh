#!/bin/sh
set -e

# In non-dev mode remove Vite "hot" marker so Laravel uses compiled assets from public/build.
# Set AUTO_REMOVE_HOT=0 when running a dedicated Vite dev server in Docker.
if [ "${AUTO_REMOVE_HOT:-1}" = "1" ]; then
    rm -f /var/www/html/public/hot
fi

# When host `public/build` is not mounted (or is empty), restore image-built assets.
if [ ! -f /var/www/html/public/build/manifest.json ] \
    && [ ! -f /var/www/html/public/build/.vite/manifest.json ]; then
    if [ -d /opt/docker-assets/public/build ] \
        && [ -n "$(ls -A /opt/docker-assets/public/build 2>/dev/null)" ]; then
        mkdir -p /var/www/html/public/build
        cp -a /opt/docker-assets/public/build/. /var/www/html/public/build/
    fi
fi

# App bootstrap in container
SQLITE_PATH="${DB_DATABASE:-/var/lib/sqlite/database.sqlite}"
mkdir -p "$(dirname "$SQLITE_PATH")"
touch "$SQLITE_PATH"

# Keep startup fast by default; run migrations only when explicitly enabled.
if [ "${AUTO_MIGRATE:-0}" = "1" ]; then
    php artisan migrate --force

    # Optional: seed after migrations
    if [ "${AUTO_SEED:-0}" = "1" ]; then
        php artisan db:seed --force
    fi
fi

# Ensure Laravel writable directories are writable in Docker on Windows mounts.
mkdir -p /var/www/html/storage/framework/{cache,sessions,views} /var/www/html/storage/logs /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache || true

# Fallback for restrictive host ACLs on some Windows Docker setups.
if ! touch /var/www/html/storage/logs/.write_test 2>/dev/null; then
    chmod -R 0777 /var/www/html/storage /var/www/html/bootstrap/cache || true
else
    rm -f /var/www/html/storage/logs/.write_test
fi

exec "$@"
