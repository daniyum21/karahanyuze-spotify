#!/bin/bash
# Start Laravel development server with increased upload limits
# Suppress broken pipe notices (harmless errors from PHP built-in server)
# These occur when client disconnects before server finishes writing
php -d upload_max_filesize=500M -d post_max_size=1024M -d max_execution_time=600 -d max_input_time=600 -d memory_limit=512M artisan serve 2>&1 | grep -v "Broken pipe" | grep -v "file_put_contents" | grep -v "errno=32" || true

