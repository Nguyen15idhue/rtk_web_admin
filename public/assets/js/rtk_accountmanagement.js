document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-button');
    const searchInput = document.getElementById('account-search');
    const accountCards = document.querySelectorAll('.account-card');
    const accountsListContainer = document.getElementById('accounts-list-container');
    const emptyStateHTML = `
        <div class="empty-state">
            <h3>Không tìm thấy tài khoản</h3>
            <p>Không có tài khoản nào khớp với tiêu chí lọc hoặc tìm kiếm của bạn.</p>
        </div>`;

    // --- Hàm Lọc và Tìm kiếm ---
    function filterAndSearchAccounts() {
        // Ensure elements exist before proceeding
        if (!accountsListContainer || !searchInput) return;

        const activeFilterButton = document.querySelector('.filter-button.active');
        const activeFilter = activeFilterButton ? activeFilterButton.getAttribute('data-filter') : 'all';
        const searchTerm = searchInput.value.toLowerCase().trim();
        let matchFound = false;

        accountCards.forEach(card => {
            const status = card.getAttribute('data-status');
            const searchTerms = card.getAttribute('data-search-terms'); // Lấy dữ liệu search đã chuẩn bị sẵn

            const statusMatch = (activeFilter === 'all' || status === activeFilter);
            // Ensure searchTerms is not null before calling includes
            const searchMatch = (searchTerm === '' || (searchTerms && searchTerms.includes(searchTerm)));

            if (statusMatch && searchMatch) {
                card.style.display = ''; // Hiện card (use default grid display)
                matchFound = true;
            } else {
                card.style.display = 'none'; // Ẩn card
            }
        });

        // Hiển thị trạng thái trống nếu không tìm thấy kết quả
        const currentEmptyState = accountsListContainer.querySelector('.empty-state');
        if (!matchFound && !currentEmptyState) {
            accountsListContainer.insertAdjacentHTML('beforeend', emptyStateHTML);
        } else if (matchFound && currentEmptyState) {
            currentEmptyState.remove();
        } else if (!matchFound && currentEmptyState) {
            // Already showing empty state, do nothing
        }
    }

    // --- Event Listener cho Nút Lọc ---
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterAndSearchAccounts();
        });
    });

    // --- Event Listener cho Ô Tìm kiếm ---
    if (searchInput) {
        searchInput.addEventListener('input', filterAndSearchAccounts);
    }

    // --- Event Listener cho Hiện/Ẩn Mật khẩu ---
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const passwordSpan = this.previousElementSibling; // Lấy thẻ span chứa mật khẩu
            if (!passwordSpan) return;
            const actualPassword = passwordSpan.getAttribute('data-password');
            if (passwordSpan.textContent === '**********') {
                passwordSpan.textContent = actualPassword;
                this.textContent = 'Ẩn';
            } else {
                passwordSpan.textContent = '**********';
                this.textContent = 'Hiện';
            }
        });
    });

    // --- Event Listener cho Hiện/Ẩn Danh sách Trạm ---
    document.querySelectorAll('.toggle-stations').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetSelector = this.getAttribute('data-target');
            if (!targetSelector) return;
            const stationList = document.querySelector(targetSelector);
            if (stationList) {
                 // Find list items that were initially hidden (more robust than checking style directly)
                 const allItems = Array.from(stationList.querySelectorAll('li'));
                 const initiallyVisibleCount = parseInt(stationList.dataset.initiallyVisible || '3'); // Assuming 3 initially visible, store this if dynamic

                if (stationList.classList.contains('expanded')) {
                    // Thu gọn
                    stationList.classList.remove('expanded');
                    allItems.forEach((item, index) => {
                        if (index >= initiallyVisibleCount) {
                            item.style.display = 'none'; // Hide items beyond the initial count
                        }
                    });
                    this.textContent = 'Hiện thêm';
                } else {
                    // Mở rộng
                    stationList.classList.add('expanded');
                     allItems.forEach(item => {
                        item.style.display = 'inline-block'; // Show all items
                    });
                    this.textContent = 'Ẩn bớt';
                }
            }
        });
        // Store initially visible count if needed for robustness
        const targetSelector = toggle.getAttribute('data-target');
        if(targetSelector){
            const stationList = document.querySelector(targetSelector);
            if(stationList){
                const visibleItems = stationList.querySelectorAll('li:not([style*="display: none"])');
                stationList.dataset.initiallyVisible = visibleItems.length;
            }
        }
    });


    // --- Event Listener cho Nút Xem Chi Tiết (Placeholder) ---
    document.querySelectorAll('.btn-view').forEach(button => {
        button.addEventListener('click', function() {
            const accountId = this.getAttribute('data-account-id');
            // Thay thế bằng logic thực tế (ví dụ: mở modal, chuyển trang)
            alert('Xem chi tiết tài khoản #' + accountId);
            // Check if baseUrl is defined before using it
            if (typeof baseUrl !== 'undefined') {
                 // window.location.href = `${baseUrl}/pages/account_details.php?id=${accountId}`;
            } else {
                console.error("Base URL is not defined for redirection.");
                // Fallback or error handling
                // window.location.href = `/pages/account_details.php?id=${accountId}`; // Example fallback
            }
        });
    });

     // --- Event Listener cho Nút Gia Hạn (Chuyển trang) ---
     // Đã xử lý bằng thẻ <a> với href đúng trong PHP

     // Initial filter on page load if needed
     // filterAndSearchAccounts();
});

