# Deployment Guide for HostMonster

This guide explains how to set up the GitHub Actions workflow for automatic deployment to HostMonster.

## Prerequisites

1. GitHub repository set up (already done)
2. HostMonster SSH access credentials
3. HostMonster account with SSH enabled

## Step 1: Configure GitHub Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions → New repository secret

Add the following secrets:

### Required Secrets:

1. **SSH_HOST**
   - Your HostMonster server hostname or IP address
   - Example: `yourdomain.com` or `123.456.789.0`

2. **SSH_USERNAME**
   - Your HostMonster SSH username
   - Example: `yourusername`

3. **SSH_PASSWORD**
   - Your HostMonster SSH password
   - **Note**: It's recommended to use SSH keys instead of passwords for security

4. **SSH_PORT** (Optional)
   - SSH port number (default: 22)
   - Only add if your HostMonster uses a non-standard port

### Recommended: Use SSH Keys Instead of Password

For better security, set up SSH key authentication:

1. Generate SSH key pair (if you don't have one):
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   ```

2. Add the public key to HostMonster:
   - Copy `~/.ssh/id_ed25519.pub`
   - Add it to HostMonster's authorized_keys file via cPanel or SSH

3. Add the private key as a GitHub secret:
   - Secret name: `SSH_PRIVATE_KEY`
   - Value: Contents of `~/.ssh/id_ed25519` (private key)

4. Update the workflow to use SSH keys instead of password (modify the workflow file)

## Step 2: Initial Server Setup

Before the first deployment, you need to set up the server manually:

1. **SSH into your HostMonster server**
   ```bash
   ssh yourusername@yourdomain.com
   ```

2. **Create the directory structure:**
   ```bash
   mkdir -p ~/private/karahanyuze11
   mkdir -p ~/backups
   ```

3. **Create and configure .env file:**
   ```bash
   cd ~/private/karahanyuze11
   # Copy your .env file here or create it manually
   # Make sure it has your production database credentials
   ```

4. **Upload storage files manually (first time only):**
   - Upload `storage/app/Audios/` folder (5.2GB) via FTP/SCP
   - Upload `storage/app/Pictures/` folder (7.9MB) via FTP/SCP
   - Or use rsync:
     ```bash
     rsync -avz storage/app/Audios/ user@host:~/private/karahanyuze11/storage/app/Audios/
     rsync -avz storage/app/Pictures/ user@host:~/private/karahanyuze11/storage/app/Pictures/
     ```

## Step 3: First Deployment

Once secrets are configured:

1. **Push to main branch** - The workflow will trigger automatically
2. **Or manually trigger** - Go to Actions tab → Deploy to HostMonster → Run workflow

## Step 4: Post-Deployment Checklist

After deployment, verify:

- [ ] Website loads correctly
- [ ] Database connection works
- [ ] Audio files are accessible
- [ ] Images load properly
- [ ] Admin login works
- [ ] User registration works
- [ ] Email verification works (if configured)

## Directory Structure on HostMonster

```
~/                          # Home directory
├── private/
│   └── karahanyuze11/      # Laravel application
│       ├── app/
│       ├── bootstrap/
│       ├── config/
│       ├── database/
│       ├── public/         # Will be copied to public_html
│       ├── resources/
│       ├── routes/
│       ├── storage/
│       │   ├── app/
│       │   │   ├── Audios/  # Audio files (uploaded separately)
│       │   │   └── Pictures/ # Picture files (uploaded separately)
│       │   └── ...
│       ├── vendor/
│       └── .env            # Production environment file
├── public_html/            # Web root (public files)
│   ├── index.php          # Updated to point to private directory
│   ├── storage -> ~/private/karahanyuze11/storage/app/public
│   └── ...
└── backups/                # Automated backups
```

## Troubleshooting

### Website not showing any data

If the website is deployed but not showing any data:

1. **SSH into your server:**
   ```bash
   ssh yourusername@yourdomain.com
   ```

2. **Navigate to the app directory:**
   ```bash
   cd ~/private/karahanyuze11
   ```

3. **Check if .env file exists and has correct database credentials:**
   ```bash
   cat .env | grep DB_
   ```
   Make sure these are set correctly:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`

4. **Test database connection:**
   ```bash
   php artisan db:show
   ```

5. **Run migrations (if not already run):**
   ```bash
   php artisan migrate --force
   ```
   This will create any missing tables without overwriting existing data.

6. **Check if data exists in database:**
   ```bash
   php artisan tinker
   ```
   Then in tinker:
   ```php
   DB::table('Indirimbo')->count(); // Should return number of songs
   DB::table('Abahanzi')->count(); // Should return number of artists
   ```

7. **Clear and cache configuration:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan config:cache
   php artisan route:cache
   ```

8. **Check Laravel logs for errors:**
   ```bash
   tail -50 storage/logs/laravel.log
   ```

9. **Verify database has data:**
   - If you imported the old database, data should already be there
   - If tables are empty, you may need to import the SQL dump from `database export` folder

### Deployment fails with SSH connection error
- Verify SSH credentials are correct
- Check if SSH is enabled on HostMonster
- Verify firewall allows SSH connections

### Database connection fails
- Check `.env` file has correct database credentials
- Verify database server is accessible from HostMonster
- Check database user has proper permissions

### Files not found errors
- Verify storage files (Audios/Pictures) are uploaded
- Check file permissions: `chmod -R 755 storage`
- Verify storage link exists: `ls -la ~/public_html/storage`

### Permission errors
- Set proper permissions:
  ```bash
  chmod -R 755 ~/private/karahanyuze11/storage
  chmod -R 755 ~/private/karahanyuze11/bootstrap/cache
  chmod -R 775 ~/private/karahanyuze11/storage/logs
  chmod -R 775 ~/private/karahanyuze11/storage/framework
  ```

## Manual Deployment

If you need to deploy manually:

```bash
# SSH into server
ssh yourusername@yourdomain.com

# Navigate to app directory
cd ~/private/karahanyuze11

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Run migrations
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Notes

- Storage files (audio/pictures) are excluded from deployment package due to size
- These files need to be uploaded separately via FTP/SCP
- The workflow creates backups automatically before each deployment
- Only the last 5 backups are kept to save disk space

