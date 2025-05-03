<?php
$paths = require __DIR__ . '/../../includes/page_bootstrap.php';
header('Location: ' 
    . $paths['private_actions_path'] . 'guide/fetch_guides.php?search=' 
    . urlencode($_GET['q'] ?? ''));
