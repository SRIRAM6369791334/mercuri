#!/bin/bash
set -e

# Render provides $PORT (default to 80 if not provided)
export PORT="${PORT:-80}"

# Configure Apache port dynamically
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# 1. Ensure .env exists inside container
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# 2. Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:zYTozkLMuBuk1w+XyFQ+PJg/cqOujBRX0iuzl09mdb0="
fi
sed -i "/^APP_KEY=/d" /var/www/html/.env || true
echo "APP_KEY=${APP_KEY}" >> /var/www/html/.env

# 3. Inject Database environment settings (TiDB Cloud Serverless)
DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-gateway01.ap-southeast-1.prod.aws.tidbcloud.com}"
DB_PORT="${DB_PORT:-4000}"
DB_DATABASE="${DB_DATABASE:-mercuri_db}"
DB_USERNAME="${DB_USERNAME:-EyMBLb8z3cuyjH7.root}"
DB_PASSWORD="${DB_PASSWORD:-LXlEj8KMK1ylZMQ8}"
MYSQL_ATTR_SSL_CA="${MYSQL_ATTR_SSL_CA:-/etc/ssl/certs/ca-certificates.crt}"

sed -i "/^DB_CONNECTION=/d" /var/www/html/.env || true
sed -i "/^DB_HOST=/d" /var/www/html/.env || true
sed -i "/^DB_PORT=/d" /var/www/html/.env || true
sed -i "/^DB_DATABASE=/d" /var/www/html/.env || true
sed -i "/^DB_USERNAME=/d" /var/www/html/.env || true
sed -i "/^DB_PASSWORD=/d" /var/www/html/.env || true
sed -i "/^MYSQL_ATTR_SSL_CA=/d" /var/www/html/.env || true

echo "DB_CONNECTION=${DB_CONNECTION}" >> /var/www/html/.env
echo "DB_HOST=${DB_HOST}" >> /var/www/html/.env
echo "DB_PORT=${DB_PORT}" >> /var/www/html/.env
echo "DB_DATABASE=${DB_DATABASE}" >> /var/www/html/.env
echo "DB_USERNAME=${DB_USERNAME}" >> /var/www/html/.env
echo "DB_PASSWORD=${DB_PASSWORD}" >> /var/www/html/.env
echo "MYSQL_ATTR_SSL_CA=${MYSQL_ATTR_SSL_CA}" >> /var/www/html/.env

# 4. App environment defaults
sed -i "/^APP_ENV=/d" /var/www/html/.env || true
sed -i "/^APP_DEBUG=/d" /var/www/html/.env || true
echo "APP_ENV=${APP_ENV:-production}" >> /var/www/html/.env
echo "APP_DEBUG=${APP_DEBUG:-false}" >> /var/www/html/.env

# 5. Ensure writable storage directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/.env
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
chmod 664 /var/www/html/.env

# 6. Ensure storage symlink exists
if [ ! -L "/var/www/html/public/storage" ]; then
    php artisan storage:link || true
fi

# 7. Run migrations if explicitly enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# 8. Clear and optimize Laravel caches
php artisan package:discover --ansi || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "==> Application ready. Starting Apache web server on port ${PORT}..."
exec apache2-foreground
