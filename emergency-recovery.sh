#!/bin/bash

# EMERGENCY RECOVERY SCRIPT
# Run this immediately to restore all websites
# bash emergency-recovery.sh

echo "🚨 EMERGENCY RECOVERY: Restoring all websites"
echo ""

# 1. Restore public_html/.htaccess to working version
echo "📋 Step 1: Restoring public_html/.htaccess..."
cd ~/public_html

# Check if backup exists
if [ -f ".htaccess.backup" ]; then
    echo "📦 Restoring from .htaccess.backup..."
    cp .htaccess.backup .htaccess
    echo "✅ Restored from backup"
elif [ -f ".htaccess_old" ]; then
    echo "📦 Restoring from .htaccess_old..."
    cp .htaccess_old .htaccess
    echo "✅ Restored from .htaccess_old"
else
    # Create minimal working .htaccess
    echo "🔧 Creating minimal working .htaccess..."
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
    RewriteCond %{HTTP_HOST} ^(www\.)?karahanyuze\.com$ [NC]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{HTTP_HOST} ^(www\.)?karahanyuze\.com$ [NC]
    RewriteCond %{REQUEST_URI} !^/iwacu/
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
    echo "✅ Created minimal .htaccess"
fi

# 2. Restore public_html/index.php
echo ""
echo "📋 Step 2: Checking public_html/index.php..."
if [ -f "index.php" ]; then
    # Check if it points to karahanyuze11
    if ! grep -q "karahanyuze11" index.php; then
        echo "🔧 Fixing index.php paths..."
        if [ -f "index.php.backup" ]; then
            cp index.php.backup index.php
            echo "✅ Restored from backup"
        else
            # Try to fix paths
            sed -i "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/../private/karahanyuze11/vendor/autoload.php'|g" index.php
            sed -i "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/../private/karahanyuze11/bootstrap/app.php'|g" index.php
            echo "✅ Fixed paths"
        fi
    else
        echo "✅ index.php looks correct"
    fi
else
    echo "❌ index.php not found!"
fi

# 3. Restore iwacu/.htaccess
echo ""
echo "📋 Step 3: Restoring public_html/iwacu/.htaccess..."
cd ~/public_html/iwacu
if [ -f ".htaccess" ]; then
    # Check if backup exists
    BACKUP_FILE=$(ls -t .htaccess.backup* .htaccess.insecure* 2>/dev/null | head -1)
    if [ -n "$BACKUP_FILE" ]; then
        echo "📦 Restoring from $BACKUP_FILE..."
        cp "$BACKUP_FILE" .htaccess
        echo "✅ Restored from backup"
    else
        # Create minimal working .htaccess
        echo "🔧 Creating minimal working .htaccess..."
        cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

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
        echo "✅ Created minimal .htaccess"
    fi
else
    echo "⚠️  .htaccess not found, creating one..."
    cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

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
    echo "✅ Created .htaccess"
fi

# 4. Restore iwacu/index.php
echo ""
echo "📋 Step 4: Checking public_html/iwacu/index.php..."
if [ -f "index.php" ]; then
    # Check if it points to karahanyuze-spotify
    if ! grep -q "karahanyuze-spotify" index.php; then
        echo "🔧 Fixing index.php paths..."
        if [ -f "index.php.backup" ]; then
            cp index.php.backup index.php
            echo "✅ Restored from backup"
        else
            # Try to fix paths
            sed -i "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/../../private/karahanyuze-spotify/vendor/autoload.php'|g" index.php
            sed -i "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/../../private/karahanyuze-spotify/bootstrap/app.php'|g" index.php
            echo "✅ Fixed paths"
        fi
    else
        echo "✅ index.php looks correct"
    fi
else
    echo "❌ index.php not found!"
fi

# 5. Fix Laravel .env files
echo ""
echo "📋 Step 5: Fixing Laravel .env files..."

# Fix karahanyuze11 .env
cd ~/private/karahanyuze11
if [ -f ".env" ]; then
    echo "🔧 Fixing karahanyuze11 .env..."
    # Fix APP_URL if it contains paths
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        if [[ "$APP_URL" == *"/home"* ]] || [[ "$APP_URL" == *"public_html"* ]]; then
            sed -i "s|^APP_URL=.*|APP_URL=https://karahanyuze.com|g" .env
            echo "✅ Fixed APP_URL in karahanyuze11"
        fi
    fi
    php artisan config:clear || true
fi

# Fix karahanyuze-spotify .env
cd ~/private/karahanyuze-spotify
if [ -f ".env" ]; then
    echo "🔧 Fixing karahanyuze-spotify .env..."
    # Fix APP_URL if it contains paths
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        if [[ "$APP_URL" == *"/home"* ]] || [[ "$APP_URL" == *"public_html"* ]]; then
            sed -i "s|^APP_URL=.*|APP_URL=https://iwacu.org|g" .env
            echo "✅ Fixed APP_URL in karahanyuze-spotify"
        fi
    fi
    php artisan config:clear || true
fi

echo ""
echo "✅ Emergency recovery complete!"
echo ""
echo "⚠️  Please test all websites now:"
echo "   1. karahanyuze.com"
echo "   2. iwacu.org"
echo "   3. Other domains (indirimbo.com, etc.)"
echo ""
echo "If sites are still down, check:"
echo "   - Apache error logs: tail -f /usr/local/apache/logs/error_log"
echo "   - Laravel logs: tail -f ~/private/karahanyuze11/storage/logs/laravel.log"
echo "   - Laravel logs: tail -f ~/private/karahanyuze-spotify/storage/logs/laravel.log"