function updateTableRow(accountId, updatedData) {
    const row = accountsTableBody?.querySelector(`tr[data-account-id="${accountId}"]`);
    if (!row) return;

    // Update specific cells if needed, e.g., username
    const usernameCell = row.cells[1]; // Assuming username is the second column
    if (usernameCell && updatedData.username_acc) {
        usernameCell.textContent = updatedData.username_acc;
    }

    // Potentially update status and buttons if the edit affects them
    // This might require fetching the updated row HTML or specific data
    // For now, we only update username after edit.
    // Status/button updates are handled separately by toggle/delete functions.
}

// --- NEW: Function to handle status toggle (Suspend/Reactivate) ---
function toggleAccountStatus(accountId, action, event) {
    event.preventDefault(); // Prevent default button behavior
    event.stopPropagation(); // Stop event bubbling

    const actionText = action === 'suspend' ? 'đình chỉ (disable)' : 'kích hoạt lại (enable)';
    const confirmationMessage = `Bạn có chắc chắn muốn ${actionText} tài khoản ID ${accountId}?`;

    if (!confirm(confirmationMessage)) {
        return; // User cancelled
    }

    const button = event.currentTarget;
    const originalButtonContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`${basePath}private/actions/account/toggle_status.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: accountId, action: action })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast('success', result.message || `Tài khoản ${accountId} đã được ${action === 'suspend' ? 'đình chỉ' : 'kích hoạt lại'}.`);
            // Update UI dynamically using new status and HTML from response
            updateAccountRowStatus(accountId, result.newStatus, result.newStatusBadgeHtml, result.newButtonsHtml); // Pass badge HTML
        } else {
            showToast('error', result.message || `Lỗi khi ${actionText} tài khoản.`);
        }
    })
    .catch(error => {
        console.error(`Error toggling account status for ${accountId}:`, error);
        showToast('error', `Lỗi kết nối khi ${actionText} tài khoản.`);
    })
    .finally(() => {
        // Restore button only if it hasn't been replaced by new HTML
        if (button.closest('td')) { // Check if button is still in the DOM
             button.disabled = false;
             button.innerHTML = originalButtonContent;
        }
    });
}

// --- NEW: Function to handle account deletion ---
function deleteAccount(accountId, event) {
    event.preventDefault();
    event.stopPropagation();

    const confirmationMessage = `Bạn có chắc chắn muốn XÓA tài khoản ID ${accountId}? Hành động này không thể hoàn tác (tài khoản sẽ bị đánh dấu xóa).`;

    if (!confirm(confirmationMessage)) {
        return; // User cancelled
    }

    const button = event.currentTarget;
    const originalButtonContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`${basePath}private/actions/account/delete_account.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: accountId })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast('success', result.message || `Tài khoản ${accountId} đã được xóa.`);
            // Remove the row from the table
            const row = button.closest('tr');
            if (row) {
                row.remove();
                // Check if table is empty after deletion
                if (accountsTableBody && accountsTableBody.rows.length === 0 && noResultsRow) {
                    noResultsRow.style.display = 'table-row';
                }
            }
        } else {
            showToast('error', result.message || 'Lỗi khi xóa tài khoản.');
            // Restore button on failure
            button.disabled = false;
            button.innerHTML = originalButtonContent;
        }
    })
    .catch(error => {
        console.error(`Error deleting account ${accountId}:`, error);
        showToast('error', 'Lỗi kết nối khi xóa tài khoản.');
        // Restore button on failure
        button.disabled = false;
        button.innerHTML = originalButtonContent;
    });
}

