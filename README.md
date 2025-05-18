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

## Yêu Cầu Hệ Thống

*   PHP (khuyến nghị phiên bản 7.4 trở lên, kiểm tra `composer.json` để biết phiên bản cụ thể nếu có).
*   Web server (ví dụ: Apache, Nginx - Laragon đã bao gồm).
*   MySQL hoặc MariaDB.
*   Composer để quản lý dependencies.

## Hướng Dẫn Cài Đặt

1.  **Clone Repository:**
    Nếu mã nguồn được quản lý bằng Git, clone repository về máy của bạn. Nếu không, đảm bảo bạn có toàn bộ thư mục mã nguồn.

2.  **Cài Đặt Dependencies:**
    Mở terminal hoặc command prompt, điều hướng đến thư mục gốc của dự án (`e:\Application\laragon\www\rtk_web_admin`) và chạy lệnh sau để cài đặt các thư viện cần thiết:
    ```bash
    composer install
    ```

3.  **Cấu Hình Cơ Sở Dữ Liệu:**
    *   Tạo một cơ sở dữ liệu mới trong MySQL/MariaDB (ví dụ: `sa3`).
    *   Import dữ liệu từ file `sa3 (1).sql` vào cơ sở dữ liệu vừa tạo.
        ```bash
        # Ví dụ sử dụng mysql client, thay thế username và database_name cho phù hợp
        mysql -u your_username -p your_database_name < "sa3 (1).sql"
        ```
    *   Cấu hình thông tin kết nối cơ sở dữ liệu trong file `private/config/database.php`.
        Bạn có thể cần tạo các biến môi trường `DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME` hoặc sửa trực tiếp các giá trị mặc định trong file:
        ```php
        // private/config/database.php
        $db_server = getenv('DB_SERVER') ?: '127.0.0.1'; // Hoặc điền trực tiếp IP/hostname
        $db_username = getenv('DB_USERNAME') ?: 'root';    // Hoặc điền trực tiếp username
        $db_password = getenv('DB_PASSWORD') ?: '';       // Hoặc điền trực tiếp password
        $db_name = getenv('DB_NAME') ?: 'sa3';        // Hoặc điền trực tiếp tên database
        ```

4.  **Cấu Hình Web Server:**
    *   Nếu bạn sử dụng Laragon, trỏ Document Root của một site mới vào thư mục `public` của dự án (`e:\Application\laragon\www\rtk_web_admin\public`).
    *   Đối với các web server khác, cấu hình Document Root tương tự. Đảm bảo URL rewriting (ví dụ: `mod_rewrite` cho Apache) được kích hoạt nếu ứng dụng sử dụng URL thân thiện.

## Chạy Ứng Dụng

Sau khi hoàn tất các bước cài đặt, bạn có thể truy cập ứng dụng thông qua URL đã cấu hình trên web server (ví dụ: `http://rtk_web_admin.test`).

Trang mặc định sẽ là trang đăng nhập.

## Cấu Trúc Dự Án (Sơ Lược)

*   `private/`: Chứa toàn bộ mã nguồn backend của ứng dụng.
    *   `actions/`: Logic xử lý cho các yêu cầu cụ thể.
    *   `classes/`: Các lớp đối tượng (Models) và lớp tiện ích (Database, Auth).
    *   `config/`: Các file cấu hình hệ thống.
    *   `core/`: Các file khởi tạo và cốt lõi của ứng dụng.
    *   `layouts/`: Các thành phần giao diện chung.
    *   `services/`: Các lớp dịch vụ (ví dụ: xuất Excel).
*   `public/`: Thư mục gốc cho web server, chứa các file có thể truy cập công khai.
    *   `index.php`: Điểm vào chính của ứng dụng.
    *   `assets/`: Tài nguyên tĩnh (CSS, JavaScript, hình ảnh).
    *   `pages/`: Các file giao diện cho từng trang.
*   `vendor/`: Thư mục chứa các thư viện được quản lý bởi Composer.
*   `composer.json`: Định nghĩa các dependencies của dự án.
*   `sa3 (1).sql`: File dump cơ sở dữ liệu ban đầu.

## Luồng Xử Lý Lỗi

Khi có lỗi xảy ra trong quá trình hoạt động của ứng dụng:

1.  **Bắt Lỗi:** Hệ thống sử dụng một trình xử lý lỗi tùy chỉnh được định nghĩa trong `private/core/error_handler.php`. Trình xử lý này sẽ bắt các lỗi PHP, ngoại lệ (exceptions) không được xử lý.
2.  **Ghi Log:** Thông tin chi tiết về lỗi (bao gồm thông báo lỗi, file, dòng gây lỗi, và stack trace) sẽ được ghi vào file `private/logs/error.log`. Điều này giúp cho việc debug và theo dõi lỗi sau này.
3.  **Hiển Thị Thông Báo:**
    *   Đối với các lỗi nghiêm trọng hoặc không thể phục hồi, người dùng có thể được chuyển hướng đến một trang lỗi chung (ví dụ: `public/pages/error.php`) hiển thị thông báo thân thiện, tránh lộ thông tin nhạy cảm.
    *   Đối với các lỗi trong quá trình xử lý API hoặc các tác vụ cụ thể, hệ thống có thể trả về thông báo lỗi dưới dạng JSON hoặc hiển thị trực tiếp trên giao diện người dùng.

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

## Đóng Góp

Nếu bạn muốn đóng góp vào dự án, vui lòng tuân theo các coding convention hiện có (dự án sử dụng PHP CS Fixer để kiểm tra).

## Giấy Phép

(Thông tin giấy phép - nếu có)
