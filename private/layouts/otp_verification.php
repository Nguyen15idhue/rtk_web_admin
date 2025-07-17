<?php
// OTP Verification Form Template
// Variables available: $base_path, $otp_error, $otp_session_key
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP - Nhật ký Hệ thống</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/pages/otp.css">
</head>
<body>
    <div class="otp-container">
        <div class="otp-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h2 class="otp-title">Xác thực Bảo mật</h2>
        <p class="otp-subtitle">
            Trang nhật ký hệ thống yêu cầu xác thực OTP để truy cập.<br>
            Vui lòng nhập mã OTP để tiếp tục.
        </p>
        <?php if (isset($otp_error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($otp_error); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" id="otp-form">
            <div class="otp-inputs">
                <input type="text" class="otp-input" data-index="0" maxlength="1" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-input" data-index="1" maxlength="1" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-input" data-index="2" maxlength="1" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-input" data-index="3" maxlength="1" pattern="[0-9]" autocomplete="off">
            </div>
            <input type="hidden" name="otp" id="hidden-otp">
            <input type="hidden" name="verify_otp" value="1">
            <button type="submit" name="verify_otp" class="btn otp-submit" id="otp-submit" disabled>
                <i class="fas fa-unlock"></i>
                Xác thực & Truy cập
            </button>
        </form>
        <div class="security-notice">
            <i class="fas fa-info-circle"></i>
            Trang này chứa thông tin nhạy cảm về hệ thống và được bảo vệ bằng OTP.
        </div>
    </div>
    <script src="<?php echo $base_url; ?>public/assets/js/pages/otp.js"></script>
</body>
</html>
