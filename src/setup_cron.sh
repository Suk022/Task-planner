#!/bin/bash

# directory path : windows -> wsl format
WINDOWS_PATH="$(pwd)"
WSL_PATH=$(wslpath -a "$WINDOWS_PATH")

SCRIPT_PATH="$WSL_PATH/cron.php"

# php availability in WSL env
PHP_PATH=$(which php)

if [ -z "$PHP_PATH" ]; then
    echo "Error: PHP not found in WSL. Please install PHP in your WSL environment:"
    echo "sudo apt update"
    echo "sudo apt install php"
    exit 1
fi

CRON_CMD="0 * * * * $PHP_PATH $SCRIPT_PATH"
    
# cmd for avoding duplication of existing cron job
(crontab -l 2>/dev/null | grep -v "$SCRIPT_PATH") | crontab -

# add new CRON job
(crontab -l 2>/dev/null; echo "$CRON_CMD") | crontab -
    
# Verify CRON installation
if crontab -l | grep -q "$SCRIPT_PATH"; then
    echo "CRON job has been set up successfully to run every hour"
    echo "The script will run at the start of every hour"
    echo "PHP Path: $PHP_PATH"
    echo "Script Path: $SCRIPT_PATH"
    echo ""
    echo "To check CRON status:"
    echo "crontab -l"
    echo ""
    echo "To check CRON logs:"
    echo "grep CRON /var/log/syslog"
else
    echo "Error: Failed to set up CRON job"
    exit 1
fi
