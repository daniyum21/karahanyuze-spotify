#!/bin/bash
# Script to fix MySQL configuration and start it

echo "Fixing MySQL configuration..."

# Create a my.cnf file that points to the correct data directory
sudo tee /usr/local/etc/my.cnf > /dev/null <<EOF
[mysqld]
datadir=/usr/local/var/mysql
socket=/tmp/mysql.sock
user=_mysql

[client]
socket=/tmp/mysql.sock
EOF

echo "Starting MySQL..."
brew services start mysql

echo "Waiting for MySQL to start..."
sleep 5

echo "Testing connection..."
mysql -u root -e "SELECT 1;" 2>&1

echo "Done! If MySQL is running, you can now import the database with: ./import-database.sh"

