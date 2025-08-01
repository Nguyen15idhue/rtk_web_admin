-- Tạo bảng customer_source_rules để lưu các rule phân nhóm khách hàng theo voucher
CREATE TABLE IF NOT EXISTS customer_source_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher VARCHAR(255) NOT NULL,
    `group` VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
