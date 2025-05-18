# Trang Quản Trị Web RTK

## Tổng Quan

Đây là một ứng dụng web quản trị (admin panel) được xây dựng bằng PHP, dùng để quản lý các hoạt động và dữ liệu của hệ thống RTK. Ứng dụng cho phép quản trị viên thực hiện các tác vụ như quản lý tài khoản, xem báo cáo, quản lý trạm, và nhiều chức năng khác.

## Tính Năng Chính

*   Quản lý tài khoản người dùng và quản trị viên.
*   Quản lý trạm RTK.
*   Quản lý hóa đơn và giao dịch.
*   Xem và xuất báo cáo.
*   Quản lý voucher và giới thiệu.
*   Hỗ trợ người dùng.
*   Cài đặt hệ thống.
*   Ghi log hoạt động và lỗi.

## Yêu Cầu Hệ Thống (Người Dùng)

*   Một máy tính có cài đặt Web server (ví dụ: Laragon đã bao gồm Apache, Nginx).
*   Hệ quản trị cơ sở dữ liệu MySQL hoặc MariaDB (Laragon đã bao gồm).
*   Trình duyệt web hiện đại (Chrome, Firefox, Edge, Safari).

## Chạy Ứng Dụng

Sau khi hoàn tất các bước cài đặt, bạn có thể truy cập ứng dụng thông qua URL đã cấu hình trên web server (test2.taikhoandodac.vn).

Trang mặc định sẽ là trang đăng nhập.

## Chức Năng Của Từng Trang (Trong `public/pages/`)

Dưới đây là mô tả chức năng chính của các module trong `public/pages/`:

*   `account/` (`account_management.php`): Quản lý tài khoản người dùng, bao gồm tạo, sửa, xóa, kích hoạt/vô hiệu hóa, gia hạn và tìm kiếm tài khoản.
*   `auth/`:
    *   `admin_login.php`: Đăng nhập và đăng xuất cho quản trị viên.
    *   `permission_management.php`: Quản lý vai trò và quyền hạn của quản trị viên, bao gồm tạo, sửa, xóa vai trò và gán quyền.
*   `dashboard/` (`dashboard.php`): Hiển thị tổng quan các thông số, biểu đồ thống kê và tóm tắt hoạt động của các khách hàng.
*   `error.php`: Trang hiển thị thông báo lỗi chung.
*   `guide/` (`guide_management.php`, `edit_guide.php`): Quản lý nội dung hướng dẫn sử dụng, bao gồm tạo, sửa, xóa và thay đổi trạng thái hiển thị bài viết.
*   `invoice/` (`invoice_review.php`, `invoice_upload.php`): Quản lý hóa đơn. Cho phép xem xét, gửi, từ chối, hoàn lại hóa đơn, tải lên chứng từ, xem lịch sử giao dịch và tổng kết doanh thu.
*   `purchase/` (`invoice_management.php`, `revenue_management.php`): Quản lý các giao dịch mua hàng và doanh thu. Bao gồm duyệt, từ chối, hoàn lại giao dịch và theo dõi doanh thu.
*   `referral/` (`referral_management.php`): Quản lý chương trình giới thiệu người dùng. Bao gồm theo dõi người giới thiệu, người được giới thiệu, hoa hồng và xử lý yêu cầu rút tiền.
*   `report/`: Hiển thị các loại báo cáo khác nhau (ví dụ: báo cáo doanh thu, báo cáo người dùng).
*   `setting/`: Trang cấu hình các cài đặt chung cho hệ thống.
*   `station/`: Quản lý thông tin các trạm RTK.
*   `support/`: Quản lý các yêu cầu hỗ trợ từ người dùng, ticket hỗ trợ.
*   `user/`: Quản lý danh sách khách hàng đăng kí.
*   `voucher/`: Quản lý mã giảm giá, chương trình khuyến mãi.
