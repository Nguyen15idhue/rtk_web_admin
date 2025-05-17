// filepath: public/assets/js/pages/support/support_management.js
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
                        `<td class="actions text-center"><button type="button" class="btn-icon btn-view" data-id="${r.id}"><i class="fas fa-eye"></i></button></td>` +
                    `</tr>`;
                }).join('');
                $('#tbl-support tbody').html(rows);
            })
            .catch(err => window.showToast(err.message, 'error'));
    }

    function getSupportModalContentHTML(data) {
        return `
            <p><strong>ID:</strong> <span id="modalId">${data.id}</span></p>
            <p><strong>Email:</strong> <span>${data.user_email}</span></p>
            <p><strong>Tiêu đề:</strong> <span>${data.subject}</span></p>
            <p><strong>Nội dung:</strong></p>
            <p>${data.message}</p>
            <p><strong>Thể loại:</strong> <span>${
                data.category === 'technical' ? 'Kỹ thuật'
                : data.category === 'billing' ? 'Thanh toán'
                : data.category === 'account' ? 'Tài khoản'
                : 'Khác'
            }</span></p>
            <p><strong>Trạng thái:</strong>
                <select id="modalStatus" class="form-control">
                    <option value="pending" ${data.status === 'pending' ? 'selected' : ''}>Chờ xử lý</option>
                    <option value="in_progress" ${data.status === 'in_progress' ? 'selected' : ''}>Đang xử lý</option>
                    <option value="resolved" ${data.status === 'resolved' ? 'selected' : ''}>Đã giải quyết</option>
                    <option value="closed" ${data.status === 'closed' ? 'selected' : ''}>Đã đóng</option>
                </select>
            </p>
            <p><strong>Phản hồi của Admin:</strong></p>
            <textarea id="modalResponse" class="form-control" rows="4">${data.admin_response || ''}</textarea>
            <p><strong>Ngày tạo:</strong> <span>${helpers.formatDateTime(data.created_at)}</span></p>
            <p><strong>Ngày cập nhật:</strong> <span>${data.updated_at ? helpers.formatDateTime(data.updated_at) : ''}</span></p>
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
        $(document).on('click', '.btn-view', function(event) {
            event.preventDefault();
            const id = $(this).data('id');
            openSupportModal(id);
        });

        $('#selectAll').on('change', function() {
            $('.rowCheckbox').prop('checked', this.checked);
        });
    });
})(jQuery);
