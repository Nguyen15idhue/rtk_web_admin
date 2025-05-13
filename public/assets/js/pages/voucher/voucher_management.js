// filepath: public/assets/js/pages/voucher/voucher_management.js
document.addEventListener('DOMContentLoaded', ()=> {
    const basePath = ('/' + (window.appConfig.basePath || ''))
                       .replace(/\/+/g,'/')
                       .replace(/\/?$/,'/');
    const apiBase = `${basePath}public/handlers/voucher/index.php`;
    const helpers = window.helpers;
    const { getJson, postJson, postForm } = window.api;

    // Elements for Generic Modal
    const genericModal = document.getElementById('genericModal');
    const genericModalTitle = document.getElementById('genericModalTitle');
    const genericModalBody = document.getElementById('genericModalBody');
    const genericModalFooter = document.getElementById('genericModalFooter');
    const genericModalPrimaryButton = document.getElementById('genericModalPrimaryButton');

    function getVoucherFormHTML() {
        return `
            <form id="voucherForm">
                <input type="hidden" id="voucherId" name="id">
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
                <div class="form-group" id="maxDiscountGroup" style="display: none;">
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
                    <input type="checkbox" id="isActive" name="is_active" value="1">
                    <label for="isActive">Kích hoạt</label>
                </div>
                <p id="voucherFormError" class="error-message"></p>
            </form>
        `;
    }

    function setupVoucherFormEventListeners() {
        const voucherForm = document.getElementById('voucherForm');
        if (!voucherForm) return;

        voucherForm.addEventListener('submit', handleVoucherFormSubmit);

        const voucherTypeSelect = document.getElementById('voucherType');
        if (voucherTypeSelect) {
            voucherTypeSelect.addEventListener('change', e => {
                const maxDiscountGroup = document.getElementById('maxDiscountGroup');
                if (maxDiscountGroup) {
                    maxDiscountGroup.style.display = e.target.value === 'percentage_discount' ? 'block' : 'none';
                }
            });
            // Trigger change for initial state
            voucherTypeSelect.dispatchEvent(new Event('change'));
        }
    }

    function loadVouchers() { /* Optionally refresh table via AJAX */ }

    // VIEW DETAILS
    window.VoucherPage = {
        viewDetails(id) {
            genericModalTitle.textContent = 'Chi tiết Voucher';
            genericModalBody.innerHTML = '<p>Đang tải...</p>';
            genericModalPrimaryButton.style.display = 'none'; // Hide save button for view
            helpers.openModal('genericModal');

            getJson(`${apiBase}?action=get_voucher_details&id=${encodeURIComponent(id)}`)
            .then(res=>{
                if(res.success) {
                    const v = res.data;
                    let html = `<div class="detail-row"><span class="detail-label">ID:</span> <span class="detail-value">${v.id}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Mã:</span> <span class="detail-value">${v.code}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Mô tả:</span> <span class="detail-value">${v.description || '-'}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Loại:</span> <span class="detail-value">${helpers.getVoucherTypeText(v.voucher_type)}</span></div>`;
                    let valueDisplay = '';
                    if (v.voucher_type === 'percentage_discount') {
                        valueDisplay = `${parseFloat(v.discount_value)}%`;
                    } else if (v.voucher_type === 'extend_duration') {
                        valueDisplay = `${parseInt(v.discount_value)} tháng`;
                    } else {
                        valueDisplay = helpers.formatCurrency(v.discount_value);
                    }
                    html += `<div class="detail-row"><span class="detail-label">Giá trị:</span> <span class="detail-value">${valueDisplay}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Giới hạn giảm:</span> <span class="detail-value">${v.max_discount ? helpers.formatCurrency(v.max_discount) : '-'}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Đơn hàng tối thiểu:</span> <span class="detail-value">${v.min_order_value ? helpers.formatCurrency(v.min_order_value) : '-'}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Số lượng:</span> <span class="detail-value">${v.quantity === null || v.quantity === undefined ? '-' : v.quantity}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Đã dùng:</span> <span class="detail-value">${v.used_quantity}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Giới hạn/người:</span> <span class="detail-value">${v.limit_usage === null || v.limit_usage === undefined ? '-' : v.limit_usage}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Thời gian:</span> <span class="detail-value">${helpers.formatDate(v.start_date)} - ${helpers.formatDate(v.end_date)}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${v.is_active == 1 ? 'Hoạt động':'Vô hiệu hóa'}</span></div>`;
                    genericModalBody.innerHTML = html;
                } else {
                    genericModalBody.innerHTML = `<p class="error-message">${res.message}</p>`;
                }
            });
        },
        openCreateModal() {
            genericModalTitle.textContent = 'Thêm Voucher';
            genericModalBody.innerHTML = getVoucherFormHTML();
            genericModalPrimaryButton.textContent = 'Tạo';
            genericModalPrimaryButton.style.display = 'block';
            genericModalPrimaryButton.onclick = () => document.getElementById('voucherForm').requestSubmit(); // Programmatically submit the form
            setupVoucherFormEventListeners();
            document.getElementById('voucherForm').reset();
            const errorEl = document.getElementById('voucherFormError');
            if(errorEl) errorEl.textContent = '';
            helpers.openModal('genericModal');
        },
        openEditModal(id) {
            genericModalTitle.textContent = 'Chỉnh sửa Voucher';
            genericModalBody.innerHTML = getVoucherFormHTML();
            genericModalPrimaryButton.textContent = 'Lưu';
            genericModalPrimaryButton.style.display = 'block';
            genericModalPrimaryButton.onclick = () => document.getElementById('voucherForm').requestSubmit(); // Programmatically submit the form
            setupVoucherFormEventListeners();
            const voucherForm = document.getElementById('voucherForm');
            const errorEl = document.getElementById('voucherFormError');
            if(voucherForm) voucherForm.reset();
            if(errorEl) errorEl.textContent = '';

            getJson(`${apiBase}?action=get_voucher_details&id=${encodeURIComponent(id)}`)
            .then(res=>{
                if(res.success) {
                    const v = res.data;
                    document.getElementById('voucherId').value = v.id;
                    document.getElementById('voucherCode').value = v.code;
                    document.getElementById('voucherDescription').value = v.description;
                    document.getElementById('voucherType').value = v.voucher_type;
                    document.getElementById('discountValue').value = v.discount_value; // No format for input
                    document.getElementById('maxDiscount').value = v.max_discount || '';
                    document.getElementById('minOrderValue').value = v.min_order_value || '';
                    document.getElementById('quantity').value = v.quantity || '';
                    document.getElementById('limitUsage').value = v.limit_usage || '';
                    document.getElementById('startDate').value = v.start_date ? v.start_date.split(' ')[0] : '';
                    document.getElementById('endDate').value = v.end_date ? v.end_date.split(' ')[0] : '';
                    document.getElementById('isActive').checked = (v.is_active == 1);
                    helpers.openModal('genericModal');
                    const voucherTypeSelect = document.getElementById('voucherType');
                    if (voucherTypeSelect) voucherTypeSelect.dispatchEvent(new Event('change'));
                } else {
                    window.showToast(res.message,'error');
                    helpers.closeModal('genericModal');
                }
            });
        },
        toggleStatus(id, action) {
            if(!confirm(`Bạn có chắc muốn ${action=='disable'?'vô hiệu hóa':'kích hoạt'} voucher ID ${id}?`)) return;
            postForm(`${apiBase}?action=toggle_voucher_status`, new URLSearchParams({id,action}))
            .then(res=>{
                if(res.success) window.showToast('Thành công','success');
                else window.showToast(res.message,'error');
                setTimeout(()=> location.reload(),500);
            });
        },
        deleteVoucher(id) {
            if (!confirm(`Bạn có chắc muốn xóa voucher ID ${id}?`)) return;
            postForm(`${apiBase}?action=delete_voucher`, new URLSearchParams({id}))
            .then(res => {
                if (res.success) {
                    window.showToast('Xóa thành công','success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    window.showToast(res.message, 'error');
                }
            }).catch(err => {
                window.showToast('Lỗi: ' + err.message, 'error');
            });
        }
    };

    // FORM SUBMIT LOGIC (MOVED TO A SEPARATE FUNCTION)
    function handleVoucherFormSubmit(e) {
        if (e) e.preventDefault(); // Prevent default if called by event listener
        const voucherForm = document.getElementById('voucherForm');
        const errorEl = document.getElementById('voucherFormError');
        if (!voucherForm || !errorEl) return;

        errorEl.textContent = '';
        const isEdit = !!document.getElementById('voucherId').value;
        genericModalPrimaryButton.disabled = true;
        genericModalPrimaryButton.textContent = isEdit?'Lưu...':'Tạo...';
        const fd = new FormData(voucherForm);

        // Ensure checkbox value is 0 or 1
        fd.set('is_active', document.getElementById('isActive').checked ? '1' : '0');

        const targetAction = isEdit?'update_voucher':'create_voucher';
        postForm(`${apiBase}?action=${targetAction}`, fd)
        .then(res=>{
            if(res.success) {
                helpers.closeModal('genericModal');
                window.showToast(res.message|| 'Lưu thành công','success');
                setTimeout(()=> location.reload(),500);
            } else {
                errorEl.textContent = res.message;
                window.showToast(res.message,'error');
            }
        })
        .catch(err=>{
            errorEl.textContent = 'Lỗi: '+err.message;
            window.showToast('Lỗi: '+err.message, 'error');
        })
        .finally(()=>{
            genericModalPrimaryButton.disabled=false;
            genericModalPrimaryButton.textContent=isEdit?'Lưu':'Tạo';
        });
    }

    // Initial setup for voucher type change (if form is somehow already in DOM, though unlikely with this new structure)
    const initialVoucherTypeSelect = document.getElementById('voucherType');
    if (initialVoucherTypeSelect) {
        initialVoucherTypeSelect.addEventListener('change',e=>{
            const maxDiscountGroup = document.getElementById('maxDiscountGroup');
            if (maxDiscountGroup) {
                 maxDiscountGroup.style.display = e.target.value=='percentage_discount'?'block':'none';
            }
        });
        initialVoucherTypeSelect.dispatchEvent(new Event('change'));
    }
});
