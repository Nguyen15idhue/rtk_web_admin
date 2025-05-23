<?php
// Recent Activities Tab
?>
<section class="recent-activity content-section">
<h3>Hoạt động hệ thống gần đây</h3>
    <div class="activity-list" id="activity-list">
        <?php if (empty($recent_activities)): ?>
            <p class="text-gray-500 italic">Không có hoạt động nào gần đây.</p>
        <?php else: ?>
            <?php foreach ($recent_activities as $log): ?>
                <?php 
                    // Pass the voucher_details_map to format_activity_log
                    $activity = format_activity_log($log, $voucher_details_map); 
                    $message = $activity['message'];
                    $icon    = $activity['icon'];
                    $time    = $activity['time'];
                    $action_type = $activity['action_type'];
                    $url     = $activity['details_url'];
                    $required_permission = $activity['required_permission'] ?? null;
                    // Determine if click navigation should be enabled
                    $canAccess = $action_type === 'navigate' && 
                                 ($required_permission === null || Auth::can($required_permission));
                ?>
                <div class="activity-item <?php echo $canAccess ? 'activity-item-clickable' : ''; ?>"
                     style="display: flex; align-items: flex-start; gap: .75rem; margin-bottom: .75rem; padding: .5rem 0; border-bottom: 1px solid #e5e7eb; <?php echo $canAccess ? 'cursor:pointer;' : ''; ?>"
                     <?php if ($canAccess): ?> onclick="window.location.href='<?php echo htmlspecialchars($url); ?>'" title="Xem chi tiết" <?php endif; ?>
                >
                    <i class="<?php echo $icon; ?>" style="margin-top:.125rem; flex-shrink:0; width:1.5rem; text-align:center;"></i>
                    <div style="flex-grow:1;">
                        <p style="margin:0; line-height:1.4; font-size:.9rem;"><?php echo $message; ?></p>
                        <small style="color:#6b7280; font-size:.8em;"><?php echo $time; ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
            <style>
                .activity-item:last-child {
                    border-bottom: none;
                    margin-bottom: 0;
                }
            </style>
        <?php endif; ?>
    </div>
</section>
