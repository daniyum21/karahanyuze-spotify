# How to Start MySQL

MySQL is installed but needs to be started. Here are the options:

## Option 1: Using Homebrew Services (Recommended)

```bash
brew services start mysql
```

If that doesn't work due to permission issues, try:

```bash
sudo brew services start mysql
```

## Option 2: Manual Start

```bash
sudo mysqld_safe --datadir=/usr/local/var/mysql --user=_mysql &
```

## Option 3: Check if MySQL is already running

Sometimes MySQL might be running but on a different socket. Try:

```bash
mysql -u root -h 127.0.0.1 -P 3306
```

## After MySQL is Running

Once MySQL is started, you can import the database:

```bash
./import-database.sh
```

Or manually:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS biriheco_karanyz CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
mysql -u root biriheco_karanyz < "database export/biriheco_karanyz.sql"
```

## Alternative: Use SQLite for Development

If you prefer to use SQLite for now (simpler for development), we can update the migrations to work with SQLite. Let me know if you'd like that option.

