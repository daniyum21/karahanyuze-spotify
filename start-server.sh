#!/bin/bash
# Start Laravel development server with increased upload limits
php -d upload_max_filesize=500M -d post_max_size=1024M -d max_execution_time=600 -d max_input_time=600 -d memory_limit=512M artisan serve

