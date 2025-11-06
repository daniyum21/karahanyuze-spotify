#!/bin/bash

# CRITICAL FIX for iwacu.org path exposure
# This fixes the redirect to /home4/biriheco/public_html/%25%25
# Run this on the server: bash fix-iwacu-path-exposure.sh

echo "🔒 CRITICAL FIX: Fixing iwacu.org path exposure"
echo ""

# 1. Fix .htaccess in iwacu
echo "📋 Step 1: Fixing public_html/iwacu/.htaccess..."
cd ~/public_html/iwacu
if [ -f ".htaccess" ]; then
    cp .htaccess .htaccess.backup-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backup created"
fi

# Create secure standard Laravel .htaccess
cat > .htaccess << 'EOF'
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
EOF
echo "✅ .htaccess fixed"

# 2. Fix index.php in iwacu - ensure it points to karahanyuze-spotify
echo ""
echo "📋 Step 2: Fixing public_html/iwacu/index.php..."
if [ -f "index.php" ]; then
    cp index.php index.php.backup-$(date +%Y%m%d-%H%M%S)
    
    # Ensure paths point to karahanyuze-spotify
    sed -i "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/../../private/karahanyuze-spotify/vendor/autoload.php'|g" index.php
    sed -i "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/../../private/karahanyuze-spotify/bootstrap/app.php'|g" index.php
    sed -i "s|private/karahanyuze11|private/karahanyuze-spotify|g" index.php
    
    echo "✅ index.php fixed"
    echo "📄 Current paths in index.php:"
    grep -E "vendor/autoload.php|bootstrap/app.php" index.php | head -2
else
    echo "❌ index.php not found!"
fi

# 3. Check Laravel .env file for karahanyuze-spotify
echo ""
echo "📋 Step 3: Checking Laravel .env file for karahanyuze-spotify..."
cd ~/private/karahanyuze-spotify
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    
    # Check APP_URL - CRITICAL: This must be set correctly
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        echo "📄 Current APP_URL: $APP_URL"
        
        # Check if APP_URL contains paths (which is wrong)
        if [[ "$APP_URL" == *"/home"* ]] || [[ "$APP_URL" == *"public_html"* ]] || [[ "$APP_URL" != *"iwacu.org"* ]]; then
            echo "❌ APP_URL is incorrect: $APP_URL"
            echo "🔧 Fixing APP_URL..."
            sed -i "s|^APP_URL=.*|APP_URL=https://iwacu.org|g" .env
            echo "✅ APP_URL fixed to https://iwacu.org"
        else
            echo "✅ APP_URL looks correct: $APP_URL"
        fi
    else
        echo "⚠️  APP_URL not set, adding..."
        echo "" >> .env
        echo "APP_URL=https://iwacu.org" >> .env
        echo "✅ APP_URL added"
    fi
    
    # Also check for any other URL-related config that might be wrong
    if grep -q "^ASSET_URL=" .env; then
        ASSET_URL=$(grep "^ASSET_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        if [[ "$ASSET_URL" == *"/home"* ]] || [[ "$ASSET_URL" == *"public_html"* ]]; then
            echo "❌ ASSET_URL is incorrect: $ASSET_URL"
            echo "🔧 Removing incorrect ASSET_URL..."
            sed -i "/^ASSET_URL=/d" .env
            echo "✅ ASSET_URL removed (will use APP_URL)"
        fi
    fi
    
    # Clear Laravel config cache
    echo "🧹 Clearing Laravel caches..."
    php artisan config:clear || true
    php artisan cache:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
else
    echo "⚠️  .env file not found!"
fi

echo ""
echo "✅ Fix complete for iwacu.org!"
echo ""
echo "⚠️  IMPORTANT: Please test iwacu.org now"
echo "   If it still redirects, check:"
echo "   1. Is iwacu.org mapped to public_html/iwacu (not public_html) in cPanel?"
echo "   2. Check ~/private/karahanyuze-spotify/storage/logs/laravel.log for errors"

