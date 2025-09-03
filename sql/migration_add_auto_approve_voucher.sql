-- Migration: add auto_approve column to voucher table
ALTER TABLE `voucher` 
  ADD COLUMN `auto_approve` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Tự động duyệt đơn khi áp voucher' AFTER `is_active`;

-- To rollback:
-- ALTER TABLE `voucher` DROP COLUMN `auto_approve`;
