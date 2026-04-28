#!/bin/bash

if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    npm install
fi

php artisan key:generate --show

php artisan migrate

npm run dev -- --host &

exec php artisan serve --host=0.0.0.0 --port=8000