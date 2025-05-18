# Trang Quản Trị Web RTK (Dành cho Lập Trình Viên)

## Tổng Quan

Đây là một ứng dụng web quản trị (admin panel) được xây dựng bằng PHP, dùng để quản lý các hoạt động và dữ liệu của hệ thống RTK. Ứng dụng cho phép quản trị viên thực hiện các tác vụ như quản lý tài khoản, xem báo cáo, quản lý trạm, và nhiều chức năng khác.

## Yêu Cầu Hệ Thống

*   PHP (khuyến nghị phiên bản 7.4 trở lên, kiểm tra `composer.json` để biết phiên bản cụ thể nếu có).
*   Web server (ví dụ: Apache, Nginx - Laragon đã bao gồm).
*   MySQL hoặc MariaDB.
*   Composer để quản lý dependencies.

## Hướng Dẫn Cài Đặt

1.  **Clone Repository:**
    Nếu mã nguồn được quản lý bằng Git, clone repository về máy của bạn. Nếu không, đảm bảo bạn có toàn bộ thư mục mã nguồn.
    ```bash
    # git clone <your-repo-url> rtk_web_admin
    cd rtk_web_admin
    ```

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
        *Khuyến nghị: Sử dụng file `.env` và một thư viện như `vlucas/phpdotenv` để quản lý biến môi trường, thay vì `getenv()` trực tiếp hoặc hardcode.*

4.  **Cấu Hình Web Server:**
    *   Nếu bạn sử dụng Laragon, trỏ Document Root của một site mới vào thư mục `public` của dự án (`e:\Application\laragon\www\rtk_web_admin\public`).
    *   Đối với các web server khác, cấu hình Document Root tương tự. Đảm bảo URL rewriting (ví dụ: `mod_rewrite` cho Apache) được kích hoạt nếu ứng dụng sử dụng URL thân thiện. Một file `.htaccess` mẫu cho Apache thường được đặt trong thư mục `public`.

## Chạy Ứng Dụng

Sau khi hoàn tất các bước cài đặt, bạn có thể truy cập ứng dụng thông qua URL đã cấu hình trên web server (ví dụ: `http://rtk_web_admin.test`).

Trang mặc định sẽ là trang đăng nhập.

## Cấu Trúc Dự Án (Sơ Lược)

*   `private/`: Chứa toàn bộ mã nguồn backend của ứng dụng.
    *   `actions/`: Logic xử lý cho các yêu cầu cụ thể (thường là các file PHP được include/require bởi các trang trong `public/pages` hoặc `public/handlers`).
    *   `classes/`: Các lớp đối tượng (Models ví dụ: `UserModel.php`, `InvoiceModel.php`) và lớp tiện ích (ví dụ: `Database.php`, `Auth.php`).
    *   `config/`: Các file cấu hình hệ thống (kết nối CSDL, hằng số, đường dẫn, session,...).
    *   `core/`: Các file khởi tạo và cốt lõi của ứng dụng (ví dụ: `page_bootstrap.php` để khởi tạo session, CSDL; `error_handler.php`).
    *   `layouts/`: Các thành phần giao diện chung (header, footer, sidebar) được include vào các trang.
    *   `services/`: Các lớp dịch vụ cho các tác vụ phức tạp hoặc tái sử dụng (ví dụ: `ExcelExportService.php`).
    *   `logs/`: Chứa file log lỗi (ví dụ: `error.log`).
    *   `utils/`: Các hàm tiện ích helper.
*   `public/`: Thư mục gốc cho web server, chứa các file có thể truy cập công khai.
    *   `index.php`: Điểm vào chính của ứng dụng (có thể không phải là điểm vào duy nhất nếu các trang được truy cập trực tiếp).
    *   `assets/`: Tài nguyên tĩnh (CSS, JavaScript, hình ảnh).
    *   `pages/`: Các file PHP định nghĩa giao diện và logic hiển thị cho từng trang/module của ứng dụng.
    *   `handlers/`: (Nếu có) Các file PHP xử lý yêu cầu từ client (ví dụ: form submissions, AJAX requests), thường gọi tới `actions/` hoặc `services/`.
    *   `uploads/`: (Nếu có) Thư mục chứa các file được người dùng tải lên.
