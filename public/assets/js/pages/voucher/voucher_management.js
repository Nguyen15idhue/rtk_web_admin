// Thêm helper local để thay thế
function getVoucherTypeText(type) {
    switch (type) {
        case 'fixed_discount':        return 'Giảm cố định';
        case 'percentage_discount':   return 'Giảm phần trăm';
        case 'extend_duration':       return 'Tặng tháng';
        default:                      return 'Không xác định';
    }
}

// Helper để tạo status badge cho voucher
function getVoucherStatusBadge(status) {
    const voucherStatusMap = {
        'active':   { class: 'badge-green', text: 'Hoạt động' },
        'inactive': { class: 'badge-red',   text: 'Vô hiệu hóa' },
        'expired':  { class: 'badge-red',   text: 'Hết hạn' }
    };
    const config = voucherStatusMap[status] || { class: 'badge-gray', text: 'Không xác định' };
    return `<span class="status-badge ${config.class}">${config.text}</span>`;
}

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
                    <div class="input-group">
                        <input type="number" step="1" id="discountValue" name="discount_value" required>
                        <span class="input-unit" id="discountUnit">VNĐ</span>
                    </div>
                </div>
                <div class="form-group" id="maxDiscountGroup" style="display: none;">
                    <label for="maxDiscount">Giới hạn giảm tối đa</label>
                    <div class="input-group">
                        <input type="number" step="1" id="maxDiscount" name="max_discount">
                        <span class="input-unit" id="maxDiscountUnit">VNĐ</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="minOrderValue">Giá trị đơn hàng tối thiểu</label>
                    <div class="input-group">
                        <input type="number" step="1" id="minOrderValue" name="min_order_value">
                        <span class="input-unit" id="minOrderUnit">VNĐ</span>
                    </div>
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
                    <label for="maxSa">Số lượng tài khoản survey tối đa</label>
                    <input type="number" id="maxSa" name="max_sa">
                </div>
                <div class="form-group">
                    <label for="locationId">Tỉnh áp dụng</label>
                    <select id="locationId" name="location_id">
                        <option value="">Tất cả</option>
                        <!-- Options will be populated by JS -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="packageId">Gói áp dụng</label>
                    <select id="packageId" name="package_id">
                        <option value="">Tất cả</option>
                        <!-- Options will be populated by JS -->
                    </select>
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
                const discountUnit = document.getElementById('discountUnit');
                const maxDiscountUnit = document.getElementById('maxDiscountUnit'); // Added

                if (maxDiscountGroup) {
                    maxDiscountGroup.style.display = e.target.value === 'percentage_discount' ? 'block' : 'none';
                }
                if (discountUnit) {
                    if (e.target.value === 'fixed_discount') {
                        discountUnit.textContent = 'VNĐ';
                    } else if (e.target.value === 'percentage_discount') {
                        discountUnit.textContent = '%';
                    } else if (e.target.value === 'extend_duration') {
                        discountUnit.textContent = 'tháng';
                    }
                }
                if (maxDiscountUnit) { // Added
                    maxDiscountUnit.textContent = 'VNĐ';
                }
                // Ensure minOrderUnit is always VNĐ as it's not dependent on voucherType
                const minOrderUnit = document.getElementById('minOrderUnit');
                if (minOrderUnit) {
                    minOrderUnit.textContent = 'VNĐ';
                }
                validateDiscountValue(); // Revalidate when type changes
            });
            // Trigger change for initial state
            voucherTypeSelect.dispatchEvent(new Event('change'));
        }

        // Add real-time validation listeners
        const codeInput = document.getElementById('voucherCode');
        if (codeInput) {
            codeInput.addEventListener('blur', validateVoucherCode);
            codeInput.addEventListener('input', clearErrorMessage);
        }

        const discountValueInput = document.getElementById('discountValue');
        if (discountValueInput) {
            discountValueInput.addEventListener('blur', validateDiscountValue);
            discountValueInput.addEventListener('input', clearErrorMessage);
        }

        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', validateDateRange);
            endDateInput.addEventListener('change', validateDateRange);
        }

        const quantityInput = document.getElementById('quantity');
        if (quantityInput) {
            quantityInput.addEventListener('blur', validatePositiveNumber);
            quantityInput.addEventListener('input', clearErrorMessage);
        }

        const limitUsageInput = document.getElementById('limitUsage');
        if (limitUsageInput) {
            limitUsageInput.addEventListener('blur', validatePositiveNumber);
            limitUsageInput.addEventListener('input', clearErrorMessage);
        }
    }

    // Client-side validation functions
    function validateVoucherCode() {
        const codeInput = document.getElementById('voucherCode');
        const code = codeInput.value.trim();
        
        if (!code) {
            showFieldError(codeInput, 'Mã voucher không được để trống');
            return false;
        }
        
        if (code.length < 3) {
            showFieldError(codeInput, 'Mã voucher phải có ít nhất 3 ký tự');
            return false;
        }
        
        if (!/^[A-Za-z0-9_-]+$/.test(code)) {
            showFieldError(codeInput, 'Mã voucher chỉ được chứa chữ cái, số, dấu gạch dưới và dấu gạch ngang');
            return false;
        }
        
        clearFieldError(codeInput);
        return true;
    }

    function validateDiscountValue() {
        const discountValueInput = document.getElementById('discountValue');
        const voucherTypeSelect = document.getElementById('voucherType');
        const value = parseFloat(discountValueInput.value);
        const type = voucherTypeSelect.value;
        
        if (isNaN(value) || value <= 0) {
            showFieldError(discountValueInput, 'Giá trị giảm phải là số dương');
            return false;
        }
        
        if (type === 'percentage_discount' && value > 100) {
            showFieldError(discountValueInput, 'Giá trị giảm phần trăm không được vượt quá 100%');
            return false;
        }
        
        if (type === 'extend_duration' && !Number.isInteger(value)) {
            showFieldError(discountValueInput, 'Số tháng gia hạn phải là số nguyên');
            return false;
        }
        
        clearFieldError(discountValueInput);
        return true;
    }

    function validateDateRange() {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (!startDateInput.value) {
            showFieldError(startDateInput, 'Ngày bắt đầu không được để trống');
            return false;
        }
        
        if (!endDateInput.value) {
            showFieldError(endDateInput, 'Ngày kết thúc không được để trống');
            return false;
        }
        
        if (startDate < today) {
            showFieldError(startDateInput, 'Ngày bắt đầu không được nhỏ hơn ngày hiện tại');
            return false;
        }
        
        if (endDate <= startDate) {
            showFieldError(endDateInput, 'Ngày kết thúc phải sau ngày bắt đầu');
            return false;
        }
        
        clearFieldError(startDateInput);
        clearFieldError(endDateInput);
        return true;
    }

    function validatePositiveNumber() {
        const input = this;
        const value = parseInt(input.value);
        
        if (input.value && (isNaN(value) || value <= 0)) {
            showFieldError(input, 'Giá trị phải là số nguyên dương');
            return false;
        }
        
        clearFieldError(input);
        return true;
    }

    function showFieldError(input, message) {
        clearFieldError(input);
        input.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error-message';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }

    function clearFieldError(input) {
        input.classList.remove('error');
        const existingError = input.parentNode.querySelector('.field-error-message');
        if (existingError) {
            existingError.remove();
        }
    }

    function clearErrorMessage() {
        clearFieldError(this);
        const generalError = document.getElementById('voucherFormError');
        if (generalError) {
            generalError.textContent = '';
        }
    }

    function validateForm() {
        const isValidCode = validateVoucherCode();
        const isValidDiscount = validateDiscountValue();
        const isValidDate = validateDateRange();
        
        // Validate other numeric fields if they have values
        const quantityInput = document.getElementById('quantity');
        const limitUsageInput = document.getElementById('limitUsage');
        let isValidNumbers = true;
        
        if (quantityInput.value) {
            isValidNumbers = validatePositiveNumber.call(quantityInput) && isValidNumbers;
        }
        
        if (limitUsageInput.value) {
            isValidNumbers = validatePositiveNumber.call(limitUsageInput) && isValidNumbers;
        }
        
        return isValidCode && isValidDiscount && isValidDate && isValidNumbers;
    }

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
                    html += `<div class="detail-row"><span class="detail-label">Loại:</span> <span class="detail-value">${getVoucherTypeText(v.voucher_type)}</span></div>`;
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
                    html += `<div class="detail-row"><span class="detail-label">SL TK Tối đa:</span> <span class="detail-value">${v.max_sa === null || v.max_sa === undefined ? 'Không giới hạn' : v.max_sa}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Tỉnh áp dụng:</span> <span class="detail-value">${v.location_name || 'Tất cả'}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Gói áp dụng:</span> <span class="detail-value">${v.package_name || 'Tất cả'}</span></div>`;
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
            populateSelectOptions(); // Populate new select dropdowns
            document.getElementById('voucherForm').reset();
            // Trigger change event to set initial unit
            const voucherTypeSelect = document.getElementById('voucherType');
            if (voucherTypeSelect) {
                voucherTypeSelect.dispatchEvent(new Event('change'));
            }
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
            populateSelectOptions(); // Populate new select dropdowns
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
                    document.getElementById('discountValue').value = parseFloat(v.discount_value) || '';
                    document.getElementById('maxDiscount').value = parseFloat(v.max_discount) || '';
                    document.getElementById('minOrderValue').value = parseFloat(v.min_order_value) || '';
                    document.getElementById('quantity').value = v.quantity || '';
                    document.getElementById('limitUsage').value = v.limit_usage || '';
                    document.getElementById('maxSa').value = v.max_sa || '';
                    document.getElementById('locationId').value = v.location_id || '';
                    document.getElementById('packageId').value = v.package_id || '';
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
            
            // Find the button and row elements
            const toggleButton = document.querySelector(`button[onclick*="toggleStatus(${id}"]`);
            const statusCell = toggleButton ? toggleButton.closest('tr').querySelector('td:nth-child(15)') : null;
            
            // Disable button during request
            if (toggleButton) {
                toggleButton.disabled = true;
                toggleButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            
            postForm(`${apiBase}?action=toggle_voucher_status`, new URLSearchParams({id,action}))
            .then(res=>{
                if(res.success) {
                    window.showToast(res.message,'success');
                    
                    // Update UI elements instead of reloading
                    if (toggleButton && statusCell) {
                        const newIsActive = action === 'enable';
                        const newAction = newIsActive ? 'disable' : 'enable';
                        const newIcon = newIsActive ? 'fa-toggle-off' : 'fa-toggle-on';
                        const newTitle = newIsActive ? 'Vô hiệu hóa' : 'Kích hoạt';
                        
                        // Update toggle button
                        toggleButton.onclick = () => VoucherPage.toggleStatus(id, newAction);
                        toggleButton.innerHTML = `<i class="fas ${newIcon}"></i>`;
                        toggleButton.title = newTitle;
                        
                        // Update status badge - we need to check if voucher is expired first
                        const timeCell = toggleButton.closest('tr').querySelector('td:nth-child(14)');
                        const endDateText = timeCell ? timeCell.textContent.split(' - ')[1] : '';
                        const isExpired = endDateText && new Date(endDateText.split('/').reverse().join('-')) < new Date();
                        
                        if (isExpired) {
                            statusCell.innerHTML = getVoucherStatusBadge('expired');
                        } else {
                            statusCell.innerHTML = getVoucherStatusBadge(newIsActive ? 'active' : 'inactive');
                        }
                    }
                } else {
                    window.showToast(res.message,'error');
                }
            })
            .catch(err => {
                window.showToast('Lỗi: ' + err.message, 'error');
            })
            .finally(() => {
                // Re-enable button
                if (toggleButton) {
                    toggleButton.disabled = false;
                    if (!toggleButton.innerHTML.includes('fa-toggle-')) {
                        // If button content wasn't updated in success block, restore original icon
                        const currentAction = toggleButton.onclick.toString().includes('disable') ? 'disable' : 'enable';
                        const icon = currentAction === 'disable' ? 'fa-toggle-off' : 'fa-toggle-on';
                        toggleButton.innerHTML = `<i class="fas ${icon}"></i>`;
                    }
                }
            });
        },
        deleteVoucher(id) {
            if (!confirm(`Bạn có chắc muốn xóa voucher ID ${id}?`)) return;
            postForm(`${apiBase}?action=delete_voucher`, new URLSearchParams({id}))
            .then(res => {
                if (res.success) {
                    window.showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    window.showToast(res.message, 'error');
                }
            }).catch(err => {
                window.showToast('Lỗi: ' + err.message, 'error');
            });
        },
        copyVoucherLinkAndShowQR(link, voucherCode) {
            navigator.clipboard.writeText(link).then(function() {
                window.showToast('Đã sao chép link voucher!', 'success');

                // Generate QR code
                const typeNumber = 4;
                const errorCorrectionLevel = 'L';
                const qr = qrcode(typeNumber, errorCorrectionLevel);
                qr.addData(link);
                qr.make();

                // Prepare QR Code HTML for generic modal BODY
                const qrCodeImageHTML = `
                    <div style="text-align: center; margin-top: 20px;">
                        ${qr.createImgTag(6, 12)} 
                    </div>
                `;
                genericModalBody.innerHTML = qrCodeImageHTML;
                genericModalTitle.textContent = `Mã QR cho Voucher: ${voucherCode}`;

                // Ensure the primary button is a button, then reconfigure or replace
                let primaryButton = document.getElementById('genericModalPrimaryButton');
                // If the primary button was replaced by an anchor, we need to recreate it or ensure it's a button.
                // For simplicity, we'll ensure it's a button and then change its behavior if needed,
                // or create a new one if it's drastically different (e.g. an anchor).

                // Check if it's an anchor, if so, replace it with a button before proceeding
                if (primaryButton && primaryButton.tagName === 'A') {
                    const newButton = document.createElement('button');
                    newButton.id = 'genericModalPrimaryButton';
                    newButton.className = primaryButton.className; // Assuming it has btn btn-primary
                    primaryButton.parentNode.replaceChild(newButton, primaryButton);
                    primaryButton = newButton;
                }

                if (primaryButton) {
                    primaryButton.textContent = 'Tải xuống QR';
                    primaryButton.style.display = 'block'; 
                    primaryButton.onclick = function() { // Set onclick for download behavior
                        const qrImgElement = genericModalBody.querySelector('img');
                        if (qrImgElement) {
                            const downloadLink = document.createElement('a');
                            downloadLink.href = qrImgElement.src;
                            downloadLink.download = `voucher_qr_${voucherCode}.png`;
                            document.body.appendChild(downloadLink); // Required for Firefox
                            downloadLink.click();
                            document.body.removeChild(downloadLink);
                        }
                    };
                }
                
                helpers.openModal('genericModal');

            }, function(err) {
                window.showToast('Không thể sao chép link. Lỗi: ' + err, 'error');
            });
        },

        // Bulk actions
        bulkToggleStatus() {
            const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
            const voucherIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (voucherIds.length === 0) {
                window.showToast('Vui lòng chọn ít nhất một voucher.', 'warning');
                return;
            }

            if (!confirm(`Bạn có chắc muốn đảo trạng thái của ${voucherIds.length} voucher đã chọn?`)) {
                return;
            }

            let successCount = 0;
            let errorCount = 0;
            const errors = [];

            // Process each voucher
            Promise.all(voucherIds.map(async id => {
                try {
                    const row = document.querySelector(`tr input[value="${id}"]`).closest('tr');
                    const toggleButton = row ? row.querySelector('button[onclick*="toggleStatus"]') : null;
                    
                    if (!toggleButton) {
                        throw new Error(`Toggle button not found for voucher ${id}`);
                    }

                    // Multiple methods to determine current action
                    let currentAction = null;
                    
                    // Method 1: Check status badge text
                    const statusCell = row.querySelector('td:nth-child(15)'); // Status column
                    if (statusCell) {
                        const statusText = statusCell.textContent.trim();
                        if (statusText.includes('Hoạt động')) {
                            currentAction = 'disable'; // Currently active, so we'll disable
                        } else if (statusText.includes('Vô hiệu hóa')) {
                            currentAction = 'enable'; // Currently inactive, so we'll enable
                        }
                    }
                    
                    // Method 2: Check button icon class as fallback
                    if (!currentAction) {
                        const icon = toggleButton.querySelector('i');
                        if (icon && icon.classList.contains('fa-toggle-off')) {
                            currentAction = 'enable'; // Currently disabled (off), so enable
                        } else if (icon && icon.classList.contains('fa-toggle-on')) {
                            currentAction = 'disable'; // Currently enabled (on), so disable
                        }
                    }
                    
                    // Method 3: Parse onclick attribute as final fallback
                    if (!currentAction) {
                        const onclickStr = toggleButton.getAttribute('onclick') || '';
                        if (onclickStr.includes("'disable'")) {
                            currentAction = 'disable';
                        } else if (onclickStr.includes("'enable'")) {
                            currentAction = 'enable';
                        }
                    }
                    
                    if (!currentAction) {
                        throw new Error(`Cannot determine current status for voucher ${id}`);
                    }

                    console.log(`Toggling voucher ${id} with action: ${currentAction}`);
                    
                    const res = await postForm(`${apiBase}?action=toggle_voucher_status`, new URLSearchParams({
                        id: id,
                        action: currentAction
                    }));
                    
                    if (res.success) {
                        successCount++;
                        return { success: true, id, action: currentAction };
                    } else {
                        throw new Error(res.message || 'Unknown error');
                    }
                } catch (error) {
                    errorCount++;
                    errors.push(`Voucher ${id}: ${error.message}`);
                    console.error(`Failed to toggle voucher ${id}:`, error);
                    return { success: false, id, error: error.message };
                }
            }))
            .then((results) => {
                console.log('Bulk toggle results:', results);
                
                if (successCount > 0 && errorCount === 0) {
                    window.showToast(`Đã đảo trạng thái thành công ${successCount} voucher.`, 'success');
                } else if (successCount > 0 && errorCount > 0) {
                    window.showToast(`Đã đảo trạng thái ${successCount} voucher. ${errorCount} voucher lỗi: ${errors.join(', ')}`, 'warning');
                } else {
                    window.showToast(`Không thể đảo trạng thái voucher nào. Lỗi: ${errors.join(', ')}`, 'error');
                }
                
                // Reload page to refresh the table
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                console.error('Bulk toggle error:', error);
                window.showToast('Lỗi hệ thống khi đảo trạng thái: ' + error.message, 'error');
            });
        },

        bulkDeleteVouchers() {
            const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
            const voucherIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (voucherIds.length === 0) {
                window.showToast('Vui lòng chọn ít nhất một voucher.', 'warning');
                return;
            }

            if (!confirm(`Bạn có chắc chắn muốn xóa ${voucherIds.length} voucher? Hành động này không thể hoàn tác.`)) {
                return;
            }

            // Process each voucher deletion
            Promise.all(voucherIds.map(async id => {
                try {
                    const res = await postForm(`${apiBase}?action=delete_voucher`, new URLSearchParams({id}));
                    return res;
                } catch (error) {
                    console.error(`Failed to delete voucher ${id}:`, error);
                    throw error;
                }
            }))
            .then(() => {
                window.showToast('Đã xóa các voucher được chọn.', 'success');
                setTimeout(() => location.reload(), 500);
            })
            .catch(error => {
                window.showToast('Lỗi khi xóa: ' + error.message, 'error');
            });
        },

        cloneVoucher(id) {
            if (!confirm(`Bạn có chắc muốn nhân bản voucher ID ${id}?`)) return;
            
            // Show loading state
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            postForm(`${apiBase}?action=clone_voucher`, new URLSearchParams({id}))
            .then(res => {
                if (res.success) {
                    window.showToast(`${res.message}`, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    window.showToast(res.message, 'error');
                }
            }).catch(err => {
                window.showToast('Lỗi: ' + err.message, 'error');
            }).finally(() => {
                // Restore button state
                button.disabled = false;
                button.innerHTML = originalContent;
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
        
        // Client-side validation
        if (!validateForm()) {
            window.showToast('Vui lòng kiểm tra lại thông tin nhập vào', 'warning');
            return;
        }

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

    // Function to populate location and package select options
    async function populateSelectOptions() {
        const locationSelect = document.getElementById('locationId');
        const packageSelect = document.getElementById('packageId');

        if (locationSelect) {
            try {
                const res = await getJson(`${basePath}public/handlers/voucher/index.php?action=get_locations`);
                if (res.success && Array.isArray(res.data)) {
                    res.data.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location.id;
                        option.textContent = location.name;
                        locationSelect.appendChild(option);
                    });
                } else {
                    console.error("Failed to load locations:", res.message);
                }
            } catch (error) {
                console.error("Error loading locations:", error);
            }
        }

        if (packageSelect) {
            try {
                const res = await getJson(`${basePath}public/handlers/voucher/index.php?action=get_packages`);
                if (res.success && Array.isArray(res.data)) {
                    res.data.forEach(pkg => {
                        const option = document.createElement('option');
                        option.value = pkg.id;
                        option.textContent = pkg.name;
                        packageSelect.appendChild(option);
                    });
                } else {
                    console.error("Failed to load packages:", res.message);
                }
            } catch (error) {
                console.error("Error loading packages:", error);
            }
        }
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

    // Add expired status handling in client-side status filter if any
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', e => {
            const selectedStatus = e.target.value;
            const rows = document.querySelectorAll('.voucher-row');
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                row.style.display = (selectedStatus === 'all' || selectedStatus === status) ? '' : 'none';
            });
        });
    }
});
