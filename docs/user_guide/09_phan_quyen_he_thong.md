# Phân quyền Hệ thống - Hướng dẫn Nhanh

**Truy cập:** Menu > QL phân quyền

## ⚡ Thao tác Thường dùng

### 🛡️ Quản lý Vai trò (Roles)
1. **Tạo vai trò mới** → Điền tên và mô tả
2. **Gán quyền hạn** → Chọn các permission cần thiết
3. **Lưu vai trò** → Sẵn sàng gán cho user

### 🔑 Quản lý Quyền hạn (Permissions)
- **Xem danh sách**: Tất cả quyền trong hệ thống
- **Tạo quyền mới**: Cho tính năng mới
- **Gán quyền cho vai trò**: Permission → Role mapping
- **Kiểm tra quyền user**: Xem user có quyền gì

### 👤 Gán Vai trò cho User
1. **Tìm user** cần phân quyền
2. **Sửa thông tin** → Tab "Vai trò"
3. **Chọn vai trò** phù hợp → Lưu
4. User **đăng nhập lại** để có quyền mới

## 🏗️ Cấu trúc Phân quyền

### Vai trò Hệ thống
- **Admin**: Toàn quyền, không thể xóa

### Nhóm Quyền Chính

**Dashboard & Reports (dashboard_):**
- `dashboard_view`: Xem trang chủ
- `reports_view`: Xem báo cáo

**User Management (users_management_):**
- `users_management_view`: Xem danh sách user
- `users_management_edit`: Sửa thông tin user

**Account Management (accounts_management_):**
- `accounts_management_view`: Xem tài khoản đo đạc
- `accounts_management_edit`: Sửa tài khoản

**Transaction Management (invoice_management_):**
- `invoice_management_view`: Xem giao dịch
- `invoice_management_edit`: Sửa giao dịch

**Station Management (stations_management_):**
- `stations_management_view`: Xem trạm RTK
- `stations_management_edit`: Sửa cấu hình trạm

## 🔧 Cài đặt Phân quyền

### Tạo Vai trò Tùy chỉnh
1. **Phân tích nhu cầu**: Xác định user cần làm gì
2. **Chọn quyền tối thiểu**: Nguyên tắc least privilege
3. **Test vai trò**: Thử nghiệm với user test
4. **Điều chỉnh**: Thêm/bớt quyền theo feedback

## 🔒 Bảo mật & Best Practices

### Nguyên tắc Phân quyền
- **Least Privilege**: Chỉ cấp quyền tối thiểu cần thiết
- **Role-based**: Dùng vai trò thay vì phân quyền trực tiếp
- **Regular Review**: Định kỳ kiểm tra và cập nhật quyền
- **Audit Trail**: Theo dõi ai làm gì, khi nào

### Kiểm soát Truy cập
- **Strong Password**: Bắt buộc mật khẩu mạnh
- **Session Timeout**: Tự động đăng xuất sau thời gian nhàn rỗi
- **IP Whitelist**: Giới hạn IP truy cập cho Super Admin
- **Two-Factor**: Bật 2FA cho các tài khoản quan trọng

### Quy trình Cấp quyền
1. **Đề xuất**: Manager đề xuất quyền cần thiết
2. **Phê duyệt**: Super Admin xem xét và phê duyệt
3. **Thực hiện**: Cấp quyền theo đúng yêu cầu
4. **Thông báo**: Inform user về quyền mới
5. **Monitor**: Theo dõi việc sử dụng quyền

## 🔧 Xử lý Tình huống

**User không có quyền truy cập:**
- Kiểm tra vai trò được gán
- Xem vai trò có đúng quyền không
- Yêu cầu user đăng nhập lại

**Cần tạo vai trò mới:**
- Phân tích requirement cụ thể
- Copy từ vai trò tương tự và điều chỉnh
- Test kỹ trước khi deploy

**Phát hiện lạm dụng quyền:**
- Thu hồi quyền ngay lập tức
- Audit log để tìm ra vấn đề
- Báo cáo và xử lý theo quy định

**Backup và Restore quyền:**
- Xuất cấu hình quyền ra file
- Backup định kỳ cài đặt phân quyền
- Có plan restore khi cần thiết

## ⚠️ Lưu ý Quan trọng
- Không bao giờ xóa vai trò Super Admin
- Test kỹ mọi thay đổi về quyền hạn
- Giữ log đầy đủ về việc thay đổi quyền
- Có ít nhất 2 Super Admin để backup
- Thường xuyên review và clean up quyền không dùng
