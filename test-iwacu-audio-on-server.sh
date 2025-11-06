#!/bin/bash

# Test audio route on server for iwacu.org
# Run this on the server: bash test-iwacu-audio-on-server.sh

echo "🧪 Testing iwacu.org audio route on server..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Test route generation
echo "📋 Step 1: Testing route generation..."
echo "Testing route('indirimbo.audio', 1):"
php artisan tinker --execute="echo route('indirimbo.audio', 1);" 2>&1 | grep -v "Psy" | tail -1
echo ""

# 2. Get a real song ID from database
echo "📋 Step 2: Getting a real song ID from database..."
SONG_ID=$(php artisan tinker --execute="\$song = App\Models\Song::where('StatusID', 2)->whereNotNull('IndirimboUrl')->first(); echo \$song ? \$song->IndirimboID : 'none';" 2>&1 | grep -v "Psy" | tail -1 | tr -d ' ')

if [ "$SONG_ID" != "none" ] && [ -n "$SONG_ID" ]; then
    echo "✅ Found song ID: $SONG_ID"
    
    # Get song info
    SONG_INFO=$(php artisan tinker --execute="
    \$song = App\Models\Song::find($SONG_ID);
    if (\$song) {
        echo 'Song Name: ' . \$song->IndirimboName . PHP_EOL;
        echo 'IndirimboUrl: ' . (\$song->IndirimboUrl ?? 'NULL') . PHP_EOL;
    }
    " 2>&1 | grep -v "Psy")
    echo "$SONG_INFO"
    echo ""
    
    # 3. Check if audio file exists
    echo "📋 Step 3: Checking if audio file exists..."
    AUDIO_PATH=$(php artisan tinker --execute="
    \$song = App\Models\Song::find($SONG_ID);
    if (\$song && \$song->IndirimboUrl) {
        \$url = ltrim(\$song->IndirimboUrl, '/');
        \$basePath = storage_path('app/');
        \$possiblePaths = [
            \$basePath . \$url,
            \$basePath . 'Audios/' . \$url,
            \$basePath . 'Audios/' . basename(\$url)
        ];
        foreach (\$possiblePaths as \$path) {
            if (file_exists(\$path)) {
                echo \$path;
                exit;
            }
        }
        echo 'NOT_FOUND';
    } else {
        echo 'NO_URL';
    }
    " 2>&1 | grep -v "Psy" | tail -1)
    
    if [ "$AUDIO_PATH" != "NOT_FOUND" ] && [ "$AUDIO_PATH" != "NO_URL" ]; then
        echo "✅ Audio file found: $AUDIO_PATH"
        if [ -f "$AUDIO_PATH" ]; then
            echo "   File exists and is readable"
            FILE_SIZE=$(stat -c%s "$AUDIO_PATH" 2>/dev/null || stat -f%z "$AUDIO_PATH" 2>/dev/null || echo "unknown")
            echo "   File size: $FILE_SIZE bytes"
        else
            echo "   ⚠️  File path exists but file is not readable"
        fi
    else
        echo "❌ Audio file NOT found!"
        echo "   Path: $AUDIO_PATH"
    fi
    echo ""
    
    # 4. Test HTTP request to audio route
    echo "📋 Step 4: Testing HTTP request to audio route..."
    if command -v curl &> /dev/null; then
        AUDIO_URL="https://iwacu.org/audio/$SONG_ID"
        echo "Testing: $AUDIO_URL"
        RESPONSE=$(curl -I -s "$AUDIO_URL" | head -5)
        echo "$RESPONSE"
        echo ""
        
        if echo "$RESPONSE" | grep -q "200 OK"; then
            echo "✅ Audio route works!"
            CONTENT_TYPE=$(echo "$RESPONSE" | grep -i "content-type" || echo "Not found")
            echo "   Content-Type: $CONTENT_TYPE"
        elif echo "$RESPONSE" | grep -q "404"; then
            echo "❌ 404 - Route not found or file not found"
            echo ""
            echo "Checking what the route returns:"
            curl -s "$AUDIO_URL" | head -10
        else
            echo "⚠️  Unexpected response"
        fi
    else
        echo "⚠️  curl not available"
    fi
    echo ""
else
    echo "⚠️  No songs found in database with audio URLs"
fi

# 5. Check .htaccess for audio route handling
echo "📋 Step 5: Checking .htaccess for route handling..."
if [ -f ~/public_html/iwacu/.htaccess ]; then
    echo "✅ .htaccess exists"
    if grep -q "RewriteRule.*index.php" ~/public_html/iwacu/.htaccess; then
        echo "✅ Found rewrite rule for index.php"
        echo "   This should allow /audio/ routes to work"
    else
        echo "❌ No rewrite rule found for index.php!"
    fi
else
    echo "❌ .htaccess not found!"
fi

echo ""
echo "✅ Test complete!"

