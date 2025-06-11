<?php
require_once 'functions.php';

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

// debugging info
error_log("Cron job started at " . date('Y-m-d H:i:s'));

$tasks = getAllTasks();
$subscribers = getSubscribers();

error_log("Found " . count($tasks) . " tasks and " . count($subscribers) . " subscribers");

sendTaskReminders();

error_log("Cron job completed at " . date('Y-m-d H:i:s'));