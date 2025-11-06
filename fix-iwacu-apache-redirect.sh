#!/bin/bash

# Fix Apache redirect for iwacu.org
# The issue is Apache is redirecting / to /home4/biriheco/public_html/iwacu/%25%25
# This is an Apache-level redirect, not Laravel
# Run this on the server: bash fix-iwacu-apache-redirect.sh

echo "🔧 Fixing Apache redirect for iwacu.org..."
echo ""

# 1. Check .htaccess in public_html/iwacu
echo "📋 Step 1: Checking .htaccess in public_html/iwacu..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ Found .htaccess"
    echo "Current content:"
    cat ~/public_html/iwacu/.htaccess
    echo ""
else
    echo "❌ .htaccess not found, creating..."
    mkdir -p ~/public_html/iwacu
fi

# 2. Check if there's a DirectoryIndex issue
echo "📋 Step 2: Checking DirectoryIndex configuration..."
if grep -q "DirectoryIndex" ~/public_html/iwacu/.htaccess 2>/dev/null; then
    echo "⚠️  Found DirectoryIndex in .htaccess:"
    grep "DirectoryIndex" ~/public_html/iwacu/.htaccess
else
    echo "✅ No DirectoryIndex found (will use default)"
fi
echo ""

# 3. Check parent .htaccess (public_html)
echo "📋 Step 3: Checking parent .htaccess (public_html)..."
if [ -f ~/public_html/.htaccess ]; then
    echo "⚠️  Found .htaccess in public_html/"
    echo "Checking for any rules that might affect iwacu:"
    if grep -q "iwacu\|RewriteRule.*iwacu\|RewriteCond.*iwacu" ~/public_html/.htaccess 2>/dev/null; then
        echo "⚠️  Found iwacu-related rules:"
        grep -i "iwacu\|RewriteRule.*iwacu\|RewriteCond.*iwacu" ~/public_html/.htaccess
    else
        echo "✅ No iwacu-specific rules found"
    fi
    
    # Check for any redirect rules
    if grep -q "RewriteRule.*\[R=" ~/public_html/.htaccess 2>/dev/null; then
        echo "⚠️  Found redirect rules in public_html/.htaccess:"
        grep "RewriteRule.*\[R=" ~/public_html/.htaccess
    fi
    
    # Check if there's a trailing slash redirect that might interfere
    if grep -q "RewriteRule.*\$" ~/public_html/.htaccess 2>/dev/null; then
        echo "⚠️  Found trailing slash rules:"
        grep "RewriteRule.*\$" ~/public_html/.htaccess
    fi
    echo ""
else
    echo "✅ No .htaccess in public_html/"
    echo ""
fi

# 4. Create/update .htaccess in public_html/iwacu to prevent redirects
echo "📋 Step 4: Creating secure .htaccess for iwacu..."
cd ~/public_html/iwacu

# Backup existing .htaccess
if [ -f .htaccess ]; then
    cp .htaccess .htaccess.backup-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backed up existing .htaccess"
fi

# Create a clean .htaccess that prevents redirects
cat > .htaccess << 'HTACCESS_EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # CRITICAL: Disable directory redirects (prevent trailing slash redirects)
    # This prevents Apache from redirecting / to /iwacu/ or adding paths
    DirectorySlash Off

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

    # IMPORTANT: Don't redirect trailing slashes - let Laravel handle it
    # Remove any trailing slash redirect rules that might cause issues

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS_EOF

echo "✅ Created new .htaccess with DirectorySlash Off"
echo ""

# 5. Test the redirect again
echo "📋 Step 5: Testing redirect after fix..."
if command -v curl &> /dev/null; then
    echo "Testing curl request to https://iwacu.org/..."
    RESPONSE=$(curl -I -s -L https://iwacu.org/ | head -5)
    echo "$RESPONSE"
    echo ""
    
    if echo "$RESPONSE" | grep -q "301\|302"; then
        echo "⚠️  Still redirecting! Location header:"
        curl -I -s https://iwacu.org/ | grep -i "location"
    else
        echo "✅ No redirect found!"
    fi
else
    echo "⚠️  curl not available for testing"
fi

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  IMPORTANT:"
echo "   1. Clear browser cache completely (Ctrl+Shift+Delete)"
echo "   2. Test https://iwacu.org/ in incognito/private mode"
echo "   3. If it still redirects, check cPanel -> Apache Handlers or .htaccess in parent directory"

