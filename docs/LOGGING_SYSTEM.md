# Enhanced Logging System - RTK Web Admin

## Tổng quan

Hệ thống logging đã được tối ưu hóa để giải quyết vấn đề log files quá lớn và khó theo dõi trên cPanel. Thay vì tất cả log ghi vào một file `error.log`, giờ đây:

### ✨ Tính năng mới

1. **Phân tách log theo mức độ**:
   - `error_2025-06-19.log` - Lỗi nghiêm trọng
   - `warning_2025-06-19.log` - Cảnh báo
   - `info_2025-06-19.log` - Thông tin 
   - `debug_2025-06-19.log` - Debug

2. **Format ngắn gọn và dễ đọc**:
   ```
   [2025-06-19 10:30:15] ERROR: Database connection failed | database.php:45 | Context: host=localhost
   [2025-06-19 10:30:16] WARNING: Cache miss | cache.php:23 | Context: key=user_123
   ```

3. **Auto rotation**: Files tự động xoay khi đạt 5MB
4. **Auto cleanup**: Tự động xóa log files cũ hơn 30 ngày
5. **Dashboard trực quan**: Xem logs realtime qua web interface

### 🚀 Cách sử dụng

#### Trong code PHP:

```php
// Load logger helpers
require_once 'private/utils/logger_helpers.php';

// Logging đơn giản
log_error("Lỗi kết nối database");
log_warning("Cache bị miss");
log_info("User đăng nhập thành công");
log_debug("Query executed", ['query' => $sql, 'time' => $duration]);

// Log exception
try {
    // some code
} catch (Exception $e) {
    log_exception($e, ['user_id' => $userId, 'action' => 'create_account']);
}

// Hoặc sử dụng trực tiếp Logger class
Logger::error("Database error", [
    'query' => $sql,
    'error_code' => $e->getCode()
]);
```

#### Xem logs:

1. **Web Dashboard**: Truy cập `/public/pages/logs.php`
   - Filter theo level (error, warning, info, debug)
   - Real-time refresh
   - Thống kê số lượng log
   - Clear logs

2. **File system**: Logs được lưu trong `/private/logs/`
   ```
   private/logs/
   ├── error_2025-06-19.log      # Lỗi hôm nay
   ├── warning_2025-06-19.log    # Cảnh báo hôm nay
   ├── info_2025-06-19.log       # Info hôm nay
   ├── debug_2025-06-19.log      # Debug hôm nay
   └── error.log                 # Legacy file (vẫn hoạt động)
   ```

### 🔧 Migration từ hệ thống cũ

Không cần thay đổi code hiện tại! Hệ thống mới:

- ✅ Tương thích với `error_log()` cũ
- ✅ Vẫn ghi vào `error.log` cho backward compatibility
- ✅ Tự động phát hiện exceptions và fatal errors
- ✅ Tích hợp với `error_handler.php` hiện tại

### 📊 Lợi ích

**Trước đây**:
```
❌ 1 file error.log khổng lồ (hàng GB)
❌ Stack trace dài dòng, khó đọc  
❌ Phải scroll rất nhiều để tìm lỗi mới
❌ Không phân biệt được mức độ nghiêm trọng
❌ Khó debug trên cPanel
```

**Bây giờ**:
```
✅ Phân chia theo ngày và mức độ
✅ Stack trace được filter, chỉ giữ phần quan trọng
✅ Lỗi mới nhất luôn ở đầu file
✅ Rõ ràng: ERROR vs WARNING vs INFO
✅ Dashboard web tiện lợi
✅ Auto cleanup, không lo đầy disk
```

### 🛠️ Cấu hình

Có thể tùy chỉnh trong `Logger.php`:

```php
private $maxFileSize = 5 * 1024 * 1024; // 5MB per file
private $maxFiles = 7; // Keep 7 days
```

### 📝 Notes

- Log files sử dụng UTF-8 encoding
- Timestamp theo timezone máy chủ  
- Context data được JSON encode
- Stack traces chỉ hiển thị 5 frames quan trọng nhất
- Files rotate theo kích thước, không theo thời gian

---


