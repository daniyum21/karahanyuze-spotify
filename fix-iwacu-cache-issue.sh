#!/bin/bash

# Fix cache issue for iwacu.org - clear ALL caches including views
# Run this on the server: bash fix-iwacu-cache-issue.sh

echo "🔧 Fixing iwacu.org cache issue..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Verify APP_URL is correct
echo "📋 Step 1: Verifying APP_URL..."
if grep -q "^APP_URL=https://iwacu.org" .env; then
    echo "✅ APP_URL is correct: https://iwacu.org"
else
    echo "❌ APP_URL is wrong, fixing..."
    sed -i "s|^APP_URL=.*|APP_URL=https://iwacu.org|g" .env
    echo "✅ APP_URL fixed"
fi

# 2. Clear ALL Laravel caches
echo ""
echo "📋 Step 2: Clearing ALL Laravel caches..."
php artisan config:clear 2>&1 || true
php artisan cache:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true

# 3. Remove ALL cached files manually
echo ""
echo "📋 Step 3: Removing ALL cached files..."
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes.php 2>/dev/null || true
rm -f bootstrap/cache/routes-v7.php 2>/dev/null || true
rm -f bootstrap/cache/services.php 2>/dev/null || true
rm -rf storage/framework/cache/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true

# 4. Test Laravel config
echo ""
echo "📋 Step 4: Testing Laravel config..."
APP_URL_TEST=$(php artisan tinker --execute="echo config('app.url');" 2>&1 | grep -v "Psy" | tail -1 | tr -d ' ')
echo "📄 Laravel config('app.url'): $APP_URL_TEST"

if [[ "$APP_URL_TEST" == *"iwacu.org"* ]] && [[ "$APP_URL_TEST" != *"/home"* ]] && [[ "$APP_URL_TEST" != *"public_html"* ]]; then
    echo "✅ Laravel config looks correct"
else
    echo "❌ Laravel config still has wrong URL!"
    echo "🔧 Clearing config cache again..."
    rm -f bootstrap/cache/config.php 2>/dev/null || true
    php artisan config:clear 2>&1 || true
fi

# 5. Rebuild config cache with correct URL
echo ""
echo "📋 Step 5: Rebuilding config cache..."
php artisan config:cache 2>&1 || echo "⚠️  Config cache failed (might be okay)"

# 6. Verify again
echo ""
echo "📋 Step 6: Final verification..."
APP_URL_FINAL=$(php artisan tinker --execute="echo config('app.url');" 2>&1 | grep -v "Psy" | tail -1 | tr -d ' ')
echo "📄 Final Laravel config('app.url'): $APP_URL_FINAL"

if [[ "$APP_URL_FINAL" == *"iwacu.org"* ]] && [[ "$APP_URL_FINAL" != *"/home"* ]]; then
    echo "✅ Config is correct!"
else
    echo "❌ Config still wrong!"
fi

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  IMPORTANT:"
echo "   1. Clear your browser cache completely (Ctrl+Shift+Delete)"
echo "   2. Or use incognito/private browsing mode"
echo "   3. Test iwacu.org again"
echo "   4. If it still redirects, check browser console for JavaScript errors"

