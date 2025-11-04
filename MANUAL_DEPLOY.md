# Manual Deployment Guide

If GitHub Actions deployment keeps failing due to SSH connection issues, you can deploy manually using these steps:

## Option 1: Manual Deployment via SCP (Recommended)

1. **Build the package locally:**
   ```bash
   cd /Users/dnizeyumukiza/Desktop/karahanyuze11
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev --prefer-dist --no-interaction
   npm ci
   npm run build
   
   # Create deployment package
   mkdir -p deploy-package
   rsync -av \
     --exclude='.git' \
     --exclude='node_modules' \
     --exclude='tests' \
     --exclude='storage/logs/*' \
     --exclude='storage/framework/cache/*' \
     --exclude='storage/framework/sessions/*' \
     --exclude='storage/framework/views/*' \
     --exclude='storage/app/Audios' \
     --exclude='storage/app/Pictures' \
     --exclude='storage/app/public' \
     --exclude='.env' \
     --exclude='.env.example' \
     --exclude='deploy-package' \
     --exclude='docker-compose.yml' \
     --exclude='.github' \
     --exclude='*.md' \
     --exclude='.gitignore' \
     --exclude='.gitattributes' \
     --exclude='.editorconfig' \
     . deploy-package/
   
   tar -czf deploy-package.tar.gz -C deploy-package .
   ```

2. **Upload to HostMonster:**
   ```bash
   scp deploy-package.tar.gz yourusername@karahanyuze.com:~/deploy-package.tar.gz
   ```

3. **SSH into HostMonster and deploy:**
   ```bash
   ssh yourusername@karahanyuze.com
   
   cd ~/private/karahanyuze11
   
   # Extract
   tar -xzf ~/deploy-package.tar.gz -C ~/private/karahanyuze11
   
   # Copy public files
   rsync -av --delete ~/private/karahanyuze11/public/ ~/public_html/
   
   # Update index.php
   cd ~/public_html
   if [ -f "index.php" ]; then
     cp index.php index.php.backup
     sed -i "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/../private/karahanyuze11/vendor/autoload.php'|g" index.php
     sed -i "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/../private/karahanyuze11/bootstrap/app.php'|g" index.php
   fi
   
   # Run migrations
   cd ~/private/karahanyuze11
   php artisan migrate --force
   
   # Clear and cache
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Clean up
   rm -f ~/deploy-package.tar.gz
   ```

## Option 2: Use FTP/SFTP Client

1. Build the package as above
2. Use an FTP client (FileZilla, Cyberduck, etc.) to upload `deploy-package.tar.gz`
3. SSH into HostMonster and follow steps 3 above

## Option 3: Fix SSH Issues

If SSH keeps failing, check:

1. **SSH is enabled in HostMonster cPanel**
   - Go to cPanel → Security → SSH Access
   - Make sure SSH is enabled

2. **SSH port is correct**
   - Default is port 22
   - Some hosting providers use different ports (2222, 7822, etc.)
   - Check your HostMonster SSH settings

3. **Firewall/Whitelist**
   - GitHub Actions runners use dynamic IPs
   - HostMonster might be blocking connections
   - Check if you can whitelist GitHub IP ranges

4. **Try SSH keys instead of password**
   - More reliable than password authentication
   - Generate SSH key pair and add public key to HostMonster

## Troubleshooting SSH Connection Issues

If you get "connection refused":
- SSH service might be down on HostMonster
- Port might be blocked
- Firewall might be blocking connections

If you get "connection reset by peer":
- Server might be rate limiting
- Connection might be timing out
- Try again after a few minutes

If SSH consistently fails, consider using FTP/SFTP for deployment instead.

