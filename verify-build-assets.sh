#!/bin/bash

# Verify build assets are being deployed correctly
# Run this on the server: bash verify-build-assets.sh

echo "🔍 Verifying build assets deployment..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Check if build directory exists in private app
echo "📋 Step 1: Checking if build directory exists in private app..."
if [ -d "public/build" ]; then
    echo "✅ public/build exists in private app"
    BUILD_FILES=$(find public/build -type f | wc -l)
    echo "   Found $BUILD_FILES files in public/build"
    
    if [ -f "public/build/manifest.json" ]; then
        echo "✅ manifest.json exists"
        echo "   Manifest content:"
        cat public/build/manifest.json | head -20
    else
        echo "❌ manifest.json not found!"
    fi
else
    echo "❌ public/build does NOT exist in private app!"
    echo "   ⚠️  Assets may not have been built correctly"
fi
echo ""

# 2. Check if build directory exists in public_html/iwacu
echo "📋 Step 2: Checking if build directory exists in public_html/iwacu..."
if [ -d ~/public_html/iwacu/build ]; then
    echo "✅ build directory exists in public_html/iwacu"
    BUILD_FILES=$(find ~/public_html/iwacu/build -type f | wc -l)
    echo "   Found $BUILD_FILES files in public_html/iwacu/build"
    
    if [ -f ~/public_html/iwacu/build/manifest.json ]; then
        echo "✅ manifest.json exists in public_html/iwacu/build"
        echo "   Manifest content:"
        cat ~/public_html/iwacu/build/manifest.json | head -20
    else
        echo "❌ manifest.json not found in public_html/iwacu/build!"
    fi
else
    echo "❌ build directory does NOT exist in public_html/iwacu!"
    echo "   ⚠️  Assets were not copied correctly"
fi
echo ""

# 3. Compare build directories
echo "📋 Step 3: Comparing build directories..."
if [ -d "public/build" ] && [ -d ~/public_html/iwacu/build ]; then
    PRIVATE_COUNT=$(find public/build -type f | wc -l)
    PUBLIC_COUNT=$(find ~/public_html/iwacu/build -type f | wc -l)
    
    if [ "$PRIVATE_COUNT" -eq "$PUBLIC_COUNT" ]; then
        echo "✅ Both directories have same number of files ($PRIVATE_COUNT)"
    else
        echo "⚠️  File count mismatch:"
        echo "   private app: $PRIVATE_COUNT files"
        echo "   public_html/iwacu: $PUBLIC_COUNT files"
    fi
else
    echo "⚠️  Cannot compare - one or both directories missing"
fi
echo ""

# 4. Test HTTP access to assets
echo "📋 Step 4: Testing HTTP access to assets..."
if command -v curl &> /dev/null; then
    # Test manifest.json
    echo "Testing https://iwacu.org/build/manifest.json..."
    RESPONSE=$(curl -I -s https://iwacu.org/build/manifest.json | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ manifest.json is accessible"
    else
        echo "❌ manifest.json is NOT accessible"
        echo "   Response: $RESPONSE"
    fi
    
    # Test a specific asset file if manifest exists
    if [ -f ~/public_html/iwacu/build/manifest.json ]; then
        CSS_FILE=$(grep -o '"resources/css/app.css":{"file":"[^"]*"' ~/public_html/iwacu/build/manifest.json | cut -d'"' -f6 | head -1)
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

# 5. If build directory is missing in public_html, copy it
echo "📋 Step 5: Fixing missing build directory (if needed)..."
if [ -d "public/build" ] && [ ! -d ~/public_html/iwacu/build ]; then
    echo "⚠️  build directory missing in public_html/iwacu, copying..."
    cp -r public/build ~/public_html/iwacu/
    echo "✅ Build directory copied"
elif [ -d "public/build" ] && [ -d ~/public_html/iwacu/build ]; then
    echo "✅ Both directories exist, syncing..."
    rsync -av public/build/ ~/public_html/iwacu/build/
    echo "✅ Build directory synced"
fi

echo ""
echo "✅ Verification complete!"

