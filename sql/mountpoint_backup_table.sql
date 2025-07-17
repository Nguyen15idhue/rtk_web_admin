-- Tạo bảng backup cho mount_point (tương tự survey_account_backup)
CREATE TABLE `mountpoint_backup` (
  `id` varchar(64) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `ip` varchar(100) NOT NULL,
  `port` int(11) NOT NULL,
  `mountpoint` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `backup_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm index cho bảng backup
ALTER TABLE `mountpoint_backup`
  ADD PRIMARY KEY (`id`, `backup_date`),
  ADD KEY `idx_mountpoint_backup_location_id` (`location_id`),
  ADD KEY `idx_mountpoint_backup_date` (`backup_date`);
