// filepath: public/assets/js/pages/voucher/voucher_management.js
document.addEventListener('DOMContentLoaded', ()=> {
    const basePath = ('/' + (window.appConfig.basePath || ''))
                       .replace(/\/+/g,'/')
                       .replace(/\/?$/,'/');
    const apiBase = `${basePath}public/handlers/voucher/index.php`;
    const helpers = window.helpers;
    const { getJson, postJson, postForm } = window.api;

    // Elements
    const viewBody = document.getElementById('viewVoucherDetailsBody');
    const formModal = document.getElementById('voucherFormModal');
    const formTitle = document.getElementById('voucherFormTitle');
    const voucherForm = document.getElementById('voucherForm');
    const errorEl = document.getElementById('voucherFormError');
    const submitBtn = document.getElementById('voucherFormSubmit');

    function loadVouchers() { /* Optionally refresh table via AJAX */ }

    // VIEW DETAILS
    window.VoucherPage = {
        viewDetails(id) {
            viewBody.innerHTML = '<p>Đang tải...</p>';
            helpers.openModal('viewVoucherModal');
            getJson(`${apiBase}?action=get_voucher_details&id=${encodeURIComponent(id)}`)
            .then(res=>{
                if(res.success) {
                    const v = res.data;
                    let html = `<div class="detail-row"><span class="detail-label">ID:</span> <span class="detail-value">${v.id}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Mã:</span> <span class="detail-value">${v.code}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Mô tả:</span> <span class="detail-value">${v.description}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Loại:</span> <span class="detail-value">${helpers.getVoucherTypeText(v.voucher_type)}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Giá trị:</span> <span class="detail-value">${
                        v.voucher_type === 'percentage_discount'
                            ? v.discount_value + '%'
                            : helpers.formatCurrency(v.discount_value)
                    }</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Giới hạn giảm:</span> <span class="detail-value">${v.max_discount ? helpers.formatCurrency(v.max_discount) : '-'}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Đơn hàng tối thiểu:</span> <span class="detail-value">${v.min_order_value ? helpers.formatCurrency(v.min_order_value) : '-'}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Số lượng:</span> <span class="detail-value">${v.quantity||'-'}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Đã dùng:</span> <span class="detail-value">${v.used_quantity}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Thời gian:</span> <span class="detail-value">${helpers.formatDate(v.start_date)} - ${helpers.formatDate(v.end_date)}</span></div>`;
                    html += `<div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${v.is_active? 'Hoạt động':'Vô hiệu hóa'}</span></div>`;
                    viewBody.innerHTML = html;
                } else {
                    viewBody.innerHTML = `<p class="error-message">${res.message}</p>`;
                }
            });
        },
        openCreateModal() {
            formTitle.textContent = 'Thêm Voucher';
            voucherForm.reset();
            errorEl.textContent = '';
            submitBtn.textContent = 'Tạo';
            helpers.openModal('voucherFormModal');
        },
        openEditModal(id) {
            formTitle.textContent = 'Chỉnh sửa Voucher';
            voucherForm.reset();
            errorEl.textContent = '';
            submitBtn.textContent = 'Lưu';
            getJson(`${apiBase}?action=get_voucher_details&id=${encodeURIComponent(id)}`)
            .then(res=>{
                if(res.success) {
                    const v = res.data;
                    document.getElementById('voucherId').value = v.id;
                    document.getElementById('voucherCode').value = v.code;
                    document.getElementById('voucherDescription').value = v.description;
                    document.getElementById('voucherType').value = v.voucher_type;
                    // Format currency fields
                    document.getElementById('discountValue').value = helpers.formatCurrency(v.discount_value, ''); // No currency symbol for input
                    document.getElementById('maxDiscount').value = helpers.formatCurrency(v.max_discount, '');
                    document.getElementById('minOrderValue').value = helpers.formatCurrency(v.min_order_value, '');

                    document.getElementById('quantity').value = v.quantity;
                    document.getElementById('limitUsage').value = v.limit_usage;
                    document.getElementById('startDate').value = v.start_date.split(' ')[0];
                    document.getElementById('endDate').value = v.end_date.split(' ')[0];
                    document.getElementById('isActive').checked = v.is_active==1;
                    helpers.openModal('voucherFormModal');
                    // Trigger change for voucherType to ensure maxDiscountGroup visibility is correct
                    document.getElementById('voucherType').dispatchEvent(new Event('change'));
                } else {
                    window.showToast(res.message,'error');
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

    // FORM SUBMIT
    voucherForm.addEventListener('submit',e=>{
        e.preventDefault();
        errorEl.textContent = '';
        const isEdit = !!document.getElementById('voucherId').value;
        submitBtn.disabled = true;
        submitBtn.textContent = isEdit?'Lưu...':'Tạo...';
        const fd = new FormData(voucherForm);

        // Parse currency fields before sending
        const discountValue = helpers.parseCurrency(fd.get('discount_value'));
        if (!isNaN(discountValue)) fd.set('discount_value', discountValue);
        else fd.set('discount_value', ''); // Or handle error

        const maxDiscount = helpers.parseCurrency(fd.get('max_discount'));
        if (!isNaN(maxDiscount)) fd.set('max_discount', maxDiscount);
        else fd.set('max_discount', '');

        const minOrderValue = helpers.parseCurrency(fd.get('min_order_value'));
        if (!isNaN(minOrderValue)) fd.set('min_order_value', minOrderValue);
        else fd.set('min_order_value', '');

        const targetAction = isEdit?'update_voucher':'create_voucher';
        postForm(`${apiBase}?action=${targetAction}`, fd)
        .then(res=>{
            if(res.success) {
                helpers.closeModal('voucherFormModal');
                window.showToast(res.message|| 'Lưu thành công','success');
                setTimeout(()=> location.reload(),500);
            } else {
                errorEl.textContent = res.message;
                window.showToast(res.message,'error');
            }
        })
        .catch(err=>{
            errorEl.textContent = 'Lỗi: '+err.message;
        })
        .finally(()=>{
            submitBtn.disabled=false;
            submitBtn.textContent=isEdit?'Lưu':'Tạo';
        });
    });

    // Hide or show maxDiscount based on type
    document.getElementById('voucherType').addEventListener('change',e=>{
        document.getElementById('maxDiscountGroup').style.display =
            e.target.value=='percentage_discount'?'block':'none';
    });
    document.getElementById('voucherType').dispatchEvent(new Event('change'));
});
