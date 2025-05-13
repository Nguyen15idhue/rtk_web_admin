// filepath: public/assets/js/pages/support/support_management.js
(function($) {
    const apiUrl = basePath + '/public/handlers/support/index.php';

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
                        `<td>${
                            r.status === 'pending'
                                ? '<span class="status-badge badge-yellow">Chờ xử lý</span>'
                                : r.status === 'in_progress'
                                    ? '<span class="status-badge badge-info">Đang xử lý</span>'
                                    : r.status === 'resolved'
                                        ? '<span class="status-badge badge-green">Đã giải quyết</span>'
                                        : '<span class="status-badge badge-secondary">Đã đóng</span>'
                        }</td>` +
                        `<td>${new Date(r.created_at).toLocaleString()}</td>` +
                        `<td>${r.updated_at ? new Date(r.updated_at).toLocaleString() : ''}</td>` +
                        `<td class="actions text-center"><button class="btn-icon btn-view" data-id="${r.id}"><i class="fas fa-eye"></i></button></td>` +
                    `</tr>`;
                }).join('');
                $('#tbl-support tbody').html(rows);
            })
            .catch(err => alert(err.message));
    }

    function openModal(id) {
        api.getJson(`${apiUrl}?action=get_support_request_details&id=${id}`)
            .then(env => {
                if (!env.success) throw new Error(env.message);
                const data = env.data;
                $('#modalId').text(data.id);
                $('#modalEmail').text(data.user_email);
                $('#modalSubject').text(data.subject);
                $('#modalMessage').text(data.message);
                $('#modalCategory').text(
                    data.category === 'technical' ? 'Kỹ thuật'
                    : data.category === 'billing' ? 'Thanh toán'
                    : data.category === 'account' ? 'Tài khoản'
                    : 'Khác'
                );
                $('#modalStatus').val(data.status);
                $('#modalResponse').val(data.admin_response || '');
                $('#modalCreated').text(new Date(data.created_at).toLocaleString());
                $('#modalUpdated').text(data.updated_at ? new Date(data.updated_at).toLocaleString() : '');
                $('#supportModal').show();
            })
            .catch(err => alert(err.message));
    }

    function closeModal() {
        $('#supportModal').hide();
    }

    function saveChanges() {
        const id = $('#modalId').text();
        const status = $('#modalStatus').val();
        const response = $('#modalResponse').val();
        api.postJson(apiUrl + '?action=update_support_request', { id, status, admin_response: response })
            .then(env => {
                if (!env.success) throw new Error(env.message);
                alert('Cập nhật thành công');
                closeModal();
                loadRequests();
            })
            .catch(err => alert(err.message));
    }

    $(function() {
        loadRequests();
        $('#searchInput, #categoryFilter, #statusFilter').on('change input', loadRequests);
        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            openModal(id);
        });
        $('#closeModal').on('click', closeModal);
        $('#saveBtn').on('click', saveChanges);

        // add select-all checkbox functionality like revenue page
        $('#selectAll').on('change', function() {
            $('.rowCheckbox').prop('checked', this.checked);
        });
    });
})(jQuery);
