#!/bin/bash

# Script to verify and fix iwacu setup on server
# Run this on the server: bash verify-iwacu.sh

echo "🔍 Verifying iwacu setup..."

# Check .htaccess
echo ""
echo "📋 Checking public_html/iwacu/.htaccess..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ .htaccess exists"
    echo "📄 First 10 lines of .htaccess:"
    head -10 ~/public_html/iwacu/.htaccess
    
    # Check for security issues - look for RewriteRule that might expose paths
    if grep -q "RewriteRule.*%25" ~/public_html/iwacu/.htaccess || grep -q "RewriteRule.*public_html" ~/public_html/iwacu/.htaccess; then
        echo "⚠️  WARNING: .htaccess may have security issues!"
        echo "🔧 Recreating secure .htaccess..."
        cd ~/public_html/iwacu
        cp .htaccess .htaccess.insecure-$(date +%Y%m%d-%H%M%S)
        
        # Create secure .htaccess
        cat > .htaccess << 'HTACCESS_EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Disable directory browsing
    Options -Indexes

    # Prevent access to hidden files
    RewriteCond %{SCRIPT_FILENAME} -d [OR]
    RewriteCond %{SCRIPT_FILENAME} -f
    RewriteRule "(^|/)\." - [F]

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
        echo "✅ Secure .htaccess recreated"
    fi
else
    echo "❌ .htaccess not found!"
fi

# Check index.php
echo ""
echo "📋 Checking public_html/iwacu/index.php..."
if [ -f ~/public_html/iwacu/index.php ]; then
    echo "✅ index.php exists"
    echo "📄 Paths in index.php:"
    grep -E "vendor/autoload.php|bootstrap/app.php" ~/public_html/iwacu/index.php
    
    # Check if it points to karahanyuze-spotify
    if grep -q "karahanyuze-spotify" ~/public_html/iwacu/index.php; then
        echo "✅ index.php points to karahanyuze-spotify"
    else
        echo "❌ index.php does NOT point to karahanyuze-spotify!"
        echo "🔧 Fixing index.php..."
        cd ~/public_html/iwacu
        cp index.php index.php.backup-$(date +%Y%m%d-%H%M%S)
        
        # Fix paths to point to karahanyuze-spotify
        sed -i "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/../../private/karahanyuze-spotify/vendor/autoload.php'|g" index.php
        sed -i "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/../../private/karahanyuze-spotify/bootstrap/app.php'|g" index.php
        sed -i "s|private/karahanyuze11|private/karahanyuze-spotify|g" index.php
        
        echo "✅ index.php fixed!"
        echo "📄 Updated paths:"
        grep -E "vendor/autoload.php|bootstrap/app.php" index.php
    fi
else
    echo "❌ index.php not found!"
fi

# Check if Laravel installation exists
echo ""
echo "📋 Checking if private/karahanyuze-spotify exists..."
if [ -d ~/private/karahanyuze-spotify ]; then
    echo "✅ Laravel installation exists"
    if [ -f ~/private/karahanyuze-spotify/vendor/autoload.php ]; then
        echo "✅ vendor/autoload.php exists"
    else
        echo "❌ vendor/autoload.php not found! Run composer install"
    fi
    if [ -f ~/private/karahanyuze-spotify/bootstrap/app.php ]; then
        echo "✅ bootstrap/app.php exists"
    else
        echo "❌ bootstrap/app.php not found!"
    fi
else
    echo "❌ Laravel installation not found!"
fi

echo ""
echo "✅ Verification complete!"
echo ""
echo "If everything looks correct but it still doesn't work, check:"
echo "1. Is the domain correctly mapped to public_html/iwacu/ in cPanel?"
echo "2. Are there any PHP errors in ~/private/karahanyuze-spotify/storage/logs/laravel.log?"
echo "3. Try accessing the domain and check the error logs"

