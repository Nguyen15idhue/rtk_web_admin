<?php
/**
 * Content Header Configuration
 * Centralized configuration for the modernized content header
 */

return [
    // Search Configuration
    'search' => [
        'enabled' => true,
        'placeholder' => 'Tìm kiếm...',
        'min_chars' => 2,
        'max_results' => 10,
        'timeout' => 5000, // milliseconds
        'api_endpoint' => '/public/api/quick_search.php'
    ],
    
    // Notifications Configuration
    'notifications' => [
        'enabled' => true,
        'refresh_interval' => 30000, // milliseconds
        'max_display' => 5,
        'api_endpoint' => '/public/api/notifications.php',
        'sound_enabled' => false
    ],
    
    // System Status Configuration
    'system_status' => [
        'enabled' => true,
        'check_interval' => 60000, // milliseconds
        'api_endpoint' => '/public/api/system_status.php',
        'indicators' => [
            'online' => [
                'text' => 'Online',
                'icon' => 'fas fa-circle',
                'class' => 'online-indicator'
            ],
            'warning' => [
                'text' => 'Cảnh báo',
                'icon' => 'fas fa-exclamation-triangle',
                'class' => 'warning-indicator'
            ],
            'error' => [
                'text' => 'Lỗi',
                'icon' => 'fas fa-times-circle',
                'class' => 'error-indicator'
            ],
            'maintenance' => [
                'text' => 'Bảo trì',
                'icon' => 'fas fa-tools',
                'class' => 'maintenance'
            ]
        ]
    ],
    
    // Quick Actions Configuration
    'quick_actions' => [
        'enabled' => true,
        'actions' => [
            [
                'icon' => 'fas fa-user-plus',
                'text' => 'Thêm người dùng',
                'url' => '/admin/users/create',
                'permission' => 'users.create'
            ],
            [
                'icon' => 'fas fa-box',
                'text' => 'Thêm sản phẩm',
                'url' => '/admin/products/create',
                'permission' => 'products.create'
            ],
            [
                'icon' => 'fas fa-file-alt',
                'text' => 'Tạo báo cáo',
                'url' => '/admin/reports/create',
                'permission' => 'reports.create'
            ],
            [
                'icon' => 'fas fa-cog',
                'text' => 'Cài đặt',
                'url' => '/admin/settings',
                'permission' => 'settings.view'
            ]
        ]
    ],
    
    // User Menu Configuration
    'user_menu' => [
        'enabled' => true,
        'show_avatar' => true,
        'show_name' => true,
        'show_role' => true,
        'menu_items' => [
            [
                'icon' => 'fas fa-user',
                'text' => 'Hồ sơ cá nhân',
                'url' => '/admin/profile'
            ],
            [
                'icon' => 'fas fa-cog',
                'text' => 'Cài đặt',
                'url' => '/admin/settings'
            ],
            [
                'icon' => 'fas fa-bell',
                'text' => 'Thông báo',
                'url' => '/admin/notifications'
            ],
            [
                'icon' => 'fas fa-question-circle',
                'text' => 'Hỗ trợ',
                'url' => '/admin/support'
            ],
            'divider',
            [
                'icon' => 'fas fa-sign-out-alt',
                'text' => 'Đăng xuất',
                'url' => '/admin/logout',
                'class' => 'logout'
            ]
        ]
    ],
    
    // Visual Configuration
    'visual' => [
        'show_clock' => true,
        'clock_format' => 'H:i:s', // PHP date format
        'show_date' => false,
        'animations_enabled' => true,
        'theme' => 'light', // light, dark, auto
        'compact_mode' => false
    ],
    
    // Performance Configuration
    'performance' => [
        'debounce_search' => 300, // milliseconds
        'cache_notifications' => true,
        'lazy_load_menus' => false,
        'preload_quick_actions' => true
    ],
    
    // Security Configuration
    'security' => [
        'csrf_protection' => true,
        'validate_permissions' => true,
        'rate_limit_search' => true,
        'max_search_requests' => 20, // per minute
        'sanitize_input' => true
    ]
];
