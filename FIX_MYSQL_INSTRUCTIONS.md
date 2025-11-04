# Fix MySQL Configuration

The issue is that MySQL is looking for `/opt/homebrew/var/mysql/` but your data is in `/usr/local/var/mysql/`.

## Quick Fix (Run this in your terminal):

```bash
cd /Users/dnizeyumukiza/Desktop/karahanyuze11
./fix-mysql.sh
```

This will:
1. Update MySQL config to point to `/usr/local/var/mysql/`
2. Start MySQL
3. Test the connection

## Manual Fix (if script doesn't work):

1. Update MySQL config:
```bash
sudo tee /usr/local/etc/my.cnf > /dev/null <<EOF
[mysqld]
datadir=/usr/local/var/mysql
socket=/tmp/mysql.sock
user=_mysql
bind-address = 127.0.0.1

[client]
socket=/tmp/mysql.sock
EOF
```

2. Start MySQL:
```bash
brew services start mysql
```

3. Wait a few seconds, then test:
```bash
mysql -u root -e "SELECT 1;"
```

4. If it works, import the database:
```bash
./import-database.sh
```

## After MySQL is Running

Once MySQL is running successfully, you can import your database:

```bash
./import-database.sh
```

This will create the database `biriheco_karanyz` and import all your data from the SQL dump.

