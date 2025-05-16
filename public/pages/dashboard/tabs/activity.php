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
                <?php $activity = format_activity_log($log); ?>
                <div class="activity-item" style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                    <i class="<?php echo $activity['icon']; ?>" style="margin-top: 0.125rem; flex-shrink: 0; width: 1.5rem; text-align: center;"></i>
                    <div style="flex-grow: 1;">
                        <p style="margin: 0; line-height: 1.4; font-size: 0.9rem;"><?php echo $activity['message']; ?></p>
                        <small style="color: #6b7280; font-size: 0.8em;"><?php echo $activity['time']; ?></small>
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
