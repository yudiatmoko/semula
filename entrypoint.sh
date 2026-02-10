#!/bin/bash
set -e

echo "🚀 Starting Semula..."

# Create .env if not exists (needed by artisan commands)
if [ ! -f /var/www/html/.env ]; then
    echo "⏳ Creating .env file..."
    touch /var/www/html/.env
fi

# Wait for database to be ready
echo "⏳ Waiting for database..."
until php artisan db:monitor --databases=mysql 2>/dev/null; do
    sleep 2
done
echo "✅ Database connected!"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "⏳ Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "⏳ Running migrations..."
php artisan migrate --force

# Link storage
php artisan storage:link --force 2>/dev/null || true

# Cache config & routes for production
echo "⏳ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application ready!"

# Execute CMD (apache2-foreground)
exec "$@"
