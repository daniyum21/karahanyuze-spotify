#!/bin/bash

# Fix APP_URL for iwacu.org to prevent path exposure
# Run this on the server: bash fix-iwacu-app-url.sh

echo "🔧 Fixing APP_URL for iwacu.org..."

cd ~/private/karahanyuze-spotify

if [ -f ".env" ]; then
    # Check current APP_URL
    if grep -q "^APP_URL=" .env; then
        APP_URL_VALUE=$(grep "^APP_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        echo "📄 Current APP_URL: $APP_URL_VALUE"
        
        # Fix if it contains file system paths
        if [[ "$APP_URL_VALUE" == *"/home"* ]] || [[ "$APP_URL_VALUE" == *"public_html"* ]] || [[ "$APP_URL_VALUE" != *"iwacu.org"* ]]; then
            echo "❌ APP_URL is incorrect, fixing..."
            sed -i "s|^APP_URL=.*|APP_URL=https://iwacu.org|g" .env
            echo "✅ APP_URL fixed to https://iwacu.org"
        else
            echo "✅ APP_URL is correct"
        fi
    else
        echo "⚠️  APP_URL not set, adding..."
        echo "" >> .env
        echo "APP_URL=https://iwacu.org" >> .env
        echo "✅ APP_URL added"
    fi
    
    # Remove incorrect ASSET_URL
    if grep -q "^ASSET_URL=" .env; then
        ASSET_URL_VALUE=$(grep "^ASSET_URL=" .env | cut -d '=' -f2- | tr -d ' ' | tr -d '"' | tr -d "'")
        if [[ "$ASSET_URL_VALUE" == *"/home"* ]] || [[ "$ASSET_URL_VALUE" == *"public_html"* ]]; then
            echo "❌ ASSET_URL is incorrect: $ASSET_URL_VALUE"
            echo "🔧 Removing incorrect ASSET_URL..."
            sed -i "/^ASSET_URL=/d" .env
            echo "✅ ASSET_URL removed"
        fi
    fi
    
    # Clear Laravel caches
    echo ""
    echo "🧹 Clearing Laravel caches..."
    php artisan config:clear || true
    php artisan cache:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    
    echo ""
    echo "✅ Fix complete! Please test iwacu.org now"
else
    echo "❌ .env file not found!"
fi

