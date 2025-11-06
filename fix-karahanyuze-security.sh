#!/bin/bash

# CRITICAL SECURITY FIX for karahanyuze.com path exposure
# Run this immediately on the server: bash fix-karahanyuze-security.sh

echo "🔒 CRITICAL SECURITY FIX: Preventing path exposure in public_html/.htaccess"
echo ""

cd ~/public_html

# Backup existing .htaccess
if [ -f ".htaccess" ]; then
    echo "📦 Backing up existing .htaccess..."
    cp .htaccess .htaccess.insecure-$(date +%Y%m%d-%H%M%S)
    echo "✅ Backup created"
else
    echo "⚠️  Warning: .htaccess not found, creating new one..."
fi

# Create secure .htaccess (preserving existing rules but adding security)
echo "🔧 Updating .htaccess with security rules..."
# Read existing .htaccess and add security rules at the top
if [ -f ".htaccess" ]; then
    # Create a temporary file with security rules
    cat > .htaccess.tmp << 'HTACCESS_HEADER'
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

    # SECURITY: Prevent path exposure and directory traversal
    RewriteCond %{REQUEST_URI} "\.\." [OR]
    RewriteCond %{REQUEST_URI} "\.\./" [OR]
    RewriteCond %{REQUEST_URI} "/home" [OR]
    RewriteCond %{REQUEST_URI} "/private"
    RewriteRule .* - [F,L]

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
HTACCESS_HEADER

    mv .htaccess.tmp .htaccess
    echo "✅ Secure .htaccess created"
else
    echo "⚠️  Error: Could not create .htaccess"
fi

echo ""
echo "🔍 Verifying .htaccess..."
if [ -f ".htaccess" ]; then
    echo "✅ .htaccess exists"
    echo "📄 Security rules (first 30 lines):"
    head -30 .htaccess
    echo ""
    echo "✅ Security fix applied!"
    echo "⚠️  Please test karahanyuze.com immediately to ensure it works correctly"
else
    echo "❌ Error: .htaccess not created!"
fi

