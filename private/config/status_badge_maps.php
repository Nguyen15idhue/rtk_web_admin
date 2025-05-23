<?php
// filepath: private\config\status_badge_maps.php
return [
    'account'    => [
        'active'    => ['class' => 'badge-green',  'text' => 'Hoạt động'],
        'pending'   => ['class' => 'badge-yellow', 'text' => 'Chờ KH'],
        'expired'   => ['class' => 'badge-red',    'text' => 'Hết hạn'],
        'suspended' => ['class' => 'badge-gray',   'text' => 'Đình chỉ'],
        'rejected'  => ['class' => 'badge-red',    'text' => 'Bị từ chối'],
    ],
    'withdrawal' => [
        'pending'   => ['class' => 'badge-yellow', 'text' => 'Chờ xử lý'],
        'completed' => ['class' => 'badge-green',  'text' => 'Hoàn thành'],
        'rejected'  => ['class' => 'badge-red',    'text' => 'Từ chối'],
    ],
    'commission' => [
        'pending'   => ['class' => 'badge-yellow', 'text' => 'Chờ xử lý'],
        'approved'  => ['class' => 'badge-green',  'text' => 'Đã duyệt'],
        'paid'      => ['class' => 'badge-blue',   'text' => 'Đã thanh toán'],
        'cancelled' => ['class' => 'badge-gray',   'text' => 'Hủy'],
    ],
    'voucher'    => [
        'active'   => ['class' => 'badge-green', 'text' => 'Hoạt động'],
        'inactive' => ['class' => 'badge-red',   'text' => 'Vô hiệu hóa'],
        'expired'  => ['class' => 'badge-red',   'text' => 'Hết hạn'],
    ],
    'transaction' => [
        'active'    => ['class' => 'badge-green',  'text' => 'Thành công'],
        'pending'   => ['class' => 'badge-yellow', 'text' => 'Đang chờ'],
        'rejected'  => ['class' => 'badge-red',    'text' => 'Bị từ chối'],
    ],
    'invoice' => [
        'pending'   => ['class' => 'badge-yellow', 'text' => 'Chờ duyệt'],
        'approved'  => ['class' => 'badge-green',  'text' => 'Đã duyệt'],
        'rejected'  => ['class' => 'badge-red',    'text' => 'Từ chối'],
    ],
    'station' => [
        '0' => ['class' => 'badge-gray',   'text' => 'Stop'],
        '1' => ['class' => 'badge-success', 'text' => 'Online'],
        '2' => ['class' => 'badge-warning', 'text' => 'No Data'],
        '3' => ['class' => 'badge-danger',  'text' => 'Offline'],
    ],
    'support' => [
        'pending'     => ['class' => 'badge-yellow',    'text' => 'Chờ xử lý'],
        'in_progress' => ['class' => 'badge-info',      'text' => 'Đang xử lý'],
        'resolved'    => ['class' => 'badge-green',     'text' => 'Đã giải quyết'],
        'closed'      => ['class' => 'badge-secondary', 'text' => 'Đã đóng'],
    ],
];
?>
