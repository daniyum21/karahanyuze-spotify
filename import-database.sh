#!/bin/bash
# Script to import the database from SQL dump

# Create database if it doesn't exist
mysql -u root -e "CREATE DATABASE IF NOT EXISTS biriheco_karanyz CHARACTER SET utf8 COLLATE utf8_unicode_ci;"

# Import the SQL dump
mysql -u root biriheco_karanyz < "database export/biriheco_karanyz.sql"

echo "Database imported successfully!"

