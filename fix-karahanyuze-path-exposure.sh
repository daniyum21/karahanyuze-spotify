#!/bin/bash

# CRITICAL FIX for karahanyuze.com path exposure
# This fixes the redirect to /home4/biriheco/public_html/%25%25
# Run this on the server: bash fix-karahanyuze-path-exposure.sh

echo "🔒 CRITICAL FIX: Fixing karahanyuze.com path exposure"
echo ""

cd ~/public_html

# 1. Fix .htaccess - ensure it's the original working version
echo "📋 Step 1: Fixing .htaccess..."
if [ -f ".htaccess" ]; then
    cp .htaccess .htaccess.backup-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backup created"
fi

# Create the original working .htaccess
cat > .htaccess << 'EOF'
# PHP Configuration for File Uploads
<IfModule mod_php7.c>
    php_value upload_max_filesize 500M
    php_value post_max_size 1024M
    php_value max_execution_time 600
    php_value max_input_time 600
    php_value memory_limit 512M
</IfModule>
<IfModule mod_php8.c>
    php_value upload_max_filesize 500M
    php_value post_max_size 1024M
    php_value max_execution_time 600
    php_value max_input_time 600
    php_value memory_limit 512M
</IfModule>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Skip ALL Laravel rules if NOT exactly karahanyuze.com or www.karahanyuze.com
    # This allows other domains/subdomains (indirimbo.com, erwanda.com, iwacu.org, etc.) 
    # mapped to subdirectories (public_html/foldername) to work
    # When these domains are accessed, HTTP_HOST will be that domain
    # These domains point directly to their subdirectories via cPanel, so they skip Laravel rules
    # Only process Laravel rules for the exact main domain (karahanyuze.com)
    RewriteCond %{HTTP_HOST} !^karahanyuze\.com$ [NC]
    RewriteCond %{HTTP_HOST} !^www\.karahanyuze\.com$ [NC]
    RewriteRule ^ - [L]
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    # Only apply to karahanyuze.com to avoid interfering with subdomain subdirectories
    RewriteCond %{HTTP_HOST} ^(www\.)?karahanyuze\.com$ [NC]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    # Only apply to karahanyuze.com to avoid catching subdomain subdirectories
    # Exclude iwacu/ from Laravel rules (allow direct access like indirimbo/)
    RewriteCond %{HTTP_HOST} ^(www\.)?karahanyuze\.com$ [NC]
    RewriteCond %{REQUEST_URI} !^/iwacu/
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
echo "✅ .htaccess fixed"

# 2. Fix index.php - ensure it points to karahanyuze11
echo ""
echo "📋 Step 2: Fixing index.php..."
if [ -f "index.php" ]; then
    cp index.php index.php.backup-$(date +%Y%m%d-%H%M%S)
    
    # Ensure paths point to karahanyuze11
    sed -i "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/../private/karahanyuze11/vendor/autoload.php'|g" index.php
    sed -i "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/../private/karahanyuze11/bootstrap/app.php'|g" index.php
    sed -i "s|private/karahanyuze-spotify|private/karahanyuze11|g" index.php
    
    echo "✅ index.php fixed"
    echo "📄 Current paths in index.php:"
    grep -E "vendor/autoload.php|bootstrap/app.php" index.php | head -2
else
    echo "❌ index.php not found!"
fi

# 3. Check Laravel .env file
echo ""
echo "📋 Step 3: Checking Laravel .env file..."
cd ~/private/karahanyuze11
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    
    # Check APP_URL
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2-)
        echo "📄 Current APP_URL: $APP_URL"
        
        if [[ "$APP_URL" != *"karahanyuze.com"* ]]; then
            echo "⚠️  APP_URL doesn't contain karahanyuze.com, fixing..."
            sed -i "s|^APP_URL=.*|APP_URL=https://karahanyuze.com|g" .env
            echo "✅ APP_URL fixed to https://karahanyuze.com"
        fi
    else
        echo "⚠️  APP_URL not set, adding..."
        echo "" >> .env
        echo "APP_URL=https://karahanyuze.com" >> .env
        echo "✅ APP_URL added"
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
echo "✅ Fix complete!"
echo ""
echo "⚠️  IMPORTANT: Please test karahanyuze.com now"
echo "   If it still redirects, check:"
echo "   1. Is karahanyuze.com mapped to public_html (not a subdirectory) in cPanel?"
echo "   2. Are there any other .htaccess files in parent directories?"
echo "   3. Check ~/private/karahanyuze11/storage/logs/laravel.log for errors"

