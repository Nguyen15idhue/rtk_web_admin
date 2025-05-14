document.addEventListener('DOMContentLoaded', () => {
    // Base path and API base path for account handlers
    const basePath = ('/' + ((window.appConfig && window.appConfig.basePath) || ''))
        .replace(/\/\/+/, '/')  // normalize multiple slashes
        .replace(/\/?$/, '/');    // ensure trailing slash
    const apiBasePath = `${basePath}public/handlers/account/index.php`;

    const { getJson, postJson, postForm } = window.api;
    const { closeModal: helperCloseModal } = window.helpers;

    const accountsTableBody = document.getElementById('accountsTable')?.querySelector('tbody');
    const noResultsRow = document.getElementById('no-results-row');

    // Modal elements
    const viewModal = document.getElementById('viewAccountModal');
    const viewDetailsContent = document.getElementById('viewAccountDetailsContent');
    const createModal = document.getElementById('createAccountModal');
    const editModal = document.getElementById('editAccountModal');
    const renewModal = document.getElementById('renewAccountModal'); // New modal
    const createAccountForm = document.getElementById('createAccountForm');
    const editAccountForm = document.getElementById('editAccountForm');
    const renewAccountForm = document.getElementById('renewAccountForm'); // New form
    const createAccountError = document.getElementById('createAccountError');
    const editAccountError = document.getElementById('editAccountError');
    const renewAccountError = document.getElementById('renewAccountError'); // New error display

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

    async function openRenewAccountModal(accountId) {
        if (!renewModal || !renewAccountForm) return;

        renewAccountForm.reset();
        if (renewAccountError) renewAccountError.textContent = '';

        try {
            const env = await getJson(`${apiBasePath}?action=get_account_details&id=${accountId}`);
            if (!env.success) {
                throw new Error(env.message || `Không thể lấy chi tiết tài khoản.`);
            }
            const account = env.data || env.account;
            if (!account) {
                throw new Error('No account payload');
            }

            renewAccountForm.dataset.originalOldExpiry = account.expiry_date ? account.expiry_date.split(' ')[0] : ''; // Store original old expiry date

            renewAccountForm.querySelector('#renew-account-id').value = account.id;
            renewAccountForm.querySelector('#renew-username-display').textContent = account.username_acc || 'N/A';
            renewAccountForm.querySelector('#renew-current-package-display').textContent = account.package_name || 'N/A';
            renewAccountForm.querySelector('#renew-current-expiry-display').textContent = account.expiry_date ? account.expiry_date.split(' ')[0] : 'N/A';
            
            renewAccountForm.querySelector('#renew-package').value = account.package_id || '';

            // 1. Set "New Activation Date" for the form to today's date.
            const todayDateObj = new Date();
            todayDateObj.setHours(0, 0, 0, 0); // Normalize to start of day

            const todayYear = todayDateObj.getFullYear();
            const todayMonth = String(todayDateObj.getMonth() + 1).padStart(2, '0');
            const todayDay = String(todayDateObj.getDate()).padStart(2, '0');
            const todayDateString = `${todayYear}-${todayMonth}-${todayDay}`;

            renewAccountForm.querySelector('#renew-activation-date').value = todayDateString;
            
            // 2. Determine the base date for calculating the new expiry date.
            // This base date is MAX(old_expiry_date, today).
            let baseDateForExpiryCalculationObj = todayDateObj; // Default to today

            if (account.expiry_date) {
                const oldExpiryDateStr = account.expiry_date.split(' ')[0]; // "YYYY-MM-DD"
                const parts = oldExpiryDateStr.split('-');
                if (parts.length === 3) {
                    const oldExpiryDateCandidateObj = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    oldExpiryDateCandidateObj.setHours(0,0,0,0); 

                    if (!isNaN(oldExpiryDateCandidateObj.getTime()) && oldExpiryDateCandidateObj > todayDateObj) {
                        baseDateForExpiryCalculationObj = oldExpiryDateCandidateObj;
                    }
                }
            }
            
            const baseYear = baseDateForExpiryCalculationObj.getFullYear();
            const baseMonth = String(baseDateForExpiryCalculationObj.getMonth() + 1).padStart(2, '0');
            const baseDay = String(baseDateForExpiryCalculationObj.getDate()).padStart(2, '0');
            const baseDateForExpiryCalculationString = `${baseYear}-${baseMonth}-${baseDay}`;

            // 3. Calculate and set the "New Expiry Date" based on this baseDateForExpiryCalculationString and selected package.
            const newSuggestedExpiry = calculateExpiryDate(baseDateForExpiryCalculationString, account.package_id || '');
            renewAccountForm.querySelector('#renew-expiry-date').value = newSuggestedExpiry;

            renewModal.style.display = 'block';
        } catch (error) {
            console.error('Error fetching account details for renewal:', error);
            window.showToast(error.message || 'Không thể tải chi tiết tài khoản cho gia hạn.', 'error');
        }
    }

    function viewAccountDetails(accountId) {
        const mainDetailsContainer = document.getElementById('viewAccountMainDetails');
        const mountpointsContainer = document.getElementById('viewAccountMountpoints');
        const loadingIndicator = document.getElementById('viewAccountLoading');
        const errorIndicator = document.getElementById('viewAccountError');

        // Reset content and show loading
        if (mainDetailsContainer) mainDetailsContainer.innerHTML = '';
        if (mountpointsContainer) mountpointsContainer.innerHTML = '';
        if (errorIndicator) {
            errorIndicator.innerHTML = '';
            errorIndicator.style.display = 'none';
        }
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        
        if (viewModal) viewModal.style.display = 'block';

        getJson(`${apiBasePath}?action=get_account_details&id=${accountId}`)
            .then(env => {
                if (loadingIndicator) loadingIndicator.style.display = 'none';

                if (!env.success) {
                    if (errorIndicator) {
                        errorIndicator.innerHTML = `<p class="error">Không thể tải chi tiết tài khoản. ${env.message || ''}</p>`;
                        errorIndicator.style.display = 'block';
                    } else if (viewDetailsContent) { // Fallback if new elements aren't found
                        viewDetailsContent.innerHTML = `<p class="error">Không thể tải chi tiết tài khoản.</p>`;
                    }
                    return;
                }
                const account = env.data || env.account || {};
                
                if (mainDetailsContainer) {
                    let mainHtml = '<dl class="account-details-list">';
                    mainHtml += `<dt>ID TK</dt><dd>${account.id || 'N/A'}</dd>`;
                    mainHtml += `<dt>Username TK</dt><dd>${account.username_acc || 'N/A'}</dd>`;
                    mainHtml += `<dt>Mật khẩu TK</dt><dd>${account.password_acc || 'N/A'}</dd>`; // Consider security implications
                    mainHtml += `<dt>Ngày KH</dt><dd>${account.activation_date ? account.activation_date.split(' ')[0] : 'N/A'}</dd>`;
                    mainHtml += `<dt>Ngày HH</dt><dd>${account.expiry_date ? account.expiry_date.split(' ')[0] : 'N/A'}</dd>`;
                    mainHtml += '</dl>';
                    mainDetailsContainer.innerHTML = mainHtml;
                }

                if (mountpointsContainer) {
                    if (Array.isArray(account.mountpoints) && account.mountpoints.length > 0) {
                        let mpHtml = '<h5>Các Mount Point</h5>';
                        mpHtml += '<table class="mp-table modern-table"><thead><tr><th>IP</th><th>Port</th><th>Tên Mount</th></tr></thead><tbody>';
                        account.mountpoints.forEach(mp => {
                            mpHtml += `<tr><td>${mp.ip || ''}</td><td>${mp.port || ''}</td><td>${mp.mountpoint || ''}</td></tr>`;
                        });
                        mpHtml += '</tbody></table>';
                        mountpointsContainer.innerHTML = mpHtml;
                    } else {
                        mountpointsContainer.innerHTML = '<p>Không có mount point nào được cấu hình.</p>';
                    }
                }
            })
            .catch(error => {
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (errorIndicator) {
                    errorIndicator.innerHTML = `<p class="error">Lỗi khi tải chi tiết: ${error.message}</p>`;
                    errorIndicator.style.display = 'block';
                } else if (viewDetailsContent) { // Fallback
                     viewDetailsContent.innerHTML = `<p class="error">Lỗi: ${error.message}</p>`;
                }
            });
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

    async function handleRenewAccountSubmit(form) {
        const formData = new FormData(form);
        const accountId = formData.get('id');
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang gia hạn...';
        if (renewAccountError) renewAccountError.textContent = '';

        try {
            const result = await postForm(`${apiBasePath}?action=manual_renew_account`, formData);

            if (result.success) {
                window.showToast(result.message || 'Gia hạn tài khoản thành công!', 'success');
                helperCloseModal('renewAccountModal');
                if (result.account) {
                    updateTableRow(accountId, result.account);
                } else {
                    window.location.reload();
                }
            } else {
                if (renewAccountError) renewAccountError.textContent = result.message || 'Gia hạn tài khoản thất bại.';
                window.showToast(result.message || 'Gia hạn tài khoản thất bại.', 'error');
            }
        } catch (error) {
            console.error('Error renewing account:', error);
            if (renewAccountError) renewAccountError.textContent = 'Lỗi khi gửi yêu cầu gia hạn.';
            window.showToast('Lỗi khi gửi yêu cầu gia hạn.', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Xác nhận Gia hạn';
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
        row.cells[6].innerHTML = updatedData.newStatusBadgeHtml || '<span class="status-badge badge-gray">Không xác định</span>'; // Use badge HTML from backend
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

    if (renewAccountForm) {
        renewAccountForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleRenewAccountSubmit(this);
        });
    } else {
        console.error("Element with ID 'renewAccountForm' not found.");
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

    const renewPkg = document.getElementById('renew-package');
    const renewAct = document.getElementById('renew-activation-date');
    const renewExp = document.getElementById('renew-expiry-date');
    if (renewPkg && renewAct && renewExp) {
        function updateRenewExpiry() {
            const pid = renewPkg.value;
            const newActivationDateStr = renewAct.value; // Value from the '#renew-activation-date' input
            const originalOldExpiryStr = renewAccountForm.dataset.originalOldExpiry;

            if (!newActivationDateStr) {
                renewExp.value = '';
                return;
            }

            // 1. Determine the base date for calculation: MAX(originalOldExpiry, newActivationDate)
            let baseDateForCalcObj;
            const newActParts = newActivationDateStr.split('-');
            if (newActParts.length !== 3) {
                renewExp.value = ''; // Invalid new activation date format
                return;
            }
            // Month is 0-indexed for Date constructor
            const newActivationDateObj = new Date(parseInt(newActParts[0]), parseInt(newActParts[1]) - 1, parseInt(newActParts[2]));
            newActivationDateObj.setHours(0,0,0,0);

            if (isNaN(newActivationDateObj.getTime())) {
                renewExp.value = ''; // Invalid new activation date
                return;
            }

            baseDateForCalcObj = newActivationDateObj; // Default to new activation date

            if (originalOldExpiryStr) {
                const oldExpiryParts = originalOldExpiryStr.split('-');
                if (oldExpiryParts.length === 3) {
                    // Month is 0-indexed for Date constructor
                    const oldExpiryDateCandidateObj = new Date(parseInt(oldExpiryParts[0]), parseInt(oldExpiryParts[1]) - 1, parseInt(oldExpiryParts[2]));
                    oldExpiryDateCandidateObj.setHours(0,0,0,0);

                    if (!isNaN(oldExpiryDateCandidateObj.getTime()) && oldExpiryDateCandidateObj > newActivationDateObj) {
                        baseDateForCalcObj = oldExpiryDateCandidateObj;
                    }
                }
            }

            // Convert baseDateForCalcObj to "YYYY-MM-DD" string
            const baseYear = baseDateForCalcObj.getFullYear();
            const baseMonth = String(baseDateForCalcObj.getMonth() + 1).padStart(2, '0');
            const baseDay = String(baseDateForCalcObj.getDate()).padStart(2, '0');
            const finalBaseDateForCalcString = `${baseYear}-${baseMonth}-${baseDay}`;

            // 2. Calculate and set the "New Expiry Date"
            if (packageDurations && packageDurations[pid] && finalBaseDateForCalcString) {
                renewExp.value = calculateExpiryDate(finalBaseDateForCalcString, pid);
            } else {
                renewExp.value = ''; // Clear if package not selected or other issue
            }
        }
        renewPkg.addEventListener('change', updateRenewExpiry);
        renewAct.addEventListener('change', updateRenewExpiry);
        renewExp.addEventListener('input', () => {
            const pid = renewPkg.value;
            const actDate = renewAct.value;
            if (renewExp.value && packageDurations && packageDurations[pid] && actDate &&
                calculateExpiryDate(actDate, pid) !== renewExp.value) {
                // renewPkg.value = ''; // Or just allow manual override
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
            const result = await getJson(`${basePath}public/handlers/account/index.php?action=search_users&email=${encodeURIComponent(email)}&exact=1`);
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
                    const result = await getJson(`${basePath}public/handlers/account/index.php?action=search_users&email=${encodeURIComponent(query)}`);
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
                 if (selectedEmail && selectedEmail.layouts('@')) {
                     fetchAndDisplayUserInfo(selectedEmail, infoId);
                 } else {
                     infoElement.innerHTML = '';
                 }
             }
        });
    }

    setupEmailAutocomplete('create-user-email', 'emailSuggestionsCreate', 'create-user-info');
    setupEmailAutocomplete('edit-user-email', 'emailSuggestionsEdit', 'edit-user-info');
    // Bulk select-all logic now centralized in utils/bulk_actions.js

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

    // open the modal to choose package for bulk renew
    function openBulkRenewModal() {
        const form = document.getElementById('bulkRenewForm');
        if (form) {
            form.reset();
            document.getElementById('bulkRenewError').textContent = '';
            document.getElementById('bulkRenewModal').style.display = 'block';
        }
    }

    // handle form submit for bulk renew
    async function handleBulkRenewSubmit(form) {
        form.querySelector('button[type="submit"]').disabled = true;
        const pkg = form.querySelector('#bulk-renew-package').value;
        if (!pkg) {
            document.getElementById('bulkRenewError').textContent = 'Vui lòng chọn gói.';
            form.querySelector('button[type="submit"]').disabled = false;
            return;
        }
        // gather selected account IDs
        const ids = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một tài khoản để gia hạn.');
            form.querySelector('button[type="submit"]').disabled = false;
            return;
        }
        if (!confirm(`Bạn có chắc chắn muốn gia hạn ${ids.length} tài khoản sang gói "${packagesList.find(p=>p.id==pkg).name}"?`)) {
            form.querySelector('button[type="submit"]').disabled = false;
            return;
        }
        // compute today string
        const today = new Date(); today.setHours(0,0,0,0);
        const y = today.getFullYear(), m = String(today.getMonth()+1).padStart(2,'0'), d = String(today.getDate()).padStart(2,'0');
        const todayStr = `${y}-${m}-${d}`;

        try {
            await Promise.all(ids.map(async id => {
                // fetch old expiry
                const env = await getJson(`${apiBasePath}?action=get_account_details&id=${id}`);
                const acc = env.data || env.account;
                // determine base date = max(old expiry, today)
                const oldExp = acc.expiry_date?.split(' ')[0] || '';
                let base = today;
                if (oldExp) {
                    const [yy,mm,dd] = oldExp.split('-');
                    const od = new Date(+yy, +mm-1, +dd);
                    if (!isNaN(od.getTime()) && od > today) base = od;
                }
                const by = base.getFullYear(), bm = String(base.getMonth()+1).padStart(2,'0'), bd = String(base.getDate()).padStart(2,'0');
                const baseStr = `${by}-${bm}-${bd}`;
                const newExp = calculateExpiryDate(baseStr, pkg);
                const fd = new FormData();
                fd.append('id', id);
                fd.append('package_id', pkg);
                fd.append('activation_date', todayStr);      // always start today
                fd.append('expiry_date', newExp);
                return postForm(`${apiBasePath}?action=manual_renew_account`, fd);
            }));
            window.showToast(`Đã gia hạn xong ${ids.length} tài khoản.`, 'success');
            closeModal('bulkRenewModal');
            window.location.reload();
        } catch (e) {
            console.error('Bulk renew error:', e);
            window.showToast('Lỗi khi gia hạn hàng loạt.', 'error');
        } finally {
            form.querySelector('button[type="submit"]').disabled = false;
        }
    }

    // hook the bulk renew button and form
    document.getElementById('bulkRenewBtn').addEventListener('click', openBulkRenewModal);
    const bulkRenewForm = document.getElementById('bulkRenewForm');
    if (bulkRenewForm) {
        bulkRenewForm.addEventListener('submit', e => {
            e.preventDefault();
            handleBulkRenewSubmit(bulkRenewForm);
        });
    }

    window.AccountManagementPageEvents = {
        closeModal: helperCloseModal,
        openCreateMeasurementAccountModal,
        openEditAccountModal,
        viewAccountDetails,
        deleteAccount,
        toggleAccountStatus,
        bulkToggleStatus,
        bulkDeleteAccounts,
        openRenewAccountModal,
        bulkRenewAccounts: openBulkRenewModal // legacy mapping to open new modal
    };
    Object.assign(window, window.AccountManagementPageEvents);

    // Ensure table row action buttons (view/edit) are non-submitting buttons
    document.querySelectorAll('#accountsTable .action-buttons button').forEach(function(btn) {
        btn.setAttribute('type', 'button');
    });

}); // End DOMContentLoaded
