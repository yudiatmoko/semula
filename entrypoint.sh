#!/bin/bash

# Jika role adalah 'app', jalankan migrasi dan cache
if [ "$role" = "app" ]; then
    echo "Caching configuration..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    echo "Running migrations..."
    php artisan migrate --force
    
    echo "Starting Apache..."
    apache2-foreground

# Jika role adalah 'queue', jalankan worker
elif [ "$role" = "queue" ]; then
    echo "Starting Queue Worker..."
    php artisan queue:work --verbose --tries=3 --timeout=90
fi