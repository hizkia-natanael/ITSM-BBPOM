#!/bin/sh
set -e

echo "[BBPOM-Docker] Menyinkronkan aset statis ke shared volume..."
if [ -d "/opt/public-dist" ]; then
    cp -rf /opt/public-dist/. /var/www/html/public/
fi

echo "[BBPOM-Docker] Memastikan struktur direktori storage tersedia..."
mkdir -p /var/www/html/storage/app/private/attachments \
    /var/www/html/storage/app/private/ticket-responses \
    /var/www/html/storage/app/public/attachments \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

if [ -f "/var/www/html/.env" ] && ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
    echo "[BBPOM-Docker] Membuat APP_KEY baru..."
    php artisan key:generate --force --no-interaction
fi

echo "[BBPOM-Docker] Menjalankan migrasi database..."
php artisan migrate --force --no-interaction

USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tr -d '\r\n' || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "[BBPOM-Docker] Database kosong terdeteksi, menjalankan seeder awal BBPOM..."
    php artisan db:seed --force --no-interaction
fi

php artisan storage:link --force --no-interaction || true
php artisan filament:assets --no-interaction || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[BBPOM-Docker] Aplikasi siap dijalankan!"
exec "$@"
