<?php
// filepath: public/handlers/referral/process_withdrawal.php
header('Content-Type: application/json; charset=utf-8');
// Load constants and private action
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
// Execute processing logic
require_once PRIVATE_ACTIONS_PATH . 'referral/process_withdrawal.php';
exit;
