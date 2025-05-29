<?php
// filepath: public/handlers/revenue/get_transaction_details.php
header('Content-Type: application/json; charset=utf-8');

// Load constants and private action
require_once dirname(__DIR__, 3) . '/private/config/constants.php';

// Execute processing logic
require_once PRIVATE_ACTIONS_PATH . 'purchase/get_transaction_details.php';
exit;
?>
