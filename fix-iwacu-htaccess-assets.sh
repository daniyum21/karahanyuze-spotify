#!/bin/bash

# Fix .htaccess to properly allow access to build directory and static files
# Run this on the server: bash fix-iwacu-htaccess-assets.sh

echo "🔧 Fixing .htaccess to allow access to assets..."
echo ""

cd ~/public_html/iwacu

# 1. Backup current .htaccess
echo "📋 Step 1: Backing up current .htaccess..."
if [ -f ".htaccess" ]; then
    cp .htaccess .htaccess.backup-assets-fix-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backup created"
else
    echo "❌ .htaccess not found!"
    exit 1
fi
echo ""

# 2. Check current .htaccess
echo "📋 Step 2: Checking current .htaccess..."
if grep -q "RewriteCond.*build" .htaccess 2>/dev/null; then
    echo "⚠️  Found build-related rules:"
    grep "RewriteCond.*build\|RewriteRule.*build" .htaccess
else
    echo "⚠️  No build-related rules found!"
fi
echo ""

# 3. Create new .htaccess with proper asset access
echo "📋 Step 3: Creating new .htaccess with proper asset access..."
cat > .htaccess << 'HTACCESS_EOF'
<IfModule mod_headers.c>
    # Prevent browsers from caching redirects
    Header set Cache-Control "no-cache, no-store, must-revalidate, max-age=0"
    Header set Pragma "no-cache"
    Header set Expires "0"
</IfModule>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # CRITICAL: Disable directory redirects (prevents Apache from redirecting / to /iwacu/)
    DirectorySlash Off

    # Disable directory browsing
    Options -Indexes

    # IMPORTANT: Allow direct access to build directory and static files
    # This must come BEFORE other rewrite rules
    # Match both /build/ and build/ (for subdirectory)
    RewriteCond %{REQUEST_URI} ^/build/ [OR]
    RewriteCond %{REQUEST_URI} ^build/
    RewriteRule ^ - [L]

    # Allow direct access to static files (svg, png, jpg, jpeg, gif, ico, etc.)
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteCond %{REQUEST_URI} \.(svg|png|jpg|jpeg|gif|ico|css|js|woff|woff2|ttf|eot)$ [NC]
    RewriteRule ^ - [L]

    # Prevent access to hidden files and parent directories
    RewriteCond %{REQUEST_URI} "\.\." [OR]
    RewriteCond %{REQUEST_URI} "\.\./" [OR]
    RewriteCond %{REQUEST_URI} "/home" [OR]
    RewriteCond %{REQUEST_URI} "/private"
    RewriteRule .* - [F,L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # IMPORTANT: Do not redirect trailing slashes - let Laravel handle it
    # This prevents Apache from redirecting directories and exposing file paths

    # Send Requests To Front Controller...
    # Only process non-files and non-directories
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS_EOF

echo "✅ New .htaccess created with proper asset access"
echo ""

# 4. Check file permissions
echo "📋 Step 4: Checking file permissions..."
if [ -f "build/assets/app-BySXMQJt.css" ]; then
    PERMS=$(stat -c "%a" build/assets/app-BySXMQJt.css 2>/dev/null || stat -f "%OLp" build/assets/app-BySXMQJt.css 2>/dev/null || echo "unknown")
    echo "   CSS file permissions: $PERMS"
    if [ "$PERMS" != "644" ] && [ "$PERMS" != "755" ]; then
        echo "   ⚠️  Setting permissions to 644..."
        chmod 644 build/assets/*.css build/assets/*.js 2>/dev/null || true
    fi
fi

if [ -f "placeholder.svg" ]; then
    PERMS=$(stat -c "%a" placeholder.svg 2>/dev/null || stat -f "%OLp" placeholder.svg 2>/dev/null || echo "unknown")
    echo "   placeholder.svg permissions: $PERMS"
    if [ "$PERMS" != "644" ] && [ "$PERMS" != "755" ]; then
        echo "   ⚠️  Setting permissions to 644..."
        chmod 644 placeholder.svg 2>/dev/null || true
    fi
fi
echo ""

# 5. Test HTTP access
echo "📋 Step 5: Testing HTTP access..."
if command -v curl &> /dev/null; then
    echo "Testing https://iwacu.org/build/assets/app-BySXMQJt.css..."
    RESPONSE=$(curl -I -s "https://iwacu.org/build/assets/app-BySXMQJt.css" | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ CSS file is now accessible!"
    else
        echo "❌ CSS file is still NOT accessible"
        echo "   Response: $RESPONSE"
    fi
    
    echo ""
    echo "Testing https://iwacu.org/build/assets/app-CvgioS1y.js..."
    RESPONSE=$(curl -I -s "https://iwacu.org/build/assets/app-CvgioS1y.js" | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ JS file is now accessible!"
    else
        echo "❌ JS file is still NOT accessible"
        echo "   Response: $RESPONSE"
    fi
    
    echo ""
    echo "Testing https://iwacu.org/placeholder.svg..."
    RESPONSE=$(curl -I -s "https://iwacu.org/placeholder.svg" | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ placeholder.svg is now accessible!"
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
echo "   1. Check Apache error log: tail -20 ~/logs/error_log"
echo "   2. Verify file permissions: ls -la ~/public_html/iwacu/build/assets/"
echo "   3. Test direct file access: curl -I https://iwacu.org/build/assets/app-BySXMQJt.css"

