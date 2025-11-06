#!/bin/bash

# Check asset URLs for iwacu.org
# Run this on the server: bash check-iwacu-assets.sh

echo "🔍 Checking asset URLs for iwacu.org..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Check if Vite assets exist
echo "📋 Step 1: Checking Vite assets..."
if [ -d "public/build" ]; then
    echo "✅ Vite build directory exists"
    BUILD_FILES=$(find public/build -type f | wc -l)
    echo "   Found $BUILD_FILES build files"
    if [ "$BUILD_FILES" -gt 0 ]; then
        echo "   Sample files:"
        find public/build -type f | head -5
    fi
else
    echo "❌ Vite build directory not found: public/build"
    echo "   ⚠️  CSS/JS files may not be built!"
fi
echo ""

# 2. Check if assets are copied to public_html/iwacu
echo "📋 Step 2: Checking assets in public_html/iwacu..."
if [ -d ~/public_html/iwacu/build ]; then
    echo "✅ Build directory exists in public_html/iwacu"
    BUILD_FILES=$(find ~/public_html/iwacu/build -type f | wc -l)
    echo "   Found $BUILD_FILES build files"
else
    echo "❌ Build directory not found in public_html/iwacu/build"
    echo "   ⚠️  Assets may not be accessible!"
fi
echo ""

# 3. Check .env for asset configuration
echo "📋 Step 3: Checking .env for asset configuration..."
if [ -f ".env" ]; then
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ')
        echo "   APP_URL=$APP_URL"
    fi
    
    if grep -q "^ASSET_URL=" .env; then
        ASSET_URL=$(grep "^ASSET_URL=" .env | cut -d '=' -f2- | tr -d ' ')
        echo "   ASSET_URL=$ASSET_URL"
    else
        echo "   ASSET_URL not set (will use APP_URL)"
    fi
    
    if grep -q "^VITE_" .env; then
        echo "   Vite configuration found:"
        grep "^VITE_" .env
    else
        echo "   ⚠️  No Vite configuration found"
    fi
else
    echo "❌ .env file not found!"
fi
echo ""

# 4. Test asset URL generation
echo "📋 Step 4: Testing asset URL generation..."
php artisan tinker --execute="
echo 'APP_URL: ' . config('app.url') . PHP_EOL;
echo 'asset(\"/test.css\"): ' . asset('/test.css') . PHP_EOL;
echo 'Vite manifest check:' . PHP_EOL;
if (file_exists(public_path('build/manifest.json'))) {
    echo '  ✅ manifest.json exists' . PHP_EOL;
    \$manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    if (\$manifest) {
        echo '  ✅ manifest.json is valid' . PHP_EOL;
        echo '  Found ' . count(\$manifest) . ' entries' . PHP_EOL;
    } else {
        echo '  ❌ manifest.json is invalid' . PHP_EOL;
    }
} else {
    echo '  ❌ manifest.json not found' . PHP_EOL;
}
" 2>&1 | grep -v "Psy"
echo ""

# 5. Check if Vite dev server is running (shouldn't be in production)
echo "📋 Step 5: Checking Vite configuration..."
if [ -f "vite.config.js" ]; then
    echo "✅ vite.config.js exists"
    if grep -q "server" vite.config.js; then
        echo "   Server configuration found"
        grep -A 5 "server" vite.config.js | head -10
    fi
else
    echo "❌ vite.config.js not found"
fi
echo ""

# 6. Test actual HTTP request to assets
echo "📋 Step 6: Testing HTTP request to assets..."
if command -v curl &> /dev/null; then
    # Test if manifest.json is accessible
    echo "Testing https://iwacu.org/build/manifest.json..."
    RESPONSE=$(curl -I -s https://iwacu.org/build/manifest.json | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ manifest.json is accessible"
    else
        echo "❌ manifest.json is NOT accessible"
        echo "   Response: $RESPONSE"
    fi
    
    # Test if CSS is accessible
    if [ -f ~/public_html/iwacu/build/manifest.json ]; then
        CSS_FILE=$(grep -o '"resources/css/app.css":"[^"]*"' ~/public_html/iwacu/build/manifest.json | cut -d'"' -f4 | head -1)
        if [ -n "$CSS_FILE" ]; then
            echo ""
            echo "Testing CSS file: https://iwacu.org/build/$CSS_FILE"
            RESPONSE=$(curl -I -s "https://iwacu.org/build/$CSS_FILE" | head -3)
            if echo "$RESPONSE" | grep -q "200 OK"; then
                echo "✅ CSS file is accessible"
            else
                echo "❌ CSS file is NOT accessible"
                echo "   Response: $RESPONSE"
            fi
        fi
    fi
else
    echo "⚠️  curl not available"
fi

echo ""
echo "✅ Asset check complete!"
echo ""
echo "⚠️  If assets are not loading:"
echo "   1. Run: npm run build (locally or on server)"
echo "   2. Ensure public/build/ is copied to public_html/iwacu/build/"
echo "   3. Check that APP_URL is set correctly in .env"

