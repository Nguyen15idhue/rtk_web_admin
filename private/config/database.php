<?php
// filepath: private\config\database.php

$db_server = getenv('DB_SERVER') ?: '127.0.0.1';
$db_username = getenv('DB_USERNAME') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'quantri';

define('DB_SERVER', $db_server);
define('DB_USERNAME', $db_username);
define('DB_PASSWORD', $db_password);
define('DB_NAME', $db_name);

// Base URL for hosted images
define('USER_URL', 'http://localhost:8000/');
?>