// --- UPDATED: Function to update row status and buttons ---
function updateAccountRowStatus(accountId, newStatus, newStatusBadgeHtml, newButtonsHtml) { // Added newStatusBadgeHtml parameter
    const row = accountsTableBody?.querySelector(`tr[data-account-id="${accountId}"]`);
    if (!row) return;

    // Update status badge (assuming status is in the 7th column, index 6)
    const statusCell = row.cells[6];
    if (statusCell && newStatusBadgeHtml) { // Use the HTML directly from response
        statusCell.innerHTML = newStatusBadgeHtml;
    }

    // Update action buttons (assuming actions are in the 8th column, index 7)
    const actionsCell = row.cells[7];
    if (actionsCell && newButtonsHtml) {
        actionsCell.innerHTML = newButtonsHtml;
    }

    // Update the row's data-status attribute
    row.dataset.status = newStatus.toLowerCase();
}

// --- NEW or UPDATED: Function to refresh the list (basic reload) ---
function refreshAccountList() {
    // A simple way is to reload the page, preserving filters if they are in the URL
    window.location.reload();
    // More advanced: Use fetch to get updated table content and replace tbody
}

// --- Ensure Toast functions exist ---
function showToast(type, message) {
    // ... (implementation as provided before or ensure it exists)
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('hide');
        toast.addEventListener('transitionend', () => toast.remove());
    }, 5000);
}

function createToastContainer() {
    // ... (implementation as provided before or ensure it exists)
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        // Add necessary styles for positioning (e.g., fixed, top, right, z-index)
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '1050';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '10px';
        document.body.appendChild(container);
    }
    return container;
}

// Add CSS for toasts if not already present globally
if (!document.getElementById('toast-styles')) {
    const style = document.createElement('style');
    style.id = 'toast-styles';
    style.textContent = `
        /* ... (Toast CSS as provided before) ... */
        .toast {
            padding: 10px 20px;
            border-radius: 4px;
            color: #fff;
            font-size: 0.9em;
            opacity: 1;
            transition: opacity 0.5s ease-out;
            min-width: 200px;
            max-width: 400px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .toast.hide {
            opacity: 0;
        }
        .toast-success { background-color: #28a745; }
        .toast-error { background-color: #dc3545; }
        .toast-info { background-color: #17a2b8; }
        .toast-warning { background-color: #ffc107; color: #333; }
    `;
    document.head.appendChild(style);
}

// Make sure event listeners for create/edit modals are correctly placed
document.addEventListener('DOMContentLoaded', () => {
    const createForm = document.getElementById('createAccountForm');
    if (createForm) {
        createForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleCreateAccountSubmit(this);
        });
    }

    const editForm = document.getElementById('editAccountForm');
    if (editForm) {
        editForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleEditAccountSubmit(this);
        });
    }

    // Add event listener for filter form if it exists
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            // Implement filter logic or simply let the page reload via form GET action
            // For JS-driven filtering, you would fetch data with new filters here.
            // For simple GET filtering, the form submission handles it.
            filterForm.submit(); // Or handle via JS fetch
        });
    }
});