*   `vendor/`: Thư mục chứa các thư viện được quản lý bởi Composer.
*   `composer.json`: Định nghĩa các dependencies của dự án và các script Composer.
*   `composer.lock`: Ghi lại phiên bản cụ thể của các thư viện đã cài đặt.
*   `sa3 (1).sql`: File dump cơ sở dữ liệu ban đầu.

## Luồng Xử Lý Lỗi

Khi có lỗi xảy ra trong quá trình hoạt động của ứng dụng:

1.  **Bắt Lỗi:** Hệ thống sử dụng một trình xử lý lỗi tùy chỉnh được định nghĩa trong `private/core/error_handler.php`. Trình xử lý này (`customErrorHandler`) được đăng ký thông qua `set_error_handler()` và `set_exception_handler()`. Nó sẽ bắt các lỗi PHP (warnings, notices, errors) và các ngoại lệ (exceptions) không được xử lý.
2.  **Ghi Log:** Thông tin chi tiết về lỗi (bao gồm loại lỗi, thông báo lỗi, file, dòng gây lỗi, và stack trace cho exceptions) sẽ được định dạng và ghi vào file `private/logs/error.log` thông qua hàm `error_log()`. Điều này giúp cho việc debug và theo dõi lỗi sau này.
3.  **Hiển Thị Thông Báo:**
    *   Trong môi trường phát triển (thường được kiểm tra qua một hằng số như `APP_ENV === 'development'` định nghĩa trong `private/config/constants.php` hoặc tương tự), lỗi có thể được hiển thị trực tiếp để dễ debug.
    *   Trong môi trường production, để tránh lộ thông tin nhạy cảm, người dùng thường được chuyển hướng đến một trang lỗi chung (ví dụ: `public/pages/error.php`) hiển thị thông báo thân thiện. Logic chuyển hướng này có thể nằm trong `customErrorHandler` hoặc tại các điểm bắt lỗi cụ thể trong ứng dụng.
    *   Đối với các lỗi trong quá trình xử lý AJAX hoặc các tác vụ API, hệ thống có thể trả về thông báo lỗi dưới dạng JSON.

## Chức Năng Của Từng Trang (Trong `public/pages/`)

Dưới đây là mô tả chức năng chính của các module trong `public/pages/`. Các file xử lý logic liên quan thường nằm trong `private/actions/` hoặc được gọi từ các file trong `public/handlers/` (nếu có). Models tương ứng nằm trong `private/classes/`.

*   `account/` (`account_management.php`): Quản lý tài khoản người dùng.
*   `auth/`:
    *   `admin_login.php`: Đăng nhập và đăng xuất cho quản trị viên.
    *   `permission_management.php`: Quản lý vai trò và quyền hạn của quản trị viên.
*   `dashboard/` (`dashboard.php`): Hiển thị tổng quan các thông số, biểu đồ thống kê.
*   `error.php`: Trang hiển thị thông báo lỗi chung.
*   `guide/` (`guide_management.php`, `edit_guide.php`): Quản lý nội dung hướng dẫn sử dụng.
*   `invoice/` (`invoice_review.php`, `invoice_upload.php`): Quản lý hóa đơn.
*   `purchase/` (`invoice_management.php`, `revenue_management.php`): Quản lý các giao dịch mua hàng và doanh thu.
*   `referral/` (`referral_management.php`): Quản lý chương trình giới thiệu người dùng.
*   `report/`: Hiển thị các loại báo cáo khác nhau.
*   `setting/`: Trang cấu hình các cài đặt chung cho hệ thống.
*   `station/`: Quản lý thông tin các trạm RTK.
*   `support/`: Quản lý các yêu cầu hỗ trợ từ người dùng.
*   `user/`: Quản lý danh sách khách hàng đăng kí.
*   `voucher/`: Quản lý mã giảm giá, chương trình khuyến mãi.

## Đóng Góp

Nếu bạn muốn đóng góp vào dự án, vui lòng tuân theo các coding convention hiện có.
*   Sử dụng PHP CS Fixer để kiểm tra và định dạng code. Cấu hình có thể nằm trong `.php-cs-fixer.dist.php`.
    ```bash
    # Chạy PHP CS Fixer
    vendor/bin/php-cs-fixer fix .
    ```
*   Tạo branch mới cho mỗi feature hoặc bug fix.
*   Viết commit messages rõ ràng.
*   Tạo Pull Request để review.

## Giấy Phép

(Thông tin giấy phép - nếu có, ví dụ: MIT, GPL, etc.)
