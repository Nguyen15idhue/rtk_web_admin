document.addEventListener('DOMContentLoaded', () => {
    const { getJson, postJson, postForm } = window.api;
    const { closeModal: helperCloseModal } = window.helpers;

    const accountsTableBody = document.getElementById('accountsTable')?.querySelector('tbody');
    const noResultsRow = document.getElementById('no-results-row');

    // Modal elements
    const viewModal = document.getElementById('viewAccountModal');
    const viewDetailsContent = document.getElementById('viewAccountDetailsContent');
    const createModal = document.getElementById('createAccountModal');
    const editModal = document.getElementById('editAccountModal');
    const createAccountForm = document.getElementById('createAccountForm');
    const editAccountForm = document.getElementById('editAccountForm');
    const createAccountError = document.getElementById('createAccountError');
    const editAccountError = document.getElementById('editAccountError');

    function openCreateMeasurementAccountModal() {
        if (createAccountForm) createAccountForm.reset();
        if (createAccountError) createAccountError.textContent = '';
        const createUserInfo = document.getElementById('create-user-info');
        if (createUserInfo) createUserInfo.innerHTML = '';
        if (createModal) createModal.style.display = 'block';
    }

    async function openEditAccountModal(accountId) {
        if (!editModal || !editAccountForm) return;

        editAccountForm.reset();
        if (editAccountError) editAccountError.textContent = '';
        const editUserInfo = document.getElementById('edit-user-info');
        if (editUserInfo) editUserInfo.innerHTML = '';

        try {
            const env = await getJson(`${apiBasePath}?action=get_account_details&id=${accountId}`);
            if (!env.success) {
                throw new Error(env.message || `Không thể lấy chi tiết tài khoản.`);
            }
            const account = env.data || env.account;
            if (!account) {
                throw new Error('No account payload');
            }
            editAccountForm.querySelector('#edit-account-id').value = account.id;
            editAccountForm.querySelector('#edit-username').value = account.username_acc || '';
            editAccountForm.querySelector('#edit-user-email').value = account.user_email || '';
            editAccountForm.querySelector('#edit-location').value = account.location_id || '';
            editAccountForm.querySelector('#edit-package').value = account.package_id || '';
            editAccountForm.querySelector('#edit-activation-date').value = account.activation_date ? account.activation_date.split(' ')[0] : '';
            editAccountForm.querySelector('#edit-expiry-date').value = account.expiry_date ? account.expiry_date.split(' ')[0] : '';
            editAccountForm.querySelector('#edit-status').value = account.derived_status || 'unknown';

            if (account.user_email) {
                fetchAndDisplayUserInfo(account.user_email, 'edit-user-info');
            }

            editModal.style.display = 'block';
        } catch (error) {
            console.error('Error fetching account details:', error);
            window.showToast(error.message || 'Không thể tải chi tiết tài khoản.', 'error');
        }
    }

    function viewAccountDetails(accountId) {
        if (!viewModal || !viewDetailsContent) {
            console.error('View modal elements not found');
            alert('Lỗi giao diện: Không tìm thấy cửa sổ chi tiết.');
            return;
        }
        viewDetailsContent.innerHTML = '<p>Đang tải...</p>';
        viewModal.style.display = 'block';

        getJson(`${apiBasePath}?action=get_account_details&id=${accountId}`)
            .then(env => {
                if (!env.success) {
                    throw new Error(env.message || 'Lỗi server');
                }
                const account = env.data || env.account;
                if (!account) {
                    throw new Error('No account payload');
                }
                let detailsHtml = `
                    <div class="detail-row"><span class="detail-label">ID TK:</span> <span class="detail-value">${account.id || 'N/A'}</span></div>
                    <div class="detail-row"><span class="detail-label">Username TK:</span> <span class="detail-value">${account.username_acc || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Email User:</span> <span class="detail-value">${account.user_email || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Tên User:</span> <span class="detail-value">${account.user_username || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">SĐT User:</span> <span class="detail-value">${account.user_phone || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Tỉnh/Thành:</span> <span class="detail-value">${account.location_name || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Gói:</span> <span class="detail-value">${account.package_name || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Ngày KH:</span> <span class="detail-value">${account.activation_date_formatted || account.activation_date || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Ngày HH:</span> <span class="detail-value">${account.expiry_date_formatted || account.expiry_date || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${get_account_status_badge_js(account.derived_status || account.status)}</span></div>
                    <div class="detail-row"><span class="detail-label">Ngày tạo:</span> <span class="detail-value">${account.created_at_formatted || account.created_at || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Cập nhật:</span> <span class="detail-value">${account.updated_at_formatted || account.updated_at || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Ghi chú:</span> <span class="detail-value">${account.notes || '-'}</span></div>
                    `;
                viewDetailsContent.innerHTML = detailsHtml;
            })
            .catch(error => {
                console.error('Detailed error fetching account details for view:', error);
                viewDetailsContent.innerHTML =
                    `<p style="color: red;">Đã xảy ra lỗi: ${error.message}</p>`;
            });
    }

    function get_account_status_badge_js(status) {
        status = status ? status.toLowerCase() : 'unknown';
        let badgeClass = 'badge-gray';
        let statusText = 'Không xác định';

        switch (status) {
            case 'active': badgeClass = 'badge-green'; statusText = 'Hoạt động'; break;
            case 'pending': badgeClass = 'badge-yellow'; statusText = 'Chờ KH'; break;
            case 'expired': badgeClass = 'badge-red'; statusText = 'Hết hạn'; break;
            case 'suspended': badgeClass = 'badge-gray'; statusText = 'Đình chỉ'; break;
            case 'rejected': badgeClass = 'badge-red'; statusText = 'Bị từ chối'; break;
        }
        return `<span class="status-badge ${badgeClass}">${statusText}</span>`;
    }

    async function handleCreateAccountSubmit(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang tạo...';
        if (createAccountError) createAccountError.textContent = '';

        try {
            const result = await postForm(`${apiBasePath}?action=create_account`, formData);

            if (result.success) {
                window.showToast(result.message || 'Tạo tài khoản thành công!', 'success');
                helperCloseModal('createAccountModal');
                window.location.reload();
            } else {
                if(createAccountError) createAccountError.textContent = result.message || 'Tạo tài khoản thất bại.';
                window.showToast(result.message || 'Tạo tài khoản thất bại.', 'error');
            }
        } catch (error) {
            console.error('Error creating account:', error);
            if(createAccountError) createAccountError.textContent = 'Lỗi khi gửi yêu cầu tạo tài khoản.';
            window.showToast('Lỗi khi gửi yêu cầu tạo tài khoản.', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Tạo tài khoản';
        }
    }

    async function handleEditAccountSubmit(form) {
        const formData = new FormData(form);
        const accountId = formData.get('id');
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang lưu...';
         if (editAccountError) editAccountError.textContent = '';

        try {
            const result = await postForm(`${apiBasePath}?action=update_account`, formData);

            if (result.success) {
                window.showToast(result.message || 'Cập nhật tài khoản thành công!', 'success');
                helperCloseModal('editAccountModal');
                if (result.account) {
                    updateTableRow(accountId, result.account);
                } else {
                    window.location.reload();
                }
            } else {
                if(editAccountError) editAccountError.textContent = result.message || 'Cập nhật tài khoản thất bại.';
                window.showToast(result.message || 'Cập nhật tài khoản thất bại.', 'error');
            }
        } catch (error) {
            console.error('Error updating account:', error);
            if(editAccountError) editAccountError.textContent = 'Lỗi khi gửi yêu cầu cập nhật.';
            window.showToast('Lỗi khi gửi yêu cầu cập nhật.', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Lưu thay đổi';
        }
    }

    function updateTableRow(accountId, updatedData) {
        const row = accountsTableBody?.querySelector(`tr[data-account-id="${accountId}"]`);
        if (!row) return;

        const formatDateCell = (dateString) => {
            if (!dateString) return '-';
            try {
                const datePart = dateString.split(' ')[0];
                const [year, month, day] = datePart.split('-');
                return `${day}/${month}/${year}`;
            } catch (e) {
                console.error("Error formatting date:", dateString, e);
                return dateString;
            }
        };

        row.cells[1].textContent = updatedData.username_acc || '';
        row.cells[2].textContent = updatedData.user_email || '';
        row.cells[3].textContent = updatedData.package_name || '';
        row.cells[4].textContent = formatDateCell(updatedData.activation_date);
        row.cells[5].textContent = formatDateCell(updatedData.expiry_date);
        row.cells[6].innerHTML = get_account_status_badge_js(updatedData.derived_status || 'unknown');
        row.dataset.status = updatedData.derived_status || 'unknown';

        const actionCell = row.cells[7];
        const toggleButton = actionCell.querySelector('button[onclick*="toggleAccountStatus"]');
        if (toggleButton) {
             const newStatus = updatedData.derived_status;
             const isSuspended = newStatus === 'suspended';
             const isPending = newStatus === 'pending';
             let newAction = '';
             let newIcon = '';
             let newTitle = '';
             let newClass = 'btn-icon ';

             if (isPending) {
                 newAction = 'approve';
                 newIcon = 'fa-check-circle';
                 newTitle = 'Phê duyệt';
                 newClass += 'btn-approve';
             } else if (isSuspended) {
                 newAction = 'approve';
                 newIcon = 'fa-play-circle';
                 newTitle = 'Bỏ đình chỉ';
                 newClass += 'btn-approve';
             } else {
                 newAction = 'suspend';
                 newIcon = 'fa-ban';
                 newTitle = 'Đình chỉ';
                 newClass += 'btn-reject';
             }

             toggleButton.title = newTitle;
             toggleButton.className = newClass;
             toggleButton.setAttribute('onclick', `toggleAccountStatus('${accountId}', '${newAction}', event)`);
             const iconElement = toggleButton.querySelector('i');
             if (iconElement) {
                 iconElement.className = `fas ${newIcon}`;
             }
        }
    }

    async function deleteAccount(accountId, event) {
        event.stopPropagation();
        if (!confirm(`Bạn có chắc chắn muốn xóa tài khoản ID ${accountId}? Hành động này không thể hoàn tác.`)) {
            return;
        }

        try {
            const result = await postJson(`${apiBasePath}?action=delete_account`, { id: accountId });

            if (result.success) {
                window.showToast(result.message || 'Xóa tài khoản thành công!', 'success');
                const row = accountsTableBody?.querySelector(`tr[data-account-id="${accountId}"]`);
                if (row) {
                    row.remove();
                    if (accountsTableBody && accountsTableBody.rows.length === 0 && noResultsRow) {
                        noResultsRow.style.display = 'table-row';
                    }
                }
            } else {
                window.showToast(result.message || 'Xóa tài khoản thất bại.', 'error');
            }
        } catch (error) {
            console.error('Error deleting account:', error);
            window.showToast('Lỗi khi gửi yêu cầu xóa.', 'error');
        }
    }

    async function toggleAccountStatus(accountId, action, event) {
        event.stopPropagation();

        let confirmMessage = `Bạn có chắc muốn ${action === 'suspend' ? 'đình chỉ' : action === 'unsuspend' ? 'bỏ đình chỉ' : action === 'approve' ? 'phê duyệt' : 'thực hiện hành động này với'} tài khoản ID ${accountId}?`;
        if (!confirm(confirmMessage)) {
            return;
        }

        try {
            const result = await postJson(`${apiBasePath}?action=toggle_account_status`, { id: accountId, action });

            if (result.success) {
                window.showToast(result.message || 'Cập nhật trạng thái thành công!', 'success');
                if (result.account) {
                    updateTableRow(accountId, result.account);
                } else {
                    window.location.reload();
                }
            } else {
                window.showToast(result.message || 'Cập nhật trạng thái thất bại.', 'error');
            }
        } catch (error) {
            console.error('Error toggling account status:', error);
            window.showToast('Lỗi khi gửi yêu cầu cập nhật trạng thái.', 'error');
        }
    }

    if (createAccountForm) {
        createAccountForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleCreateAccountSubmit(this);
        });
    } else {
        console.error("Element with ID 'createAccountForm' not found.");
    }

    if (editAccountForm) {
        editAccountForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleEditAccountSubmit(this);
        });
    } else {
        console.error("Element with ID 'editAccountForm' not found.");
    }

    window.addEventListener('click', function(event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target === modal) {
                helperCloseModal(modal.id);
            }
        });
    });

    function calculateExpiryDate(activation, pkgId) {
        if (!activation || !packageDurations || !packageDurations[pkgId]) return '';
        try {
            const date = new Date(activation);
            if (isNaN(date.getTime())) {
                console.error("Invalid activation date provided:", activation);
                return '';
            }
            const dur = packageDurations[pkgId];
            if (dur.days)   date.setDate(date.getDate() + dur.days);
            if (dur.months) date.setMonth(date.getMonth() + dur.months);
            if (dur.years)  date.setFullYear(date.getFullYear() + dur.years);

            if (isNaN(date.getTime())) {
                console.error("Date calculation resulted in an invalid date. Input:", activation, "PkgID:", pkgId);
                return '';
            }

            const yyyy = date.getFullYear();
            const mm   = String(date.getMonth() + 1).padStart(2, '0');
            const dd   = String(date.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        } catch (e) {
            console.error("Error calculating expiry date:", e);
            return '';
        }
    }

    const createPkg = document.getElementById('create-package');
    const createAct = document.getElementById('create-activation-date');
    const createExp = document.getElementById('create-expiry-date');
    if (createPkg && createAct && createExp) {
        function updateCreateExpiry() {
            const pid = createPkg.value;
            const actDate = createAct.value;
            if (packageDurations && packageDurations[pid] && actDate) {
                createExp.value = calculateExpiryDate(actDate, pid);
            }
        }
        createPkg.addEventListener('change', updateCreateExpiry);
        createAct.addEventListener('change', updateCreateExpiry);
        createExp.addEventListener('input', () => {
            const pid = createPkg.value;
            const actDate = createAct.value;
            if (createExp.value && packageDurations && packageDurations[pid] && actDate
                && calculateExpiryDate(actDate, pid) !== createExp.value) {
                createPkg.value = '';
            }
        });
    }

    const editPkg = document.getElementById('edit-package');
    const editAct = document.getElementById('edit-activation-date');
    const editExp = document.getElementById('edit-expiry-date');
    if (editPkg && editAct && editExp) {
        function updateEditExpiry() {
            const pid = editPkg.value;
             const actDate = editAct.value;
            if (packageDurations && packageDurations[pid] && actDate) {
                editExp.value = calculateExpiryDate(actDate, pid);
            }
        }
        editPkg.addEventListener('change', updateEditExpiry);
        editAct.addEventListener('change', updateEditExpiry);
        editExp.addEventListener('input', () => {
            const pid = editPkg.value;
            const actDate = editAct.value;
            if (editExp.value && packageDurations && packageDurations[pid] && actDate
                && calculateExpiryDate(actDate, pid) !== editExp.value) {
                editPkg.value = '';
            }
        });
    }

    const editUsernameInput = document.getElementById('edit-username');
    if (editUsernameInput) {
        editUsernameInput.addEventListener('focus', () => {
            window.showToast('Username TK không thể thay đổi', 'warning');
        });
    }

    async function fetchAndDisplayUserInfo(email, infoElementId) {
        const infoElement = document.getElementById(infoElementId);
        if (!infoElement || !email) return;

        try {
            const result = await getJson(`${basePath}public/actions/account/index.php?action=search_users&email=${encodeURIComponent(email)}&exact=1`);
            const users = result.data?.users;
            if (result.success && users && users.length > 0) {
                const user = users[0];
                infoElement.innerHTML = `<p style="font-size: var(--font-size-xs); margin-top: 4px; color: var(--gray-600);">Người dùng: <strong>${user.username}</strong> — SĐT: ${user.phone || 'N/A'}</p>`;
            } else {
                infoElement.innerHTML = '';
            }
        } catch (error) {
            console.error('Error fetching user info:', error);
            infoElement.innerHTML = '<p style="font-size: var(--font-size-xs); margin-top: 4px; color: var(--danger-500);">Lỗi tìm user</p>';
        }
    }

    function setupEmailAutocomplete(inputId, listId, infoId) {
        const inputElement = document.getElementById(inputId);
        const dataListElement = document.getElementById(listId);
        const infoElement = document.getElementById(infoId);
        let searchTimer;
        let currentUsers = [];

        if (!inputElement || !dataListElement || !infoElement) {
            console.warn(`Autocomplete setup skipped: Elements not found for ${inputId}`);
            return;
        }

        inputElement.addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            const query = e.target.value.trim();

            if (query.length < 2) {
                dataListElement.innerHTML = '';
                infoElement.innerHTML = '';
                currentUsers = [];
                return;
            }

            searchTimer = setTimeout(async () => {
                try {
                    const result = await getJson(`${basePath}public/actions/account/index.php?action=search_users&email=${encodeURIComponent(query)}`);
                    const users = result.data?.users;
                    if (result.success && users) {
                        currentUsers = users;
                        dataListElement.innerHTML = currentUsers.map(user =>
                            `<option value="${user.email}">${user.username} (${user.phone || 'N/A'})</option>`
                        ).join('');
                    } else {
                        dataListElement.innerHTML = '';
                        currentUsers = [];
                    }
                } catch (error) {
                    console.error('Error fetching user suggestions:', error);
                    dataListElement.innerHTML = '';
                    currentUsers = [];
                }
            }, 300);
        });

        inputElement.addEventListener('change', (e) => {
             clearTimeout(searchTimer);
             const selectedEmail = e.target.value;
             const matchedUser = currentUsers.find(user => user.email === selectedEmail);

             if (matchedUser) {
                 infoElement.innerHTML = `<p style="font-size: var(--font-size-xs); margin-top: 4px; color: var(--gray-600);">Người dùng: <strong>${matchedUser.username}</strong> — SĐT: ${matchedUser.phone || 'N/A'}</p>`;
             } else {
                 if (selectedEmail && selectedEmail.includes('@')) {
                     fetchAndDisplayUserInfo(selectedEmail, infoId);
                 } else {
                     infoElement.innerHTML = '';
                 }
             }
        });
    }

    setupEmailAutocomplete('create-user-email', 'emailSuggestionsCreate', 'create-user-info');
    setupEmailAutocomplete('edit-user-email', 'emailSuggestionsEdit', 'edit-user-info');

    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox?.addEventListener('change', () => {
        const checked = selectAllCheckbox.checked;
        document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = checked);
    });

    async function bulkToggleStatus() {
        const ids = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
        if (ids.length === 0) {
            return alert('Vui lòng chọn ít nhất một tài khoản.');
        }
        if (!confirm(`Bạn có chắc muốn đảo trạng thái cho ${ids.length} tài khoản?`)) return;
        try {
            await Promise.all(ids.map(id => {
                const row = document.querySelector(`tr[data-account-id="${id}"]`);
                const status = row?.dataset.status;
                const action = (status === 'suspended' || status === 'pending') ? 'reactivate' : 'suspend';
                return postJson(`${apiBasePath}?action=toggle_account_status`, { id, action });
            }));
            window.showToast('Đã đảo trạng thái xong.', 'success');
            window.location.reload();
        } catch (e) {
            console.error(e);
            window.showToast('Lỗi khi đảo trạng thái.', 'error');
        }
    }

    async function bulkDeleteAccounts() {
        const ids = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
        if (ids.length === 0) {
            return alert('Vui lòng chọn ít nhất một tài khoản.');
        }
        if (!confirm(`Bạn có chắc chắn muốn xóa ${ids.length} tài khoản? Hành động này không thể hoàn tác.`)) return;
        try {
            await Promise.all(ids.map(id =>
                postJson(`${apiBasePath}?action=delete_account`, { id })
            ));
            window.showToast('Đã xóa các tài khoản được chọn.', 'success');
            window.location.reload();
        } catch (e) {
            console.error(e);
            window.showToast('Lỗi khi xóa.', 'error');
        }
    }

    window.AccountManagementPageEvents = {
        closeModal: helperCloseModal,
        openCreateMeasurementAccountModal,
        openEditAccountModal,
        viewAccountDetails,
        deleteAccount,
        toggleAccountStatus,
        bulkToggleStatus,
        bulkDeleteAccounts
    };
    Object.assign(window, window.AccountManagementPageEvents);

    // Ensure table row action buttons (view/edit) are non-submitting buttons
    document.querySelectorAll('#accountsTable .action-buttons button').forEach(function(btn) {
        btn.setAttribute('type', 'button');
    });

}); // End DOMContentLoaded
