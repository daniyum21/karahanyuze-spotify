#!/bin/bash

# CRITICAL SECURITY FIX for iwacu.org path exposure
# Run this immediately on the server: bash fix-iwacu-security.sh

echo "🔒 CRITICAL SECURITY FIX: Preventing path exposure in iwacu/.htaccess"
echo ""

cd ~/public_html/iwacu

# Backup existing .htaccess
if [ -f ".htaccess" ]; then
    echo "📦 Backing up existing .htaccess..."
    cp .htaccess .htaccess.insecure-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backup created"
fi

# Create secure .htaccess
echo "🔧 Creating secure .htaccess..."
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

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS_EOF

echo "✅ Secure .htaccess created"
echo ""
echo "🔍 Verifying .htaccess..."
if [ -f ".htaccess" ]; then
    echo "✅ .htaccess exists"
    echo "📄 First 15 lines:"
    head -15 .htaccess
    echo ""
    echo "✅ Security fix applied!"
    echo "⚠️  Please test iwacu.org immediately to ensure it works correctly"
else
    echo "❌ Error: .htaccess not created!"
fi

