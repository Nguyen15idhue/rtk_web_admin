-- Bật event scheduler (chỉ làm một lần)
SET GLOBAL event_scheduler = ON;

-- Tạo event chạy hàng ngày
CREATE EVENT IF NOT EXISTS ev_delete_old_activity_logs
ON SCHEDULE EVERY 1 DAY
COMMENT 'Xóa activity_logs cũ hơn 7 ngày'
DO
  DELETE FROM `activity_logs`
  WHERE `created_at` < NOW() - INTERVAL 7 DAY;