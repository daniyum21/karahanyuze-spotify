#!/bin/bash

# Fix browser cache redirect issue
# This adds cache-control headers to prevent browsers from caching redirects
# Run this on the server: bash fix-browser-cache-redirect.sh

echo "🔧 Fixing browser cache redirect issue..."
echo ""

cd ~/public_html/iwacu

# 1. Backup current .htaccess
echo "📋 Step 1: Backing up current .htaccess..."
if [ -f ".htaccess" ]; then
    cp .htaccess .htaccess.backup-cache-fix-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backup created"
else
    echo "❌ .htaccess not found!"
    exit 1
fi
echo ""

# 2. Check current .htaccess for cache-control headers
echo "📋 Step 2: Checking current .htaccess for cache-control headers..."
if grep -q "Cache-Control\|Header.*Cache-Control" .htaccess 2>/dev/null; then
    echo "⚠️  Found cache-control headers:"
    grep "Cache-Control\|Header.*Cache-Control" .htaccess
else
    echo "✅ No cache-control headers found"
fi
echo ""

# 3. Add cache-control headers to prevent redirect caching
echo "📋 Step 3: Adding cache-control headers to prevent redirect caching..."
# Check if mod_headers is already enabled
if ! grep -q "<IfModule mod_headers.c>" .htaccess 2>/dev/null; then
    # Add mod_headers section at the beginning
    cat > .htaccess << 'HTACCESS_EOF'
<IfModule mod_headers.c>
    # Prevent browsers from caching redirects
    Header set Cache-Control "no-cache, no-store, must-revalidate, max-age=0"
    Header set Pragma "no-cache"
    Header set Expires "0"
</IfModule>

HTACCESS_EOF
    # Append the rest of the existing .htaccess
    cat .htaccess.backup-cache-fix-* | tail -n +1 >> .htaccess 2>/dev/null || true
else
    echo "⚠️  mod_headers section already exists"
fi

# Actually, let's read the current .htaccess and add headers properly
# Let me do this differently - read the file and add headers at the top
if [ -f ".htaccess.backup-cache-fix-"* ]; then
    BACKUP_FILE=$(ls -t .htaccess.backup-cache-fix-* 2>/dev/null | head -1)
    if [ -n "$BACKUP_FILE" ]; then
        # Add headers at the beginning
        {
            echo '<IfModule mod_headers.c>'
            echo '    # Prevent browsers from caching redirects'
            echo '    Header set Cache-Control "no-cache, no-store, must-revalidate, max-age=0"'
            echo '    Header set Pragma "no-cache"'
            echo '    Header set Expires "0"'
            echo '</IfModule>'
            echo ''
            cat "$BACKUP_FILE"
        } > .htaccess
        echo "✅ Cache-control headers added"
    fi
else
    echo "⚠️  Could not find backup file, skipping header addition"
fi
echo ""

# 4. Test HTTP headers
echo "📋 Step 4: Testing HTTP headers..."
if command -v curl &> /dev/null; then
    echo "Testing https://iwacu.org/..."
    RESPONSE=$(curl -I -s https://iwacu.org/ | head -10)
    echo "$RESPONSE"
    echo ""
    
    if echo "$RESPONSE" | grep -q "Cache-Control: no-cache"; then
        echo "✅ Cache-Control header is set"
    else
        echo "⚠️  Cache-Control header not found"
    fi
else
    echo "⚠️  curl not available"
fi

echo ""
echo "✅ Fix complete!"
echo ""
echo "⚠️  IMPORTANT: To clear browser cache on your end:"
echo "   1. Chrome/Edge: Ctrl+Shift+Delete (Cmd+Shift+Delete on Mac)"
echo "   2. Select 'Cached images and files' and 'Hosted app data'"
echo "   3. Time range: 'All time'"
echo "   4. Click 'Clear data'"
echo ""
echo "   Or use incognito/private mode until cache expires (usually 24 hours)"
echo ""
echo "   Alternatively, you can add '?v=' + timestamp to URLs to force reload"

