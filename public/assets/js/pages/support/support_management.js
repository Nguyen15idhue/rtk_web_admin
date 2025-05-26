(function($) {
    const apiUrl = basePath + '/public/handlers/support/index.php';

    // Add centralized badge map and helper for support statuses
    const supportStatusBadgeMap = {
        pending:      { class: 'badge-yellow',    text: 'Chờ xử lý' },
        in_progress:  { class: 'badge-info',      text: 'Đang xử lý' },
        resolved:     { class: 'badge-green',     text: 'Đã giải quyết' },
        closed:       { class: 'badge-secondary', text: 'Đã đóng' }
    };
    function getStatusBadge(status) {
        const m = supportStatusBadgeMap[status] || { class: 'badge-secondary', text: status };
        return `<span class="status-badge ${m.class}">${m.text}</span>`;
    }

    function loadRequests() {
        const search = $('#searchInput').val();
        const category = $('#categoryFilter').val();
        const status = $('#statusFilter').val();
        api.getJson(`${apiUrl}?action=fetch_support_requests&search=${encodeURIComponent(search)}&category=${category}&status=${status}`)
            .then(env => {
                if (!env.success) throw new Error(env.message);
                const list = env.data;
                const rows = list.map(r => {
                    return `<tr data-id="${r.id}">` +
                        `<td><input type="checkbox" class="rowCheckbox" name="ids[]" value="${r.id}"></td>` +
                        `<td>${r.id}</td>` +
                        `<td>${r.user_email}</td>` +
                        `<td>${r.subject}</td>` +
                        `<td>${
                            r.category === 'technical' ? 'Kỹ thuật'
                            : r.category === 'billing' ? 'Thanh toán'
                            : r.category === 'account' ? 'Tài khoản'
                            : 'Khác'
                        }</td>` +
                        `<td>${getStatusBadge(r.status)}</td>` +
                        `<td>${helpers.formatDateTime(r.created_at)}</td>` +
                        `<td>${r.updated_at ? helpers.formatDateTime(r.updated_at) : ''}</td>` +
                        `<td class="actions text-center"><button type="button" class="btn-icon btn-edit" data-id="${r.id}" title="Sửa"><i class="fas fa-edit"></i></button></td>` +
                    `</tr>`;
                }).join('');
                $('#tbl-support tbody').html(rows);
            })
            .catch(err => window.showToast(err.message, 'error'));
    }

    function getSupportModalContentHTML(data) {
        return `
            <div class="support-modal-details">
                <div class="detail-row">
                    <div class="detail-label">ID</div>
                    <div class="detail-value" id="modalId">${data.id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">${data.user_email}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tiêu đề</div>
                    <div class="detail-value">${data.subject}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Nội dung</div>
                    <div class="detail-value modal-message">${data.message}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Thể loại</div>
                    <div class="detail-value">${
                        data.category === 'technical' ? 'Kỹ thuật'
                        : data.category === 'billing' ? 'Thanh toán'
                        : data.category === 'account' ? 'Tài khoản'
                        : 'Khác'
                    }</div>
                </div>
                <div class="section-divider"></div>
                <div class="detail-row">
                    <div class="detail-label">Trạng thái</div>
                    <div class="detail-value">
                        <select id="modalStatus" class="form-control">
                            <option value="pending" ${data.status === 'pending' ? 'selected' : ''}>Chờ xử lý</option>
                            <option value="in_progress" ${data.status === 'in_progress' ? 'selected' : ''}>Đang xử lý</option>
                            <option value="resolved" ${data.status === 'resolved' ? 'selected' : ''}>Đã giải quyết</option>
                            <option value="closed" ${data.status === 'closed' ? 'selected' : ''}>Đã đóng</option>
                        </select>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phản hồi Admin</div>
                    <div class="detail-value">
                        <textarea id="modalResponse" class="form-control" rows="4" placeholder="Nhập phản hồi của bạn...">${data.admin_response || ''}</textarea>
                    </div>
                </div>
                <div class="section-divider"></div>
                <div class="detail-row">
                    <div class="detail-label">Ngày tạo</div>
                    <div class="detail-value">${helpers.formatDateTime(data.created_at)}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Ngày cập nhật</div>
                    <div class="detail-value">${data.updated_at ? helpers.formatDateTime(data.updated_at) : '—'}</div>
                </div>
            </div>
        `;
    }

    function openSupportModal(id) {
        api.getJson(`${apiUrl}?action=get_support_request_details&id=${id}`)
            .then(env => {
                if (!env.success) throw new Error(env.message);
                const data = env.data;
                document.getElementById('genericModalTitle').textContent = 'Chi tiết yêu cầu hỗ trợ';
                document.getElementById('genericModalBody').innerHTML = getSupportModalContentHTML(data);
                
                const primaryButton = document.getElementById('genericModalPrimaryButton');
                primaryButton.textContent = 'Lưu thay đổi';
                primaryButton.onclick = saveSupportChanges;
                
                helpers.openModal('genericModal');
            })
            .catch(err => window.showToast(err.message, 'error'));
    }

    function saveSupportChanges() {
        const id = $('#modalId').text();
        const status = $('#modalStatus').val();
        const response = $('#modalResponse').val();
        api.postJson(apiUrl + '?action=update_support_request', { id, status, admin_response: response })
            .then(env => {
                if (!env.success) throw new Error(env.message);
                window.showToast('Cập nhật thành công!', 'success');
                helpers.closeModal('genericModal');
                loadRequests();
            })
            .catch(err => window.showToast(err.message, 'error'));
    }

    $(function() {
        loadRequests();
        $('#searchInput, #categoryFilter, #statusFilter').on('change input', loadRequests);
        $(document).on('click', '.btn-edit', function(event) {
            event.preventDefault();
            const id = $(this).data('id');
            openSupportModal(id);
        });

        $('#selectAll').on('change', function() {
            $('.rowCheckbox').prop('checked', this.checked);
        });
    });
})(jQuery);
