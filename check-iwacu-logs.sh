#!/bin/bash

# Check error logs for iwacu.org issues
# Run this on the server: bash check-iwacu-logs.sh

echo "🔍 Checking error logs for iwacu.org..."
echo ""

# Check common error log locations
echo "📋 Checking common error log locations:"
echo ""

# 1. User's home logs directory
if [ -f ~/logs/error_log ]; then
    echo "✅ Found: ~/logs/error_log"
    echo "   Last 20 lines related to iwacu:"
    tail -20 ~/logs/error_log | grep -i iwacu || echo "   (No iwacu entries found)"
    echo ""
fi

# 2. public_html error log
if [ -f ~/public_html/error_log ]; then
    echo "✅ Found: ~/public_html/error_log"
    echo "   Last 20 lines related to iwacu:"
    tail -20 ~/public_html/error_log | grep -i iwacu || echo "   (No iwacu entries found)"
    echo ""
fi

# 3. public_html/iwacu error log
if [ -f ~/public_html/iwacu/error_log ]; then
    echo "✅ Found: ~/public_html/iwacu/error_log"
    echo "   Last 20 lines related to iwacu:"
    tail -20 ~/public_html/iwacu/error_log | grep -i iwacu || echo "   (No iwacu entries found)"
    echo ""
fi

# 4. Apache access log
if [ -f ~/logs/access_log ]; then
    echo "✅ Found: ~/logs/access_log"
    echo "   Last 10 iwacu.org requests:"
    tail -100 ~/logs/access_log | grep -i iwacu | tail -10 || echo "   (No iwacu entries found)"
    echo ""
fi

# 5. Laravel log
if [ -f ~/private/karahanyuze-spotify/storage/logs/laravel.log ]; then
    echo "✅ Found: ~/private/karahanyuze-spotify/storage/logs/laravel.log"
    echo "   Last 20 lines:"
    tail -20 ~/private/karahanyuze-spotify/storage/logs/laravel.log | grep -v "^$" || echo "   (Log is empty)"
    echo ""
fi

# 6. Check PHP error log
if [ -f ~/public_html/iwacu/error_log ]; then
    echo "✅ Found PHP error log: ~/public_html/iwacu/error_log"
    echo "   Last 20 lines:"
    tail -20 ~/public_html/iwacu/error_log || echo "   (No errors found)"
    echo ""
fi

# 7. Check Apache error log (if accessible)
if [ -f /usr/local/apache/logs/error_log ]; then
    echo "✅ Found: /usr/local/apache/logs/error_log"
    echo "   Last 10 lines related to iwacu:"
    tail -100 /usr/local/apache/logs/error_log | grep -i iwacu | tail -10 || echo "   (No iwacu entries found)"
    echo ""
fi

# 8. Check if there are any redirects in .htaccess
echo "📋 Checking .htaccess files for redirects:"
echo ""
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ Found: ~/public_html/iwacu/.htaccess"
    if grep -q "RewriteRule.*\[R=" ~/public_html/iwacu/.htaccess; then
        echo "⚠️  Found redirect rules:"
        grep "RewriteRule.*\[R=" ~/public_html/iwacu/.htaccess
    else
        echo "✅ No redirect rules found"
    fi
    echo ""
fi

# 9. Test URL generation
echo "📋 Testing Laravel URL generation:"
echo ""
cd ~/private/karahanyuze-spotify
if [ -f .env ]; then
    echo "APP_URL from .env:"
    grep "^APP_URL=" .env || echo "   (Not set)"
    echo ""
    echo "Laravel config('app.url'):"
    php artisan tinker --execute="echo config('app.url');" 2>&1 | grep -v "Psy" | tail -1
    echo ""
    echo "route('home'):"
    php artisan tinker --execute="echo route('home');" 2>&1 | grep -v "Psy" | tail -1
    echo ""
fi

echo "✅ Log check complete!"

