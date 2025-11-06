#!/bin/bash

# Check and guide uploading audio files to server
# Run this on the server: bash check-upload-audio-files.sh

echo "🔍 Checking audio files on server..."
echo ""

cd ~/private/karahanyuze-spotify

# 1. Check if Audios directory exists
echo "📋 Step 1: Checking Audios directory..."
if [ -d "storage/app/Audios" ]; then
    echo "✅ Audios directory exists: storage/app/Audios"
    FILE_COUNT=$(find storage/app/Audios -type f | wc -l)
    echo "   Found $FILE_COUNT audio files"
    
    if [ "$FILE_COUNT" -eq 0 ]; then
        echo "   ⚠️  No audio files found!"
    else
        echo "   Sample files:"
        find storage/app/Audios -type f | head -5
    fi
else
    echo "❌ Audios directory not found: storage/app/Audios"
    echo "   Creating directory..."
    mkdir -p storage/app/Audios
    chmod 755 storage/app/Audios
    echo "✅ Directory created"
fi
echo ""

# 2. Check database for songs with audio URLs
echo "📋 Step 2: Checking database for songs with audio URLs..."
SONG_COUNT=$(php artisan tinker --execute="echo App\Models\Song::whereNotNull('IndirimboUrl')->count();" 2>&1 | grep -v "Psy" | tail -1 | tr -d ' ')
echo "   Found $SONG_COUNT songs with audio URLs in database"
echo ""

# 3. Check how many audio files match database URLs
echo "📋 Step 3: Checking how many audio files match database URLs..."
MATCH_COUNT=$(php artisan tinker --execute="
\$songs = App\Models\Song::whereNotNull('IndirimboUrl')->get();
\$matchCount = 0;
\$basePath = storage_path('app/');
foreach (\$songs as \$song) {
    \$url = ltrim(\$song->IndirimboUrl, '/');
    \$possiblePaths = [
        \$basePath . \$url,
        \$basePath . 'Audios/' . \$url,
        \$basePath . 'Audios/' . basename(\$url)
    ];
    foreach (\$possiblePaths as \$path) {
        if (file_exists(\$path)) {
            \$matchCount++;
            break;
        }
    }
}
echo \$matchCount;
" 2>&1 | grep -v "Psy" | tail -1 | tr -d ' ')

echo "   Found $MATCH_COUNT audio files that match database URLs"
if [ "$MATCH_COUNT" -lt "$SONG_COUNT" ]; then
    MISSING=$((SONG_COUNT - MATCH_COUNT))
    echo "   ⚠️  $MISSING audio files are missing!"
fi
echo ""

# 4. Show sample missing files
echo "📋 Step 4: Sample missing audio files..."
php artisan tinker --execute="
\$songs = App\Models\Song::whereNotNull('IndirimboUrl')->take(5)->get();
\$basePath = storage_path('app/');
\$missing = [];
foreach (\$songs as \$song) {
    \$url = ltrim(\$song->IndirimboUrl, '/');
    \$possiblePaths = [
        \$basePath . \$url,
        \$basePath . 'Audios/' . \$url,
        \$basePath . 'Audios/' . basename(\$url)
    ];
    \$found = false;
    foreach (\$possiblePaths as \$path) {
        if (file_exists(\$path)) {
            \$found = true;
            break;
        }
    }
    if (!\$found) {
        \$missing[] = \$song->IndirimboName . ' -> ' . \$song->IndirimboUrl;
    }
}
if (count(\$missing) > 0) {
    echo 'Missing files:' . PHP_EOL;
    foreach (\$missing as \$item) {
        echo '  - ' . \$item . PHP_EOL;
    }
} else {
    echo 'All checked files found!' . PHP_EOL;
}
" 2>&1 | grep -v "Psy"
echo ""

# 5. Instructions for uploading
echo "📋 Step 5: Instructions for uploading audio files..."
echo ""
echo "⚠️  To upload audio files to the server:"
echo ""
echo "   Option 1: Using SCP (from your local machine):"
echo "   scp -r /path/to/local/audio/files/* user@server:~/private/karahanyuze-spotify/storage/app/Audios/"
echo ""
echo "   Option 2: Using SFTP (FileZilla, WinSCP, etc.):"
echo "   - Connect to: $(hostname)"
echo "   - Navigate to: ~/private/karahanyuze-spotify/storage/app/Audios/"
echo "   - Upload all audio files"
echo ""
echo "   Option 3: Using cPanel File Manager:"
echo "   - Navigate to: private/karahanyuze-spotify/storage/app/Audios/"
echo "   - Upload audio files"
echo ""
echo "   ⚠️  IMPORTANT:"
echo "   - Audio files should match the 'IndirimboUrl' field in the database"
echo "   - Files can be in the root of Audios/ or include the full path"
echo "   - Ensure file permissions are readable (644 or 755)"
echo ""

# 6. Check file permissions
echo "📋 Step 6: Checking file permissions..."
if [ -d "storage/app/Audios" ]; then
    PERMS=$(stat -c "%a" storage/app/Audios 2>/dev/null || stat -f "%OLp" storage/app/Audios 2>/dev/null || echo "unknown")
    echo "   Directory permissions: $PERMS"
    if [ "$PERMS" != "755" ] && [ "$PERMS" != "775" ]; then
        echo "   ⚠️  Consider setting permissions to 755: chmod 755 storage/app/Audios"
    fi
    
    # Check if directory is writable
    if [ -w "storage/app/Audios" ]; then
        echo "   ✅ Directory is writable"
    else
        echo "   ⚠️  Directory is not writable (may need to upload files manually)"
    fi
fi

echo ""
echo "✅ Check complete!"

