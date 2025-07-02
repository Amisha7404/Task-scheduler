#!/bin/bash
SCRIPT_PATH="$(cd "$(dirname "$0")"; pwd)/cron.php"
CRON_JOB="0 * * * * php $SCRIPT_PATH"

# Add cron job
(crontab -l 2>/dev/null | grep -v "$SCRIPT_PATH" ; echo "$CRON_JOB") | crontab -
echo "CRON job added to run every hour."
chmod +x setup_cron.sh