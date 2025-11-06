#!/bin/bash

# Fix CSS/JS assets and placeholder.svg 404 errors
# Run this on the server: bash fix-iwacu-assets-404.sh

echo "🔧 Fixing asset 404 errors..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Check if asset files exist
echo "📋 Step 1: Checking asset files..."
if [ -d "public/build/assets" ]; then
    echo "✅ public/build/assets exists"
    ASSET_FILES=$(find public/build/assets -type f | wc -l)
    echo "   Found $ASSET_FILES asset files"
    
    # Check for specific files mentioned in error
    if [ -f "public/build/assets/app-BySXMQJt.css" ]; then
        echo "   ✅ app-BySXMQJt.css exists"
    else
        echo "   ❌ app-BySXMQJt.css NOT found"
        echo "   Checking what CSS files exist:"
        find public/build/assets -name "*.css" | head -5
    fi
    
    if [ -f "public/build/assets/app-CvgioS1y.js" ]; then
        echo "   ✅ app-CvgioS1y.js exists"
    else
        echo "   ❌ app-CvgioS1y.js NOT found"
        echo "   Checking what JS files exist:"
        find public/build/assets -name "*.js" | head -5
    fi
else
    echo "❌ public/build/assets does NOT exist!"
fi
echo ""

# 2. Check if assets are in public_html/iwacu
echo "📋 Step 2: Checking assets in public_html/iwacu..."
if [ -d ~/public_html/iwacu/build/assets ]; then
    echo "✅ public_html/iwacu/build/assets exists"
    ASSET_FILES=$(find ~/public_html/iwacu/build/assets -type f | wc -l)
    echo "   Found $ASSET_FILES asset files"
    
    # Check for specific files
    if [ -f ~/public_html/iwacu/build/assets/app-BySXMQJt.css ]; then
        echo "   ✅ app-BySXMQJt.css exists in public_html/iwacu"
    else
        echo "   ❌ app-BySXMQJt.css NOT found in public_html/iwacu"
    fi
    
    if [ -f ~/public_html/iwacu/build/assets/app-CvgioS1y.js ]; then
        echo "   ✅ app-CvgioS1y.js exists in public_html/iwacu"
    else
        echo "   ❌ app-CvgioS1y.js NOT found in public_html/iwacu"
    fi
else
    echo "❌ public_html/iwacu/build/assets does NOT exist!"
    echo "   Need to copy assets from private app"
fi
echo ""

# 3. Sync assets if needed
echo "📋 Step 3: Syncing assets if needed..."
if [ -d "public/build/assets" ] && [ ! -d ~/public_html/iwacu/build/assets ]; then
    echo "⚠️  Assets missing in public_html/iwacu, copying..."
    mkdir -p ~/public_html/iwacu/build/assets
    cp -r public/build/assets/* ~/public_html/iwacu/build/assets/
    echo "✅ Assets copied"
elif [ -d "public/build/assets" ] && [ -d ~/public_html/iwacu/build/assets ]; then
    echo "⚠️  Syncing assets..."
    rsync -av public/build/assets/ ~/public_html/iwacu/build/assets/
    echo "✅ Assets synced"
fi
echo ""

# 4. Check placeholder.svg
echo "📋 Step 4: Checking placeholder.svg..."
if [ -f "public/placeholder.svg" ]; then
    echo "✅ placeholder.svg exists in public/"
    if [ -f ~/public_html/iwacu/placeholder.svg ]; then
        echo "   ✅ placeholder.svg exists in public_html/iwacu"
    else
        echo "   ⚠️  placeholder.svg missing in public_html/iwacu, copying..."
        cp public/placeholder.svg ~/public_html/iwacu/
        echo "   ✅ placeholder.svg copied"
    fi
else
    echo "⚠️  placeholder.svg not found in public/"
    echo "   Creating a simple placeholder..."
    cat > ~/public_html/iwacu/placeholder.svg << 'EOF'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="#e5e7eb"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="14" fill="#9ca3af" text-anchor="middle" dominant-baseline="middle">No Image</text>
</svg>
EOF
    echo "   ✅ placeholder.svg created"
fi
echo ""

# 5. Test HTTP access
echo "📋 Step 5: Testing HTTP access..."
if command -v curl &> /dev/null; then
    # Test CSS file
    if [ -f ~/public_html/iwacu/build/assets/app-BySXMQJt.css ]; then
        echo "Testing https://iwacu.org/build/assets/app-BySXMQJt.css..."
        RESPONSE=$(curl -I -s "https://iwacu.org/build/assets/app-BySXMQJt.css" | head -3)
        if echo "$RESPONSE" | grep -q "200 OK"; then
            echo "✅ CSS file is accessible!"
        else
            echo "❌ CSS file is still NOT accessible"
            echo "   Response: $RESPONSE"
        fi
    fi
    
    # Test JS file
    if [ -f ~/public_html/iwacu/build/assets/app-CvgioS1y.js ]; then
        echo ""
        echo "Testing https://iwacu.org/build/assets/app-CvgioS1y.js..."
        RESPONSE=$(curl -I -s "https://iwacu.org/build/assets/app-CvgioS1y.js" | head -3)
        if echo "$RESPONSE" | grep -q "200 OK"; then
            echo "✅ JS file is accessible!"
        else
            echo "❌ JS file is still NOT accessible"
            echo "   Response: $RESPONSE"
        fi
    fi
    
    # Test placeholder.svg
    echo ""
    echo "Testing https://iwacu.org/placeholder.svg..."
    RESPONSE=$(curl -I -s "https://iwacu.org/placeholder.svg" | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ placeholder.svg is accessible!"
    else
        echo "❌ placeholder.svg is still NOT accessible"
        echo "   Response: $RESPONSE"
    fi
else
    echo "⚠️  curl not available"
fi

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  If assets still don't load:"
echo "   1. Check file permissions: ls -la ~/public_html/iwacu/build/assets/"
echo "   2. Verify .htaccess allows /build/ access"
echo "   3. Clear browser cache (Ctrl+Shift+Delete)"

