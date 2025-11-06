#!/bin/bash

# Quick diagnosis of what broke on iwacu.org
# Run this on the server: bash quick-diagnose-iwacu.sh

echo "🔍 Quick diagnosis of iwacu.org..."
echo ""

cd ~/public_html/iwacu

# 1. Check if .htaccess exists and what it looks like
echo "📋 Step 1: Checking .htaccess..."
if [ -f ".htaccess" ]; then
    echo "✅ .htaccess exists"
    
    # Check for key rules
    if grep -q "DirectorySlash Off" .htaccess; then
        echo "✅ DirectorySlash Off found"
    else
        echo "❌ DirectorySlash Off NOT found!"
    fi
    
    if grep -q "RewriteCond.*build" .htaccess; then
        echo "✅ Build directory rule found"
    else
        echo "❌ Build directory rule NOT found!"
    fi
    
    if grep -q "\.(svg|png|jpg|jpeg|gif|ico|css|js)" .htaccess; then
        echo "✅ Static file rule found"
    else
        echo "❌ Static file rule NOT found!"
    fi
    
    # Check when it was last modified
    MOD_TIME=$(stat -c "%y" .htaccess 2>/dev/null || stat -f "%Sm" .htaccess 2>/dev/null || echo "unknown")
    echo "   Last modified: $MOD_TIME"
else
    echo "❌ .htaccess NOT found!"
fi
echo ""

# 2. Test HTTP access to key files
echo "📋 Step 2: Testing HTTP access..."
if command -v curl &> /dev/null; then
    echo "Testing homepage:"
    RESPONSE=$(curl -I -s https://iwacu.org/ | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ Homepage accessible"
    elif echo "$RESPONSE" | grep -q "301\|302"; then
        echo "❌ Homepage redirecting!"
        LOCATION=$(echo "$RESPONSE" | grep -i "location" || echo "No location header")
        echo "   $LOCATION"
    else
        echo "⚠️  Homepage response: $(echo "$RESPONSE" | head -1)"
    fi
    
    echo ""
    echo "Testing CSS:"
    RESPONSE=$(curl -I -s "https://iwacu.org/build/assets/app-BySXMQJt.css" | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ CSS accessible"
    else
        echo "❌ CSS NOT accessible: $(echo "$RESPONSE" | head -1)"
    fi
    
    echo ""
    echo "Testing placeholder:"
    RESPONSE=$(curl -I -s "https://iwacu.org/placeholder.svg" | head -3)
    if echo "$RESPONSE" | grep -q "200 OK"; then
        echo "✅ placeholder.svg accessible"
    else
        echo "❌ placeholder.svg NOT accessible: $(echo "$RESPONSE" | head -1)"
    fi
else
    echo "⚠️  curl not available"
fi
echo ""

# 3. Check if recent deployment happened
echo "📋 Step 3: Checking for recent changes..."
if [ -f ".htaccess.backup-"* ]; then
    LATEST_BACKUP=$(ls -t .htaccess.backup-* 2>/dev/null | head -1)
    if [ -n "$LATEST_BACKUP" ]; then
        BACKUP_TIME=$(stat -c "%y" "$LATEST_BACKUP" 2>/dev/null || stat -f "%Sm" "$LATEST_BACKUP" 2>/dev/null || echo "unknown")
        echo "⚠️  Latest .htaccess backup: $BACKUP_TIME"
        echo "   Backup: $LATEST_BACKUP"
    fi
fi

# Check if index.php was recently modified
if [ -f "index.php" ]; then
    INDEX_TIME=$(stat -c "%y" index.php 2>/dev/null || stat -f "%Sm" index.php 2>/dev/null || echo "unknown")
    echo "   index.php last modified: $INDEX_TIME"
fi
echo ""

# 4. Quick fix option
echo "📋 Step 4: Quick fix available..."
echo ""
echo "⚠️  If .htaccess was overwritten, run:"
echo "   bash fix-iwacu-htaccess-assets.sh"
echo ""
echo "✅ Diagnosis complete!"

