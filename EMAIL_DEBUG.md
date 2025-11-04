# Email Configuration Debugging Guide for Production

## Required .env Configuration for Brevo

Make sure your `.env` file on production has these settings:

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=9abbe9001@smtp-brevo.com
MAIL_PASSWORD=your_brevo_smtp_password_or_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Important: Make sure APP_URL is set correctly
APP_URL=https://yourdomain.com
```

## Common Issues and Solutions

### 1. Emails Not Sending

**Check 1: Verify .env file has correct values**
```bash
# SSH into your production server
cd ~/private/karahanyuze11
cat .env | grep MAIL_
```

**Check 2: Clear config cache**
```bash
php artisan config:clear
php artisan cache:clear
```

**Check 3: Verify mail configuration is loaded**
```bash
php artisan tinker
```
Then in tinker:
```php
config('mail.mailers.smtp.host'); // Should return 'smtp-relay.brevo.com'
config('mail.mailers.smtp.port'); // Should return 587
config('mail.mailers.smtp.username'); // Should return your Brevo username
config('mail.from.address'); // Should return your from address
```

### 2. Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Look for errors like:
- `Connection could not be established`
- `SMTP authentication failed`
- `Expected response code 250 but got code 535`

### 3. Test Email Sending

Create a test route to verify email works:

```php
// Add this temporarily to routes/web.php for testing
Route::get('/test-email', function () {
    try {
        Mail::raw('Test email from Karahanyuze', function ($message) {
            $message->to('your-email@example.com')
                    ->subject('Test Email');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
```

Then visit `/test-email` in your browser to test.

### 4. Verify Brevo SMTP Settings

1. Log into your Brevo account
2. Go to **Settings** > **SMTP & API**
3. Make sure you're using the **SMTP** credentials (not API key)
4. Verify the SMTP password/key is correct
5. Check that your sender email is verified in Brevo

### 5. Check APP_URL

The verification link uses `APP_URL` to generate the full URL. Make sure it's set correctly:

```bash
# Check APP_URL
cat .env | grep APP_URL

# Should be something like:
# APP_URL=https://yourdomain.com
```

If `APP_URL` is wrong, verification links will be broken.

### 6. Port and Encryption Settings

For Brevo:
- Port: `587` (TLS) or `465` (SSL)
- Encryption: `tls` (for port 587) or `ssl` (for port 465)

Make sure both match:
- Port 587 + TLS ✅
- Port 465 + SSL ✅
- Port 587 + SSL ❌
- Port 465 + TLS ❌

### 7. Check Firewall/Security

Some hosting providers block SMTP ports. Verify:
- Port 587 or 465 is not blocked
- Outbound SMTP connections are allowed

### 8. Verify MAIL_FROM_ADDRESS

The `MAIL_FROM_ADDRESS` must be:
- A verified sender in Brevo
- A valid email address
- Match your domain (if using custom domain)

### 9. Test Email Verification Manually

You can test the verification notification directly:

```php
// In tinker
$user = \App\Models\User::where('Email', 'test@example.com')->first();
$user->sendEmailVerificationNotification();
```

### 10. Check Queue Jobs (if using queues)

If you're using queues for email:
```bash
php artisan queue:work
```

Make sure queue workers are running.

## Quick Diagnostic Commands

Run these on production to diagnose issues:

```bash
cd ~/private/karahanyuze11

# 1. Check mail config
php artisan config:show mail

# 2. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. Check logs
tail -50 storage/logs/laravel.log

# 4. Verify .env file
cat .env | grep -E "MAIL_|APP_URL"

# 5. Test database connection
php artisan db:show
```

## Common Error Messages

### "Connection could not be established"
- **Cause**: SMTP server unreachable or port blocked
- **Fix**: Check firewall, verify SMTP host/port

### "SMTP authentication failed"
- **Cause**: Wrong username/password
- **Fix**: Double-check Brevo SMTP credentials

### "Expected response code 250 but got code 535"
- **Cause**: Authentication failed
- **Fix**: Verify username and password are correct

### "Could not instantiate mail driver"
- **Cause**: Wrong MAIL_MAILER value
- **Fix**: Set `MAIL_MAILER=smtp` in .env

## After Fixing Configuration

1. Clear config cache: `php artisan config:clear`
2. Test email sending
3. Check logs for errors
4. Try registering a new user to test verification email

