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

*   Trình duyệt web hiện đại (Chrome, Firefox, Edge, Safari).
*   Thông tin đăng nhập vào trang quản trị (sẽ được cung cấp bởi người cài đặt hoặc quản trị viên hệ thống của bạn).

## Truy Cập Ứng Dụng

Ứng dụng web quản trị RTK đã được cài đặt sẵn trên máy chủ hosting sử dụng cPanel.

Để truy cập ứng dụng:

1.  Mở trình duyệt web của bạn.
2.  Truy cập vào địa chỉ URL đã được cung cấp cho bạn (ví dụ: `https://test2.taikhoandodac.vn`).
3.  Trang mặc định sẽ là trang đăng nhập. Hãy sử dụng thông tin đăng nhập đã được cung cấp để vào hệ thống.

## Tổng Quan Giao Diện

Sau khi đăng nhập thành công, bạn sẽ thấy giao diện quản trị chính, bao gồm:

*   **Thanh điều hướng (Sidebar):** Chứa các menu chính để truy cập các chức năng của hệ thống như Quản lý tài khoản, Quản lý trạm, Hóa đơn, Báo cáo, v.v.
*   **Khu vực nội dung chính:** Hiển thị thông tin và các công cụ tương tác của mục bạn đang chọn.
*   **Header:** Thường chứa thông tin người dùng và nút đăng xuất.

## Cấu hình Cơ bản (Qua Giao Diện)

Một số cài đặt cơ bản của hệ thống có thể được điều chỉnh trực tiếp thông qua giao diện quản trị, thường nằm trong mục "Cài đặt" (`setting/`). Vui lòng tham khảo mục đó để biết thêm chi tiết về các tùy chọn có sẵn.

Đối với các cấu hình kỹ thuật sâu hơn, chúng đã được thực hiện trong quá trình cài đặt ban đầu bởi quản trị viên kỹ thuật.

## Hướng dẫn Sử dụng Chi Tiết

Để xem hướng dẫn chi tiết về cách sử dụng từng tính năng của trang quản trị, vui lòng tham khảo các tài liệu trong thư mục `docs/user_guide/`. Mỗi file markdown trong thư mục này sẽ mô tả một module hoặc một nhóm chức năng cụ thể.

## Xử lý Sự cố Thường gặp (FAQ)

*(Mục này sẽ được cập nhật với các câu hỏi và giải pháp thường gặp trong quá trình sử dụng)*

1.  **Không thể đăng nhập?**
    *   Đảm bảo bạn đã nhập đúng tên đăng nhập và mật khẩu. Kiểm tra kỹ lỗi chính tả và phím Caps Lock.
    *   Nếu bạn quên mật khẩu, hãy tìm tùy chọn "Quên mật khẩu" (nếu có) hoặc liên hệ với quản trị viên hệ thống để được hỗ trợ.

2.  **Trang web hiển thị lỗi hoặc không tải đúng cách?**
    *   Thử làm mới trang (nhấn F5 hoặc Ctrl+R).
    *   Xóa cache và cookies của trình duyệt rồi thử lại.
    *   Kiểm tra kết nối internet của bạn.
    *   Nếu vấn đề vẫn tiếp diễn, vui lòng liên hệ bộ phận hỗ trợ kỹ thuật hoặc quản trị viên của bạn và cung cấp thông tin chi tiết về lỗi (ví dụ: thông báo lỗi, trang nào bị lỗi, bạn đang làm gì khi lỗi xảy ra).

3.  **Một số chức năng không hoạt động như mong đợi?**
    *   Đảm bảo bạn đang sử dụng một trình duyệt web hiện đại và đã được cập nhật (Chrome, Firefox, Edge, Safari).
    *   Tham khảo các tài liệu hướng dẫn trong thư mục `docs/user_guide/` để chắc chắn rằng bạn đang thực hiện đúng các bước.
    *   Nếu vẫn gặp sự cố, liên hệ với quản trị viên hệ thống để được trợ giúp.

## Tổng Quan Các Chức Năng Chính

Hệ thống quản trị bao gồm nhiều module với các chức năng cụ thể. Dưới đây là tổng quan về các khu vực chính. Để xem hướng dẫn chi tiết cho từng chức năng, vui lòng tham khảo các tài liệu trong thư mục `docs/user_guide/`.

*   **Quản lý Tài khoản (`account/`):** Quản lý thông tin, quyền hạn và trạng thái tài khoản người dùng và quản trị viên.
*   **Xác thực & Phân quyền (`auth/`):** Xử lý đăng nhập, đăng xuất và quản lý vai trò, quyền hạn.
*   **Bảng điều khiển (`dashboard/`):** Cung cấp cái nhìn tổng quan về hoạt động hệ thống và các số liệu thống kê quan trọng.
*   **Quản lý Hướng dẫn (`guide/`):** Tạo và quản lý các bài viết hướng dẫn sử dụng.
*   **Quản lý Hóa đơn & Giao dịch (`invoice/`, `purchase/`):** Xử lý hóa đơn, chứng từ, giao dịch mua hàng và theo dõi doanh thu.
*   **Chương trình Giới thiệu (`referral/`):** Quản lý người giới thiệu, hoa hồng và yêu cầu rút tiền.
*   **Báo cáo (`report/`):** Xem và xuất các loại báo cáo khác nhau.
*   **Cài đặt Hệ thống (`setting/`):** Cấu hình các thông số chung cho ứng dụng.
*   **Quản lý Trạm RTK (`station/`):** Quản lý thông tin các trạm RTK.
*   **Hỗ trợ Người dùng (`support/`):** Xử lý các yêu cầu hỗ trợ và ticket.
*   **Quản lý Khách hàng (`user/`):** Quản lý danh sách khách hàng đã đăng ký.
*   **Quản lý Voucher (`voucher/`):** Tạo và quản lý mã giảm giá, chương trình khuyến mãi.

Để biết thêm chi tiết về cách sử dụng từng module, vui lòng tham khảo các tài liệu hướng dẫn cụ thể trong thư mục `docs/user_guide/`.
