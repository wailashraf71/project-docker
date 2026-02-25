#!/bin/sh
set -e
cd /var/www/html/laravel-app

# Ensure .env exists (e.g. on first deploy when not in git)
test -f .env || cp .env.example .env

composer install --no-dev --optimize-autoloader

if grep -q '^APP_KEY=\s*$' .env 2>/dev/null || ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
  php artisan key:generate --force
fi

# Wait for Postgres to be reachable (safety net; Compose already waits for postgres health)
echo "Waiting for Postgres..."
i=0
while [ "$i" -lt 30 ]; do
  if php -r "
    try {
      new PDO(
        'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
      );
      exit(0);
    } catch (Exception \$e) {
      exit(1);
    }
  " 2>/dev/null; then
    echo "Postgres is ready."
    break
  fi
  i=$((i + 1))
  if [ "$i" -eq 30 ]; then
    echo "Postgres did not become ready in time."
    exit 1
  fi
  sleep 2
done

php artisan migrate --force

# Ensure storage and cache dirs exist and are writable by PHP-FPM (bind-mount UID can differ)
mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache
chmod -R 777 storage bootstrap/cache
# Clear stale compiled views so Blade recompiles into the correct path
php artisan view:clear 2>/dev/null || true

exec php-fpm
