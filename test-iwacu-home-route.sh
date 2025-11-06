#!/bin/bash

# Test what Laravel returns for the home route
# This helps diagnose if the redirect is coming from Laravel or browser cache
# Run this on the server: bash test-iwacu-home-route.sh

echo "🧪 Testing iwacu.org home route response..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Test route generation
echo "📋 Step 1: Testing route generation..."
echo "route('home'):"
php artisan tinker --execute="echo route('home');" 2>&1 | grep -v "Psy" | tail -1
echo ""

# 2. Test URL helper
echo "📋 Step 2: Testing URL helper..."
echo "url('/'):"
php artisan tinker --execute="echo url('/');" 2>&1 | grep -v "Psy" | tail -1
echo ""

# 3. Test asset helper
echo "📋 Step 3: Testing asset helper..."
echo "asset('/test.css'):"
php artisan tinker --execute="echo asset('/test.css');" 2>&1 | grep -v "Psy" | tail -1
echo ""

# 4. Test what the HomeController returns
echo "📋 Step 4: Testing HomeController response..."
echo "Testing HomeController::index() response..."
php artisan tinker --execute="
\$response = app(App\Http\Controllers\HomeController::class)->index();
echo 'Response type: ' . get_class(\$response) . PHP_EOL;
if (method_exists(\$response, 'getStatusCode')) {
    echo 'Status code: ' . \$response->getStatusCode() . PHP_EOL;
}
if (method_exists(\$response, 'headers')) {
    \$headers = \$response->headers->all();
    if (isset(\$headers['location'])) {
        echo '⚠️  Found Location header (redirect): ' . implode(', ', \$headers['location']) . PHP_EOL;
    } else {
        echo '✅ No Location header found (not a redirect)' . PHP_EOL;
    }
}
" 2>&1 | grep -v "Psy"
echo ""

# 5. Test actual HTTP request (if curl is available)
echo "📋 Step 5: Testing actual HTTP request..."
if command -v curl &> /dev/null; then
    echo "Testing curl request to https://iwacu.org/..."
    curl -I -s -L https://iwacu.org/ | head -20
    echo ""
    echo "Checking for redirects in response:"
    curl -I -s -L https://iwacu.org/ | grep -i "location\|redirect" || echo "No redirect headers found"
    echo ""
else
    echo "⚠️  curl not available, skipping HTTP test"
fi

# 6. Check if there's a redirect in .htaccess
echo "📋 Step 6: Checking .htaccess for any redirect rules..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "Checking ~/public_html/iwacu/.htaccess:"
    if grep -q "RewriteRule.*\[R" ~/public_html/iwacu/.htaccess; then
        echo "⚠️  Found redirect rules:"
        grep "RewriteRule.*\[R" ~/public_html/iwacu/.htaccess
    else
        echo "✅ No redirect rules found in .htaccess"
    fi
    echo ""
fi

# 7. Check index.php file
echo "📋 Step 7: Checking index.php file..."
if [ -f ~/public_html/iwacu/index.php ]; then
    echo "✅ index.php exists"
    echo "Checking for any redirects or incorrect paths:"
    if grep -q "header.*Location\|redirect\|Location:" ~/public_html/iwacu/index.php; then
        echo "⚠️  Found redirect code in index.php:"
        grep -i "header.*Location\|redirect\|Location:" ~/public_html/iwacu/index.php
    else
        echo "✅ No redirect code found in index.php"
    fi
    
    echo "Checking paths in index.php:"
    if grep -q "/home4/biriheco\|public_html" ~/public_html/iwacu/index.php; then
        echo "⚠️  Found file paths in index.php:"
        grep "/home4/biriheco\|public_html" ~/public_html/iwacu/index.php
    else
        echo "✅ No file paths found in index.php"
    fi
    echo ""
else
    echo "❌ index.php not found in ~/public_html/iwacu/"
    echo ""
fi

echo "✅ Test complete!"
echo ""
echo "⚠️  IMPORTANT: If Laravel shows no redirects but browser still redirects:"
echo "   1. Clear browser cache completely (Ctrl+Shift+Delete)"
echo "   2. Try incognito/private browsing mode"
echo "   3. Check browser console for JavaScript errors"
echo "   4. Check browser Network tab to see the actual redirect source"

