document.addEventListener('DOMContentLoaded', () => {
    const { getJson, postJson, postForm } = window.api;

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

    // --- Global Functions (accessible via window or directly if not conflicting) ---

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
        // Reset forms and errors when closing
        if (modalId === 'createAccountModal' && createAccountForm) {
            createAccountForm.reset();
            if(createAccountError) createAccountError.textContent = '';
            // Reset user info display
            const createUserInfo = document.getElementById('create-user-info');
            if (createUserInfo) createUserInfo.innerHTML = '';
        } else if (modalId === 'editAccountModal' && editAccountForm) {
            editAccountForm.reset();
             if(editAccountError) editAccountError.textContent = '';
             // Reset user info display
             const editUserInfo = document.getElementById('edit-user-info');
             if (editUserInfo) editUserInfo.innerHTML = '';
        }
    }

    window.openCreateMeasurementAccountModal = function() {
        if (createAccountForm) createAccountForm.reset();
        if (createAccountError) createAccountError.textContent = '';
        const createUserInfo = document.getElementById('create-user-info');
        if (createUserInfo) createUserInfo.innerHTML = '';
        if (createModal) createModal.style.display = 'block';
    }

    window.openEditAccountModal = async function(accountId) {
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
            // envelope.data is the account object (or fallback to old .account)
            const account = env.data || env.account;
            if (!account) {
                throw new Error('No account payload');
            }
            editAccountForm.querySelector('#edit-account-id').value = account.id;
            editAccountForm.querySelector('#edit-username').value = account.username_acc || '';
            editAccountForm.querySelector('#edit-user-email').value = account.user_email || '';
            editAccountForm.querySelector('#edit-location').value = account.location_id || '';
            editAccountForm.querySelector('#edit-package').value = account.package_id || '';
            // Ensure date format is YYYY-MM-DD for input type="date"
            editAccountForm.querySelector('#edit-activation-date').value = account.activation_date ? account.activation_date.split(' ')[0] : '';
            editAccountForm.querySelector('#edit-expiry-date').value = account.expiry_date ? account.expiry_date.split(' ')[0] : '';
            editAccountForm.querySelector('#edit-status').value = account.derived_status || 'unknown'; // Use derived_status

            // Display user info if email exists
            if (account.user_email) {
                fetchAndDisplayUserInfo(account.user_email, 'edit-user-info');
            }

            editModal.style.display = 'block';
        } catch (error) {
            console.error('Error fetching account details:', error);
            window.showToast(error.message || 'Không thể tải chi tiết tài khoản.', 'error');
        }
    }

    window.viewAccountDetails = function(accountId) {
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

    // Function to generate status badge HTML (mirrors PHP helper)
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
                closeModal('createAccountModal');
                window.location.reload(); // Reload to see the new account
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
                closeModal('editAccountModal');
                // Update the table row directly instead of full reload for better UX
                if (result.account) {
                    updateTableRow(accountId, result.account);
                } else {
                    // Fallback to reload if updated data isn't returned
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
                // Assuming API returns YYYY-MM-DD HH:MM:SS or just YYYY-MM-DD
                const datePart = dateString.split(' ')[0];
                const [year, month, day] = datePart.split('-');
                return `${day}/${month}/${year}`; // Format to DD/MM/YYYY
            } catch (e) {
                console.error("Error formatting date:", dateString, e);
                return dateString; // Return original if formatting fails
            }
        };

        // Update cell content based on updatedData
        row.cells[1].textContent = updatedData.username_acc || '';
        row.cells[2].textContent = updatedData.user_email || '';
        row.cells[3].textContent = updatedData.package_name || '';
        row.cells[4].textContent = formatDateCell(updatedData.activation_date);
        row.cells[5].textContent = formatDateCell(updatedData.expiry_date);
        row.cells[6].innerHTML = get_account_status_badge_js(updatedData.derived_status || 'unknown'); // Update status badge
        row.dataset.status = updatedData.derived_status || 'unknown'; // Update data attribute

        // Re-generate action buttons if necessary (requires updatedData to contain all needed fields for get_account_action_buttons logic)
        // This part is complex as it requires replicating the PHP logic in JS or making another API call.
        // For now, we only update data fields and the toggle button state.
        const actionCell = row.cells[7];
        const toggleButton = actionCell.querySelector('button[onclick*="toggleAccountStatus"]');
        if (toggleButton) {
             const newStatus = updatedData.derived_status;
             const isSuspended = newStatus === 'suspended';
             const isPending = newStatus === 'pending';
             let newAction = '';
             let newIcon = '';
             let newTitle = '';
             let newClass = 'btn-icon '; // Base class

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
             } else { // active, expired, rejected (can be suspended)
                 newAction = 'suspend';
                 newIcon = 'fa-ban';
                 newTitle = 'Đình chỉ';
                 newClass += 'btn-reject';
             }

             toggleButton.title = newTitle;
             toggleButton.className = newClass; // Update class for color
             toggleButton.setAttribute('onclick', `toggleAccountStatus('${accountId}', '${newAction}', event)`);
             const iconElement = toggleButton.querySelector('i');
             if (iconElement) {
                 iconElement.className = `fas ${newIcon}`;
             }
             // Hide/show toggle button based on status if needed (e.g., hide for 'rejected' or 'expired')
             if (newStatus === 'rejected' || newStatus === 'expired') {
                 // Maybe hide the suspend/unsuspend button entirely for these states
                 // toggleButton.style.display = 'none';
             } else {
                 // toggleButton.style.display = ''; // Ensure it's visible otherwise
             }
        }
         // Update edit button (usually always present unless permissions change)
        const editButton = actionCell.querySelector('button[onclick*="openEditAccountModal"]');
        if (editButton) {
            // Potentially disable edit based on status?
            // editButton.disabled = (newStatus === 'expired' || newStatus === 'rejected');
        }

        // Update delete button (usually always present unless permissions change)
        const deleteButton = actionCell.querySelector('button[onclick*="deleteAccount"]');
         if (deleteButton) {
            // Potentially disable delete based on status?
            // deleteButton.disabled = (newStatus === 'active');
        }
    }

    window.deleteAccount = async function(accountId, event) {
        event.stopPropagation(); // Prevent triggering row click or other parent events
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
                    // Check if table is empty and show 'no results' row
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

    window.toggleAccountStatus = async function(accountId, action, event) {
        event.stopPropagation(); // Prevent triggering row click or other parent events

        let confirmMessage = `Bạn có chắc muốn ${action === 'suspend' ? 'đình chỉ' : action === 'unsuspend' ? 'bỏ đình chỉ' : action === 'approve' ? 'phê duyệt' : 'thực hiện hành động này với'} tài khoản ID ${accountId}?`;
        if (!confirm(confirmMessage)) {
            return;
        }

        try {
            const result = await postJson(`${apiBasePath}?action=toggle_account_status`, { id: accountId, action });

            if (result.success) {
                window.showToast(result.message || 'Cập nhật trạng thái thành công!', 'success');
                // Update the table row directly
                if (result.account) {
                    updateTableRow(accountId, result.account);
                } else {
                    // Fallback to reload if updated data isn't returned
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

    // --- Event Listeners ---

    // Attach submit listeners
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

    // Close modals if clicked outside the content area
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target == modal) {
                closeModal(modal.id);
            }
        });
    });

    // Add auto-expiry calculation logic
    // packageDurations is expected to be a global variable defined via inline script

    function calculateExpiryDate(activation, pkgId) {
        if (!activation || !packageDurations || !packageDurations[pkgId]) return '';
        try {
            const date = new Date(activation);
            // Check if date is valid before proceeding
            if (isNaN(date.getTime())) {
                console.error("Invalid activation date provided:", activation);
                return '';
            }
            const dur = packageDurations[pkgId];
            if (dur.days)   date.setDate(date.getDate() + dur.days);
            if (dur.months) date.setMonth(date.getMonth() + dur.months);
            if (dur.years)  date.setFullYear(date.getFullYear() + dur.years);

            // Check if the resulting date is valid
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

    // Bind events cho form Tạo
    const createPkg = document.getElementById('create-package');
    const createAct = document.getElementById('create-activation-date');
    const createExp = document.getElementById('create-expiry-date');
    if (createPkg && createAct && createExp) {
        function updateCreateExpiry() {
            const pid = createPkg.value;
            const actDate = createAct.value;
            if (packageDurations && packageDurations[pid] && actDate) {
                createExp.value = calculateExpiryDate(actDate, pid);
            } else if (!pid) {
                // If no package is selected, don't auto-calculate
                // createExp.value = ''; // Optionally clear expiry if package is unselected
            }
        }
        createPkg.addEventListener('change', updateCreateExpiry);
        createAct.addEventListener('change', updateCreateExpiry);
        // When expiry date is manually changed, deselect the package if it no longer matches
        createExp.addEventListener('input', () => {
            const pid = createPkg.value;
            const actDate = createAct.value;
            if (createExp.value && packageDurations && packageDurations[pid] && actDate
                && calculateExpiryDate(actDate, pid) !== createExp.value) {
                createPkg.value = ''; // Set package to "Select package" or equivalent empty value
            }
        });
    }

    // Bind events cho form Chỉnh sửa (tương tự)
    const editPkg = document.getElementById('edit-package');
    const editAct = document.getElementById('edit-activation-date');
    const editExp = document.getElementById('edit-expiry-date');
    if (editPkg && editAct && editExp) {
        function updateEditExpiry() {
            const pid = editPkg.value;
             const actDate = editAct.value;
            if (packageDurations && packageDurations[pid] && actDate) {
                editExp.value = calculateExpiryDate(actDate, pid);
            } else if (!pid) {
                 // Optionally clear expiry if package is unselected
                 // editExp.value = '';
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

    // Warn when focusing readonly username field
    const editUsernameInput = document.getElementById('edit-username');
    if (editUsernameInput) {
        editUsernameInput.addEventListener('focus', () => {
            window.showToast('Username TK không thể thay đổi', 'warning');
        });
    }

    // --- Email Autocomplete & User Info Display ---
    // Helper to fetch user info based on email
    async function fetchAndDisplayUserInfo(email, infoElementId) {
        const infoElement = document.getElementById(infoElementId);
        if (!infoElement || !email) return;

        try {
            const result = await getJson(`${basePath}public/actions/account/index.php?action=search_users&email=${encodeURIComponent(email)}&exact=1`);
            const users = result.data?.users;
            if (result.success && users && users.length > 0) {
                const user = users[0]; // exact-match user
                infoElement.innerHTML = `<p style="font-size: var(--font-size-xs); margin-top: 4px; color: var(--gray-600);">Người dùng: <strong>${user.username}</strong> — SĐT: ${user.phone || 'N/A'}</p>`;
            } else {
                infoElement.innerHTML = ''; // Clear if no user found
            }
        } catch (error) {
            console.error('Error fetching user info:', error);
            infoElement.innerHTML = '<p style="font-size: var(--font-size-xs); margin-top: 4px; color: var(--danger-500);">Lỗi tìm user</p>';
        }
    }

    // Setup autocomplete for Create and Edit forms
    function setupEmailAutocomplete(inputId, listId, infoId) {
        const inputElement = document.getElementById(inputId);
        const dataListElement = document.getElementById(listId);
        const infoElement = document.getElementById(infoId);
        let searchTimer;
        let currentUsers = []; // Store fetched users for the current input session

        if (!inputElement || !dataListElement || !infoElement) {
            console.warn(`Autocomplete setup skipped: Elements not found for ${inputId}`);
            return;
        }

        inputElement.addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            const query = e.target.value.trim();

            if (query.length < 2) { // Start searching after 2 characters
                dataListElement.innerHTML = '';
                infoElement.innerHTML = ''; // Clear info on input clear/short query
                currentUsers = [];
                return;
            }

            searchTimer = setTimeout(async () => {
                try {
                    const result = await getJson(`${basePath}public/actions/account/index.php?action=search_users&email=${encodeURIComponent(query)}`);
                    const users = result.data?.users;
                    if (result.success && users) {
                        currentUsers = users; // Update cache
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
            }, 300); // Debounce time
        });

        // Use 'change' event to finalize selection and display info
        inputElement.addEventListener('change', (e) => {
             clearTimeout(searchTimer); // Clear any pending search
             const selectedEmail = e.target.value;
             const matchedUser = currentUsers.find(user => user.email === selectedEmail);

             if (matchedUser) {
                 infoElement.innerHTML = `<p style="font-size: var(--font-size-xs); margin-top: 4px; color: var(--gray-600);">Người dùng: <strong>${matchedUser.username}</strong> — SĐT: ${matchedUser.phone || 'N/A'}</p>`;
             } else {
                 // If the email doesn't match any suggestion, try fetching directly
                 // This handles cases where the user types/pastes a full valid email
                 // without selecting from the dropdown.
                 if (selectedEmail && selectedEmail.includes('@')) { // Basic email format check
                     fetchAndDisplayUserInfo(selectedEmail, infoId);
                 } else {
                     infoElement.innerHTML = ''; // Clear if input is not a valid email or no match
                 }
             }
        });
    }

    setupEmailAutocomplete('create-user-email', 'emailSuggestionsCreate', 'create-user-info');
    setupEmailAutocomplete('edit-user-email', 'emailSuggestionsEdit', 'edit-user-info');

}); // End DOMContentLoaded
