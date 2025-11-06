#!/bin/bash

# Fix .htaccess to allow access to build directory
# Run this on the server: bash fix-iwacu-build-access.sh

echo "🔧 Fixing .htaccess to allow access to build directory..."
echo ""

cd ~/public_html/iwacu

# 1. Backup current .htaccess
echo "📋 Step 1: Backing up current .htaccess..."
if [ -f ".htaccess" ]; then
    cp .htaccess .htaccess.backup-build-fix-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backup created"
else
    echo "❌ .htaccess not found!"
    exit 1
fi
echo ""

# 2. Check current .htaccess
echo "📋 Step 2: Checking current .htaccess..."
if grep -q "RewriteRule.*build" .htaccess 2>/dev/null; then
    echo "⚠️  Found build-related rules:"
    grep "RewriteRule.*build" .htaccess
else
    echo "✅ No build-blocking rules found"
fi
echo ""

# 3. Create new .htaccess with proper build directory access
echo "📋 Step 3: Creating new .htaccess with build directory access..."
cat > .htaccess << 'HTACCESS_EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # CRITICAL: Disable directory redirects (prevents Apache from redirecting / to /iwacu/)
    DirectorySlash Off

    # Disable directory browsing
    Options -Indexes

    # IMPORTANT: Allow direct access to build directory and assets
    # This prevents Laravel rewrite rules from blocking static assets
    RewriteCond %{REQUEST_URI} ^/build/
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
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS_EOF

echo "✅ New .htaccess created with build directory access"
echo ""

# 4. Test HTTP access
echo "📋 Step 4: Testing HTTP access to build directory..."
if command -v curl &> /dev/null; then
    echo "Testing https://iwacu.org/build/manifest.json..."
    RESPONSE=$(curl -I -s https://iwacu.org/build/manifest.json | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ manifest.json is now accessible!"
    else
        echo "❌ manifest.json is still NOT accessible"
        echo "   Response: $RESPONSE"
        
        # Try testing a CSS file
        if [ -f "build/assets/app-BySXMQJt.css" ]; then
            echo ""
            echo "Testing CSS file: https://iwacu.org/build/assets/app-BySXMQJt.css..."
            RESPONSE=$(curl -I -s "https://iwacu.org/build/assets/app-BySXMQJt.css" | head -3)
            if echo "$RESPONSE" | grep -q "200 OK"; then
                echo "✅ CSS file is accessible!"
            else
                echo "❌ CSS file is still NOT accessible"
                echo "   Response: $RESPONSE"
            fi
        fi
    fi
else
    echo "⚠️  curl not available for testing"
fi

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  If assets still don't load:"
echo "   1. Check file permissions: ls -la ~/public_html/iwacu/build/"
echo "   2. Check Apache error log: tail -20 ~/logs/error_log"
echo "   3. Verify DirectorySlash Off is supported (if 500 error, remove it)"

