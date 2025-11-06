#!/bin/bash

# Fix iwacu.org redirect issue - clear ALL compiled views and force recompilation
# This fixes issues where Blade views are compiled with old URLs
# Run this on the server: bash fix-iwacu-views-redirect.sh

echo "🔧 Fixing iwacu.org redirect by clearing compiled views..."
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

# 3. Remove ALL compiled views (CRITICAL - this is often the issue)
echo ""
echo "📋 Step 3: Removing ALL compiled views..."
rm -rf storage/framework/views/* 2>/dev/null || true
# Also remove any cached compiled views
find storage/framework/views -name "*.php" -type f -delete 2>/dev/null || true
echo "✅ All compiled views removed"

# 4. Remove ALL cached config files
echo ""
echo "📋 Step 4: Removing ALL cached config files..."
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes.php 2>/dev/null || true
rm -f bootstrap/cache/routes-v7.php 2>/dev/null || true
rm -f bootstrap/cache/services.php 2>/dev/null || true
rm -rf storage/framework/cache/* 2>/dev/null || true

# 5. Clear session cache (in case session has old URLs)
echo ""
echo "📋 Step 5: Clearing session cache..."
rm -rf storage/framework/sessions/* 2>/dev/null || true
echo "✅ Session cache cleared"

# 6. Verify APP_URL before rebuilding
echo ""
echo "📋 Step 6: Verifying APP_URL before rebuilding..."
APP_URL_VERIFY=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
if [[ "$APP_URL_VERIFY" != "https://iwacu.org" ]]; then
    echo "❌ APP_URL is still wrong: $APP_URL_VERIFY"
    echo "🔧 Forcing APP_URL to https://iwacu.org..."
    sed -i "s|^APP_URL=.*|APP_URL=https://iwacu.org|g" .env
    echo "✅ APP_URL forced"
fi

# 7. Rebuild config cache with correct URL
echo ""
echo "📋 Step 7: Rebuilding config cache..."
php artisan config:cache 2>&1 || echo "⚠️  Config cache failed (might be okay)"

# 8. Test URL generation
echo ""
echo "📋 Step 8: Testing URL generation..."
php artisan tinker --execute="echo 'APP_URL: ' . config('app.url') . PHP_EOL; echo 'Route home: ' . route('home') . PHP_EOL; echo 'Asset test: ' . asset('/test.css') . PHP_EOL;" 2>&1 | grep -v "Psy" | tail -3

# 9. Check .htaccess in iwacu
echo ""
echo "📋 Step 9: Checking .htaccess in public_html/iwacu..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ .htaccess exists"
    # Check for any redirect rules
    if grep -q "RewriteRule.*\[R=" ~/public_html/iwacu/.htaccess; then
        echo "⚠️  Found redirect rules in .htaccess:"
        grep "RewriteRule.*\[R=" ~/public_html/iwacu/.htaccess
    else
        echo "✅ No redirect rules found in .htaccess"
    fi
else
    echo "⚠️  .htaccess not found in public_html/iwacu"
fi

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  IMPORTANT NEXT STEPS:"
echo "   1. Test iwacu.org in your browser (use incognito/private mode)"
echo "   2. Clear browser cache completely (Ctrl+Shift+Delete)"
echo "   3. Check browser console for any JavaScript errors"
echo "   4. Check Network tab in browser DevTools to see what redirects are happening"
echo "   5. If it still redirects, run: tail -f ~/logs/error_log | grep iwacu"

