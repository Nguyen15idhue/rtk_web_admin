<?php
define('BASE_PATH', dirname(__DIR__));                      
define('BASE_URL',  (isset($_SERVER['HTTPS'])?'https':'http')
                  .'://'.$_SERVER['HTTP_HOST'].'/');

// Thêm hằng số cho private includes/actions
define('PRIVATE_INCLUDES_PATH', BASE_PATH . '/includes/');
define('PRIVATE_ACTIONS_PATH', BASE_PATH . '/actions/');