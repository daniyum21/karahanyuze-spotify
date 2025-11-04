# Fix Mail From Name

The email is working, but the "From" name shows `${APP_NAME}` instead of the actual app name.

## Quick Fix on Production:

SSH into production and edit the `.env` file:

```bash
cd ~/private/karahanyuze11
nano .env
```

**Change this line:**
```
MAIL_FROM_NAME="${APP_NAME}"
```

**To this:**
```
MAIL_FROM_NAME="Karahanyuze"
```

**Or add this line if it doesn't exist:**
```
APP_NAME="Karahanyuze"
MAIL_FROM_NAME="${APP_NAME}"
```

Then clear the config cache:
```bash
php artisan config:clear
```

This will fix the email "From" name to show "Karahanyuze" instead of "${APP_NAME}".

