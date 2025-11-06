#!/bin/bash

# Diagnostic script to find why iwacu.org redirects to file paths
# Run this on the server: bash diagnose-iwacu-url-issue.sh

echo "🔍 DIAGNOSING iwacu.org URL issue..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Check APP_URL
echo "📋 Step 1: Checking APP_URL..."
if [ -f ".env" ]; then
    if grep -q "^APP_URL=" .env; then
        APP_URL_VALUE=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        echo "📄 APP_URL: $APP_URL_VALUE"
        
        if [[ "$APP_URL_VALUE" == *"/home"* ]] || [[ "$APP_URL_VALUE" == *"public_html"* ]] || [[ "$APP_URL_VALUE" != *"iwacu.org"* ]]; then
            echo "❌ APP_URL IS INCORRECT - This is the problem!"
        else
            echo "✅ APP_URL looks correct"
        fi
    else
        echo "❌ APP_URL not set!"
    fi
    
    # Check ASSET_URL
    if grep -q "^ASSET_URL=" .env; then
        ASSET_URL_VALUE=$(grep "^ASSET_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        echo "📄 ASSET_URL: $ASSET_URL_VALUE"
        if [[ "$ASSET_URL_VALUE" == *"/home"* ]] || [[ "$ASSET_URL_VALUE" == *"public_html"* ]]; then
            echo "❌ ASSET_URL IS INCORRECT - This could be the problem!"
        fi
    fi
else
    echo "❌ .env file not found!"
fi

# 2. Check Laravel config
echo ""
echo "📋 Step 2: Checking Laravel config..."
php artisan tinker --execute="echo 'APP_URL: ' . config('app.url') . PHP_EOL;" 2>&1 | grep -v "Psy" | tail -3

# 3. Check cached config
echo ""
echo "📋 Step 3: Checking for cached config..."
if [ -f "bootstrap/cache/config.php" ]; then
    echo "⚠️  Cached config exists - may contain old APP_URL"
    echo "📄 Checking cached config..."
    grep -i "app.url" bootstrap/cache/config.php | head -1 || echo "Could not find in cache"
else
    echo "✅ No cached config"
fi

# 4. Check Laravel logs
echo ""
echo "📋 Step 4: Checking recent Laravel logs..."
if [ -f "storage/logs/laravel.log" ]; then
    echo "📄 Last 20 lines of laravel.log:"
    tail -20 storage/logs/laravel.log | grep -i "url\|redirect\|path" || echo "No URL/redirect errors found"
else
    echo "⚠️  Laravel log not found"
fi

# 5. Check if index.php paths are correct
echo ""
echo "📋 Step 5: Checking public_html/iwacu/index.php..."
if [ -f ~/public_html/iwacu/index.php ]; then
    echo "📄 Paths in index.php:"
    grep -E "vendor/autoload.php|bootstrap/app.php" ~/public_html/iwacu/index.php | head -2
    
    if grep -q "karahanyuze-spotify" ~/public_html/iwacu/index.php; then
        echo "✅ index.php points to karahanyuze-spotify"
    else
        echo "❌ index.php does NOT point to karahanyuze-spotify!"
    fi
else
    echo "❌ index.php not found!"
fi

# 6. Check .htaccess
echo ""
echo "📋 Step 6: Checking public_html/iwacu/.htaccess..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ .htaccess exists"
    echo "📄 First 10 lines:"
    head -10 ~/public_html/iwacu/.htaccess
else
    echo "❌ .htaccess not found!"
fi

echo ""
echo "✅ Diagnostic complete!"
echo ""
echo "🔧 To fix:"
echo "   1. If APP_URL is wrong, run: bash fix-iwacu-app-url.sh"
echo "   2. Clear all caches: php artisan config:clear && php artisan cache:clear"
echo "   3. Remove cached config: rm -f bootstrap/cache/config.php"
echo "   4. Test iwacu.org again"

