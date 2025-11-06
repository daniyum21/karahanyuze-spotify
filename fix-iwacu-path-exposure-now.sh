#!/bin/bash

# IMMEDIATE FIX for iwacu.org path exposure
# This fixes the redirect to /home4/biriheco/public_html/iwacu/%25%25
# Run this on the server: bash fix-iwacu-path-exposure-now.sh

echo "🔒 IMMEDIATE FIX: Fixing iwacu.org path exposure"
echo ""

cd ~/private/karahanyuze-spotify

if [ ! -f ".env" ]; then
    echo "❌ .env file not found!"
    exit 1
fi

# 1. Fix APP_URL
echo "📋 Step 1: Fixing APP_URL..."
if grep -q "^APP_URL=" .env; then
    APP_URL_VALUE=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
    echo "📄 Current APP_URL: $APP_URL_VALUE"
    
    # Fix if it contains ANY file system paths
    if [[ "$APP_URL_VALUE" == *"/home"* ]] || [[ "$APP_URL_VALUE" == *"public_html"* ]] || [[ "$APP_URL_VALUE" == *"/" ]] || [[ "$APP_URL_VALUE" != *"iwacu.org"* ]]; then
        echo "❌ APP_URL is incorrect, fixing..."
        # Backup original
        cp .env .env.backup-$(date +%Y%m%d-%H%M%S)
        # Fix it
        sed -i "s|^APP_URL=.*|APP_URL=https://iwacu.org|g" .env
        echo "✅ APP_URL fixed to https://iwacu.org"
    else
        echo "✅ APP_URL looks correct"
    fi
else
    echo "⚠️  APP_URL not set, adding..."
    echo "" >> .env
    echo "APP_URL=https://iwacu.org" >> .env
    echo "✅ APP_URL added"
fi

# 2. Remove/check ASSET_URL
echo ""
echo "📋 Step 2: Checking ASSET_URL..."
if grep -q "^ASSET_URL=" .env; then
    ASSET_URL_VALUE=$(grep "^ASSET_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
    if [[ "$ASSET_URL_VALUE" == *"/home"* ]] || [[ "$ASSET_URL_VALUE" == *"public_html"* ]]; then
        echo "❌ ASSET_URL is incorrect: $ASSET_URL_VALUE"
        echo "🔧 Removing incorrect ASSET_URL..."
        sed -i "/^ASSET_URL=/d" .env
        echo "✅ ASSET_URL removed"
    fi
else
    echo "✅ ASSET_URL not set (will use APP_URL)"
fi

# 3. Check for any other URL-related config
echo ""
echo "📋 Step 3: Checking for other URL-related config..."
grep -E "^.*URL=" .env | grep -v "APP_URL" | grep -v "ASSET_URL" | grep -v "MAIL_" | grep -v "DB_" || echo "✅ No other URL config found"

# 4. Clear ALL Laravel caches
echo ""
echo "📋 Step 4: Clearing ALL Laravel caches..."
php artisan config:clear 2>&1 || true
php artisan cache:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true

# Clear bootstrap cache
echo "🧹 Clearing bootstrap cache..."
rm -rf bootstrap/cache/*.php 2>/dev/null || true

# Clear storage framework cache
echo "🧹 Clearing storage framework cache..."
rm -rf storage/framework/cache/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true

# 5. Verify APP_URL is correct before caching
echo ""
echo "📋 Step 5: Verifying APP_URL before caching..."
APP_URL_VERIFY=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
if [[ "$APP_URL_VERIFY" != "https://iwacu.org" ]]; then
    echo "❌ APP_URL is still wrong: $APP_URL_VERIFY"
    echo "🔧 Forcing APP_URL to https://iwacu.org..."
    sed -i "s|^APP_URL=.*|APP_URL=https://iwacu.org|g" .env
    echo "✅ APP_URL forced to https://iwacu.org"
fi

# Rebuild config cache with correct APP_URL
echo ""
echo "📋 Step 6: Rebuilding config cache..."
php artisan config:cache 2>&1 || echo "⚠️  Config cache failed (might be okay)"

# 6. Verify the fix
echo ""
echo "📋 Step 6: Verifying fix..."
if grep -q "^APP_URL=https://iwacu.org" .env; then
    echo "✅ APP_URL is correctly set to https://iwacu.org"
else
    echo "❌ APP_URL still incorrect!"
    echo "📄 Current value:"
    grep "^APP_URL=" .env
fi

# 7. Check Laravel config
echo ""
echo "📋 Step 7: Checking Laravel config..."
php artisan tinker --execute="echo config('app.url');" 2>&1 | tail -1 || echo "⚠️  Could not check config"

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  IMPORTANT:"
echo "   1. Test iwacu.org in your browser now"
echo "   2. Clear your browser cache (Cmd+Shift+R or Ctrl+Shift+R)"
echo "   3. If it still redirects, check:"
echo "      - tail -50 ~/private/karahanyuze-spotify/storage/logs/laravel.log"
echo "      - Verify .env: grep '^APP_URL=' ~/private/karahanyuze-spotify/.env"

