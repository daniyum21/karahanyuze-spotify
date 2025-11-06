#!/bin/bash

# Fix HTTP 500 error on iwacu.org
# This restores a working .htaccess if DirectorySlash Off is causing issues
# Run this on the server: bash fix-iwacu-500-error.sh

echo "🔧 Fixing HTTP 500 error on iwacu.org..."
echo ""

cd ~/public_html/iwacu

# 1. Check if .htaccess exists
if [ ! -f ".htaccess" ]; then
    echo "❌ .htaccess not found!"
    exit 1
fi

# 2. Backup current .htaccess
echo "📋 Step 1: Backing up current .htaccess..."
cp .htaccess .htaccess.error-backup-$(date +%Y%m%d-%H%M%S)
echo "✅ Backup created"
echo ""

# 3. Try removing DirectorySlash Off (it might not be supported)
echo "📋 Step 2: Removing DirectorySlash Off (may not be supported)..."
if grep -q "DirectorySlash Off" .htaccess; then
    # Remove DirectorySlash Off line
    sed -i '/DirectorySlash Off/d' .htaccess
    echo "✅ Removed DirectorySlash Off"
    
    # Test if Apache can parse the .htaccess now
    echo "📋 Step 3: Testing .htaccess syntax..."
    if apachectl configtest 2>&1 | grep -q "Syntax OK"; then
        echo "✅ Apache syntax OK"
    else
        echo "⚠️  Could not test Apache syntax (apachectl may not be available)"
    fi
else
    echo "✅ DirectorySlash Off not found (already removed or never added)"
fi
echo ""

# 4. Create a minimal working .htaccess
echo "📋 Step 4: Creating minimal working .htaccess..."
cat > .htaccess << 'HTACCESS_EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Disable directory browsing
    Options -Indexes

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

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS_EOF

echo "✅ Created minimal .htaccess"
echo ""

# 5. Test the site
echo "📋 Step 5: Testing site..."
if command -v curl &> /dev/null; then
    echo "Testing https://iwacu.org/..."
    RESPONSE=$(curl -I -s https://iwacu.org/ | head -3)
    echo "$RESPONSE"
    echo ""
    
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ Site is working!"
    elif echo "$RESPONSE" | grep -q "500"; then
        echo "❌ Still getting 500 error"
        echo "   Check Laravel error log: tail -50 ~/private/karahanyuze-spotify/storage/logs/laravel.log"
    else
        echo "⚠️  Got response: $(echo "$RESPONSE" | head -1)"
    fi
else
    echo "⚠️  curl not available for testing"
fi

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  If the site still doesn't work:"
echo "   1. Run: bash diagnose-iwacu-500-error.sh"
echo "   2. Check Laravel log: tail -50 ~/private/karahanyuze-spotify/storage/logs/laravel.log"
echo "   3. Restore backup: cp .htaccess.error-backup-* .htaccess"

