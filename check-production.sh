#!/bin/bash

# Production Server Diagnostic Script
# Run this on your HostMonster server via SSH

echo "🔍 Laravel Production Diagnostic Check"
echo "======================================"
echo ""

cd ~/private/karahanyuze11

echo "1️⃣  Checking .env file..."
if [ -f .env ]; then
    echo "✅ .env file exists"
    if grep -q "DB_DATABASE=" .env; then
        DB_NAME=$(grep "DB_DATABASE=" .env | cut -d '=' -f2)
        echo "   Database name: $DB_NAME"
    else
        echo "   ⚠️  DB_DATABASE not set in .env"
    fi
    if grep -q "APP_KEY=" .env && ! grep -q "APP_KEY=$" .env; then
        echo "   ✅ APP_KEY is set"
    else
        echo "   ⚠️  APP_KEY is missing or empty"
        echo "   Run: php artisan key:generate --force"
    fi
else
    echo "   ❌ .env file not found!"
    echo "   Create it from .env.example and configure database settings"
    exit 1
fi

echo ""
echo "2️⃣  Testing database connection..."
php artisan db:show 2>&1 | head -5

echo ""
echo "3️⃣  Checking migration status..."
php artisan migrate:status 2>&1 | tail -10

echo ""
echo "4️⃣  Checking database tables..."
php artisan tinker --execute="echo 'Indirimbo: ' . DB::table('Indirimbo')->count() . ' songs\n'; echo 'Abahanzi: ' . DB::table('Abahanzi')->count() . ' artists\n'; echo 'Playlist: ' . DB::table('Playlist')->count() . ' playlists\n';" 2>&1 | grep -E "(Indirimbo|Abahanzi|Playlist)" || echo "   ⚠️  Could not query tables (possible database connection issue)"

echo ""
echo "5️⃣  Checking Laravel logs for errors..."
if [ -f storage/logs/laravel.log ]; then
    echo "   Recent errors:"
    tail -20 storage/logs/laravel.log | grep -i "error\|exception" | tail -5 || echo "   No recent errors found"
else
    echo "   ⚠️  No log file found"
fi

echo ""
echo "6️⃣  Quick fixes to try:"
echo "   - Run migrations: php artisan migrate --force"
echo "   - Clear caches: php artisan config:clear && php artisan route:clear && php artisan view:clear"
echo "   - Cache config: php artisan config:cache"
echo "   - Check database connection: php artisan db:show"
echo "   - Check if data exists: php artisan tinker"
echo ""
echo "✅ Diagnostic complete!"

