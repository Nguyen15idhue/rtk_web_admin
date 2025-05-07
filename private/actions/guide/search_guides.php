<?php
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthenticated(); // Allow any authenticated user to search guides

$paths = require __DIR__ . '/../../includes/page_bootstrap.php';
header('Location: ' 
    . $paths['private_actions_path'] . 'guide/fetch_guides.php?search=' 
    . urlencode($_GET['q'] ?? ''));
