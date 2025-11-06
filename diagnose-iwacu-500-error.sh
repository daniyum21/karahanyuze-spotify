#!/bin/bash

# Diagnose HTTP 500 error on iwacu.org
# Run this on the server: bash diagnose-iwacu-500-error.sh

echo "🔍 Diagnosing HTTP 500 error on iwacu.org..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Check Laravel error log
echo "📋 Step 1: Checking Laravel error log..."
if [ -f "storage/logs/laravel.log" ]; then
    echo "✅ Found Laravel log file"
    echo "Last 50 lines of error log:"
    tail -50 storage/logs/laravel.log
    echo ""
else
    echo "⚠️  Laravel log file not found"
fi
echo ""

# 2. Check PHP error log
echo "📋 Step 2: Checking PHP error log..."
if [ -f ~/public_html/iwacu/error_log ]; then
    echo "✅ Found PHP error log in iwacu"
    echo "Last 20 lines:"
    tail -20 ~/public_html/iwacu/error_log
    echo ""
elif [ -f ~/public_html/error_log ]; then
    echo "✅ Found PHP error log in public_html"
    echo "Last 20 lines:"
    tail -20 ~/public_html/error_log | grep -i iwacu || tail -20 ~/public_html/error_log
    echo ""
else
    echo "⚠️  PHP error log not found"
fi
echo ""

# 3. Check Apache error log
echo "📋 Step 3: Checking Apache error log..."
if [ -f /usr/local/apache/logs/error_log ]; then
    echo "✅ Found Apache error log"
    echo "Last 10 lines related to iwacu:"
    tail -100 /usr/local/apache/logs/error_log | grep -i iwacu | tail -10 || echo "No iwacu-related errors found"
    echo ""
else
    echo "⚠️  Apache error log not found"
fi
echo ""

# 4. Check .htaccess for syntax errors
echo "📋 Step 4: Checking .htaccess syntax..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ .htaccess exists"
    # Check for common syntax errors
    if grep -q "DirectorySlash" ~/public_html/iwacu/.htaccess; then
        echo "✅ DirectorySlash found"
    fi
    
    # Check for unclosed tags
    IFMODULE_COUNT=$(grep -c "<IfModule" ~/public_html/iwacu/.htaccess || echo "0")
    ENDIFMODULE_COUNT=$(grep -c "</IfModule>" ~/public_html/iwacu/.htaccess || echo "0")
    
    if [ "$IFMODULE_COUNT" -eq "$ENDIFMODULE_COUNT" ]; then
        echo "✅ IfModule tags are balanced"
    else
        echo "❌ IfModule tags are NOT balanced! ($IFMODULE_COUNT open, $ENDIFMODULE_COUNT close)"
    fi
    
    # Check for RewriteEngine
    if grep -q "RewriteEngine On" ~/public_html/iwacu/.htaccess; then
        echo "✅ RewriteEngine On found"
    else
        echo "⚠️  RewriteEngine On not found"
    fi
else
    echo "❌ .htaccess not found!"
fi
echo ""

# 5. Check file permissions
echo "📋 Step 5: Checking file permissions..."
if [ -f ~/public_html/iwacu/index.php ]; then
    PERMS=$(stat -c "%a" ~/public_html/iwacu/index.php 2>/dev/null || stat -f "%OLp" ~/public_html/iwacu/index.php 2>/dev/null || echo "unknown")
    echo "✅ index.php exists (permissions: $PERMS)"
else
    echo "❌ index.php not found!"
fi

if [ -d "storage/logs" ]; then
    PERMS=$(stat -c "%a" storage/logs 2>/dev/null || stat -f "%OLp" storage/logs 2>/dev/null || echo "unknown")
    echo "✅ storage/logs exists (permissions: $PERMS)"
    if [ ! -w "storage/logs" ]; then
        echo "⚠️  storage/logs is not writable!"
    fi
else
    echo "❌ storage/logs not found!"
fi
echo ""

# 6. Check .env file
echo "📋 Step 6: Checking .env configuration..."
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    
    # Check critical settings
    if grep -q "^APP_DEBUG=" .env; then
        APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d '=' -f2 | tr -d ' ')
        echo "   APP_DEBUG=$APP_DEBUG"
    fi
    
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ')
        echo "   APP_URL=$APP_URL"
    fi
    
    # Check database connection
    if grep -q "^DB_CONNECTION=" .env; then
        DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2 | tr -d ' ')
        echo "   DB_CONNECTION=$DB_CONNECTION"
    fi
else
    echo "❌ .env file not found!"
fi
echo ""

# 7. Test PHP syntax
echo "📋 Step 7: Testing PHP syntax..."
if [ -f ~/public_html/iwacu/index.php ]; then
    if php -l ~/public_html/iwacu/index.php 2>&1; then
        echo "✅ index.php syntax is valid"
    else
        echo "❌ index.php has syntax errors!"
    fi
fi

# Test bootstrap/app.php
if [ -f "bootstrap/app.php" ]; then
    if php -l bootstrap/app.php 2>&1; then
        echo "✅ bootstrap/app.php syntax is valid"
    else
        echo "❌ bootstrap/app.php has syntax errors!"
    fi
fi
echo ""

# 8. Quick test of Laravel bootstrap
echo "📋 Step 8: Testing Laravel bootstrap..."
php artisan --version 2>&1 | head -1 || echo "❌ Laravel bootstrap failed!"
echo ""

# 9. Check recent .htaccess changes
echo "📋 Step 9: Checking recent .htaccess backups..."
if ls -t ~/public_html/iwacu/.htaccess.backup-* 2>/dev/null | head -1; then
    LATEST_BACKUP=$(ls -t ~/public_html/iwacu/.htaccess.backup-* 2>/dev/null | head -1)
    echo "⚠️  Latest backup found: $LATEST_BACKUP"
    echo "   You can restore it with: cp $LATEST_BACKUP ~/public_html/iwacu/.htaccess"
fi
echo ""

echo "✅ Diagnosis complete!"
echo ""
echo "⚠️  IMPORTANT: Check the Laravel error log above for the actual error message"
echo "   The most common causes of HTTP 500 errors:"
echo "   1. Syntax error in .htaccess (check Step 4)"
echo "   2. PHP syntax error (check Step 7)"
echo "   3. Database connection error (check .env)"
echo "   4. Missing file permissions (check Step 5)"
echo "   5. Missing dependencies or configuration"

