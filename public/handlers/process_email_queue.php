<?php
// Public handler for processing email queue, protected by token authentication

// Include token constant
require_once __DIR__ . '/../../private/config/constants.php';

// Include and execute the private cron script
require_once __DIR__ . '/../../private/scripts/process_email_queue.php';
