#!/bin/bash

# Test audio route for iwacu.org
# Run this on the server: bash test-iwacu-audio-route.sh

echo "🧪 Testing iwacu.org audio route..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Test route generation
echo "📋 Step 1: Testing route generation..."
echo "Testing route('indirimbo.audio', 1):"
php artisan tinker --execute="echo route('indirimbo.audio', 1);" 2>&1 | grep -v "Psy" | tail -1
echo ""

# 2. Test if route exists
echo "📋 Step 2: Checking if route exists..."
php artisan route:list | grep -i "indirimbo.audio" || echo "Route not found!"
echo ""

# 3. Test actual HTTP request to audio route
echo "📋 Step 3: Testing HTTP request to audio route..."
if command -v curl &> /dev/null; then
    # Get first song ID from database
    SONG_ID=$(php artisan tinker --execute="echo App\Models\Song::where('StatusID', 2)->first()?->IndirimboID ?? 'none';" 2>&1 | grep -v "Psy" | tail -1 | tr -d ' ')
    
    if [ "$SONG_ID" != "none" ] && [ -n "$SONG_ID" ]; then
        echo "Testing with song ID: $SONG_ID"
        echo "URL: https://iwacu.org/audio/$SONG_ID"
        echo ""
        RESPONSE=$(curl -I -s https://iwacu.org/audio/$SONG_ID | head -10)
        echo "$RESPONSE"
        echo ""
        
        if echo "$RESPONSE" | grep -q "200 OK"; then
            echo "✅ Audio route works!"
        elif echo "$RESPONSE" | grep -q "404"; then
            echo "❌ 404 - Route not found or file not found"
            echo ""
            echo "Checking what the route returns:"
            curl -s https://iwacu.org/audio/$SONG_ID | head -5
        else
            echo "⚠️  Unexpected response"
        fi
    else
        echo "⚠️  No songs found in database"
    fi
else
    echo "⚠️  curl not available"
fi
echo ""

# 4. Check if audio files directory exists
echo "📋 Step 4: Checking audio files directory..."
if [ -d "storage/app/Audios" ]; then
    echo "✅ Audio directory exists: storage/app/Audios"
    FILE_COUNT=$(find storage/app/Audios -type f | wc -l)
    echo "   Found $FILE_COUNT audio files"
    
    if [ "$FILE_COUNT" -gt 0 ]; then
        echo "   Sample files:"
        find storage/app/Audios -type f | head -5
    else
        echo "   ⚠️  No audio files found!"
    fi
else
    echo "❌ Audio directory not found: storage/app/Audios"
fi
echo ""

# 5. Check a specific song's audio file path
echo "📋 Step 5: Checking a song's audio file path..."
SONG_INFO=$(php artisan tinker --execute="
\$song = App\Models\Song::where('StatusID', 2)->first();
if (\$song) {
    echo 'Song ID: ' . \$song->IndirimboID . PHP_EOL;
    echo 'Song Name: ' . \$song->IndirimboName . PHP_EOL;
    echo 'IndirimboUrl: ' . (\$song->IndirimboUrl ?? 'NULL') . PHP_EOL;
    if (\$song->IndirimboUrl) {
        \$basePath = storage_path('app/');
        \$url = ltrim(\$song->IndirimboUrl, '/');
        \$possiblePaths = [
            \$basePath . \$url,
            \$basePath . 'Audios/' . \$url,
            \$basePath . 'Audios/' . basename(\$url)
        ];
        echo 'Possible paths:' . PHP_EOL;
        foreach (\$possiblePaths as \$path) {
            echo '  - ' . \$path . ' (' . (file_exists(\$path) ? 'EXISTS' : 'NOT FOUND') . ')' . PHP_EOL;
        }
    }
} else {
    echo 'No songs found';
}
" 2>&1 | grep -v "Psy")
echo "$SONG_INFO"
echo ""

# 6. Check .htaccess to ensure routes are properly handled
echo "📋 Step 6: Checking .htaccess for route handling..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ .htaccess exists"
    if grep -q "RewriteRule.*index.php" ~/public_html/iwacu/.htaccess; then
        echo "✅ Found rewrite rule for index.php"
        grep "RewriteRule.*index.php" ~/public_html/iwacu/.htaccess
    else
        echo "❌ No rewrite rule found for index.php!"
    fi
else
    echo "❌ .htaccess not found!"
fi

echo ""
echo "✅ Test complete!"

