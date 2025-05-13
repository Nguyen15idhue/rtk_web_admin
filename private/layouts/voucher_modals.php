<?php
// filepath: private/layouts/voucher_modals.php
?>

<!-- View Voucher Modal -->
<div id="viewVoucherModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Chi tiết Voucher</h4>
            <span class="modal-close" onclick="helpers.closeModal('viewVoucherModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewVoucherDetailsBody">
            <p>Đang tải...</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="helpers.closeModal('viewVoucherModal')">Đóng</button>
        </div>
    </div>
</div>

<!-- Create/Edit Voucher Modal -->
<div id="voucherFormModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="voucherFormTitle">Thêm Voucher</h4>
            <span class="modal-close" onclick="helpers.closeModal('voucherFormModal')">&times;</span>
        </div>
        <form id="voucherForm">
            <input type="hidden" id="voucherId" name="id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="voucherCode">Mã Voucher</label>
                    <input type="text" id="voucherCode" name="code" required>
                </div>
                <div class="form-group">
                    <label for="voucherDescription">Mô tả</label>
                    <textarea id="voucherDescription" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="voucherType">Loại</label>
                    <select id="voucherType" name="voucher_type" required>
                        <option value="fixed_discount">Giảm cố định</option>
                        <option value="percentage_discount">Giảm phần trăm</option>
                        <option value="extend_duration">Tặng tháng</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="discountValue">Giá trị</label>
                    <input type="number" step="0.01" id="discountValue" name="discount_value" required>
                    <span id="discountUnit"></span>
                </div>
                <div class="form-group" id="maxDiscountGroup">
                    <label for="maxDiscount">Giới hạn giảm tối đa</label>
                    <input type="number" step="0.01" id="maxDiscount" name="max_discount">
                </div>
                <div class="form-group">
                    <label for="minOrderValue">Giá trị đơn hàng tối thiểu</label>
                    <input type="number" step="0.01" id="minOrderValue" name="min_order_value">
                </div>
                <div class="form-group">
                    <label for="quantity">Số lượng</label>
                    <input type="number" id="quantity" name="quantity">
                </div>
                <div class="form-group">
                    <label for="limitUsage">Giới hạn sử dụng mỗi người</label>
                    <input type="number" id="limitUsage" name="limit_usage">
                </div>
                <div class="form-group">
                    <label for="startDate">Ngày bắt đầu</label>
                    <input type="date" id="startDate" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="endDate">Ngày kết thúc</label>
                    <input type="date" id="endDate" name="end_date" required>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" id="isActive" name="is_active">
                    <label for="isActive">Kích hoạt</label>
                </div>
                <p id="voucherFormError" class="error-message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="helpers.closeModal('voucherFormModal')">Hủy</button>
                <button type="submit" class="btn btn-primary" id="voucherFormSubmit">Lưu</button>
            </div>
        </form>
    </div>
</div>
