/**
 * Content Header Interactive Components
 * Handles search, notifications, quick actions, and user menu
 */
(function(window, document) {
    'use strict';

    // Check if script is already loaded
    if (window.ContentHeader) {
        return;
    }

    // DOM Elements
    let searchInput;
    let searchResults;
    let notificationBell;
    let notificationDropdown;
    let quickActionsBtn;
    let quickActionsMenu;
    let userMenuTrigger;
    let userMenuDropdown;
    let userMenu;
    let currentTimeElement;
    let systemStatusElement;

    // Performance optimizations
    let isSearching = false;
    let notificationRefreshTimer = null;
    let systemStatusRefreshTimer = null;

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeElements();
        setupEventListeners();
        startClock();
        loadNotifications();
        checkSystemStatus();
        setupSystemStatusTooltip(); // Add tooltip setup
        
        // Performance: Use requestAnimationFrame for smoother animations
        requestAnimationFrame(() => {
            // Auto-refresh notifications every 30 seconds
            notificationRefreshTimer = setInterval(loadNotifications, 30000);
            
            // Check system status every 60 seconds
            systemStatusRefreshTimer = setInterval(checkSystemStatus, 60000);
        });
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (notificationRefreshTimer) clearInterval(notificationRefreshTimer);
            if (systemStatusRefreshTimer) clearInterval(systemStatusRefreshTimer);
        });
    });

    function initializeElements() {
        searchInput = document.getElementById('global-search');
        searchResults = document.getElementById('search-results');
        notificationBell = document.getElementById('notification-bell');
        notificationDropdown = document.getElementById('notification-dropdown');
        quickActionsBtn = document.getElementById('quick-actions-toggle');
        quickActionsMenu = document.getElementById('quick-actions-menu');
        userMenuTrigger = document.getElementById('user-menu-trigger');
        userMenuDropdown = document.getElementById('user-menu-dropdown');
        userMenu = document.querySelector('.user-menu');
        currentTimeElement = document.getElementById('current-time');
        systemStatusElement = document.getElementById('system-status');
    }

    function setupEventListeners() {
        // Global search functionality
        if (searchInput) {
            searchInput.addEventListener('input', debounce(handleSearch, 300));
            searchInput.addEventListener('focus', showSearchResults);
            searchInput.addEventListener('keydown', handleSearchKeydown);
        }

        // Notification bell
        if (notificationBell) {
            notificationBell.addEventListener('click', toggleNotifications);
        }

        // Quick actions
        if (quickActionsBtn) {
            quickActionsBtn.addEventListener('click', toggleQuickActions);
        }

        // User menu
        if (userMenuTrigger) {
            userMenuTrigger.addEventListener('click', toggleUserMenu);
        }

        // Mark all notifications as read
        const markAllReadBtn = document.querySelector('.mark-all-read');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', markAllNotificationsRead);
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', handleOutsideClick);

        // Escape key to close dropdowns
        document.addEventListener('keydown', handleEscapeKey);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', handleKeyboardShortcuts);
    }

    // Real-time clock
    function startClock() {
        if (!currentTimeElement) return;
        
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const dateString = now.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            currentTimeElement.textContent = `${timeString} - ${dateString}`;
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    }

    // Enhanced dropdown animations and interactions
    function showDropdown(dropdown) {
        if (!dropdown) return;
        dropdown.classList.add('show');
    }

    function hideDropdown(dropdown) {
        if (!dropdown) return;
        dropdown.classList.remove('show');
    }

    // Enhanced search with better UX
    function handleSearch(event) {
        const query = event.target.value.trim();
        
        if (query.length < 2) {
            hideSearchResults();
            return;
        }

        if (isSearching) return;
        isSearching = true;

        showSearchLoading();
        
        // Debounced API call
        setTimeout(() => {
            performSearch(query);
        }, 150);
    }

    function showSearchLoading() {
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="search-loading">
                    <span>Đang tìm kiếm...</span>
                </div>
            `;
            searchResults.style.display = 'block';
        }
    }

    async function performSearch(query) {
        try {
            const response = await fetch(`${window.location.origin}/public/api/quick_search.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ query: query })
            });

            const data = await response.json();
            
            if (data.success) {
                displaySearchResults(data.results);
            } else {
                showSearchError(data.message || 'Có lỗi xảy ra khi tìm kiếm');
            }
        } catch (error) {
            console.error('Search error:', error);
            showSearchError('Không thể kết nối đến máy chủ');
        } finally {
            isSearching = false;
        }
    }

    function displaySearchResults(results) {
        if (!searchResults) return;

        if (results.length === 0) {
            searchResults.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <span>Không tìm thấy kết quả phù hợp</span>
                </div>
            `;
        } else {
            const resultsHTML = results.map((result, index) => `
                <a href="${result.url}" class="search-result-item" style="animation-delay: ${index * 0.05}s">
                    <div class="result-icon ${result.type}">
                        <i class="fas ${result.icon}"></i>
                    </div>
                    <div class="result-content">
                        <div class="result-title">${highlightQuery(result.title, searchInput.value)}</div>
                        <div class="result-subtitle">${result.subtitle}</div>
                    </div>
                </a>
            `).join('');
            
            searchResults.innerHTML = resultsHTML;
        }

        searchResults.style.display = 'block';
    }

    function highlightQuery(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    function showSearchError(message) {
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="search-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${message}</span>
                </div>
            `;
            searchResults.style.display = 'block';
        }
    }

    // Enhanced notifications with real-time updates
    async function loadNotifications() {
        try {
            const response = await fetch(`${window.location.origin}/public/api/notifications.php`);
            const data = await response.json();
            
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    function updateNotificationBadge(count) {
        const badge = document.getElementById('notification-count');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'block';
                badge.classList.add('pulse');
            } else {
                badge.style.display = 'none';
                badge.classList.remove('pulse');
            }
        }
    }

    function renderNotifications(notifications) {
        const notificationList = document.getElementById('notification-list');
        if (!notificationList) {
            console.error("Notification list element (#notification-list) not found.");
            return;
        }

        if (!notifications || notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <span>Không có thông báo mới</span>
                </div>
            `;
        } else {
            const notificationsHTML = notifications.map((notification, index) => {
                const itemClass = notification.unread ? 'unread' : '';
                const iconClass = notification.icon || 'fas fa-info-circle'; // Default icon
                const typeClass = notification.type || 'default'; // Default type
                const title = notification.title || 'Thông báo'; // Default title
                const message = notification.message || 'Bạn có một thông báo mới.'; // Default message
                const time = notification.time || '';
                const url = notification.url || '#';

                return `
                <div class="notification-item ${itemClass}" 
                     data-id="${notification.id}" 
                     data-url="${url}"
                     style="animation-delay: ${index * 0.05}s">
                    <div class="notification-icon ${typeClass}">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${title}</div>
                        <div class="notification-message">${message}</div>
                        <div class="notification-time">${time}</div>
                    </div>
                    ${notification.unread ? '<div class="unread-indicator"></div>' : ''}
                </div>
            `;
            }).join('');
            
            notificationList.innerHTML = notificationsHTML;
            
            notificationList.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', () => {
                    markNotificationAsRead(item.dataset.id);
                    const itemUrl = item.dataset.url;
                    if (itemUrl && itemUrl !== '#' && itemUrl !== 'undefined' && itemUrl !== 'null') {
                        window.location.href = itemUrl;
                    }
                });
            });
        }
    }

    // Enhanced system status with health monitoring
    async function checkSystemStatus() {
        try {
            const response = await fetch(`${window.location.origin}/public/api/system_status.php`);
            const data = await response.json();
            
            updateSystemStatus(data.status, data.details);
        } catch (error) {
            updateSystemStatus('offline', 'Không thể kết nối');
        }
    }

    function updateSystemStatus(status, details) {
        if (systemStatusElement) {
            const statusConfig = {
                online: {
                    class: 'online-indicator',
                    icon: 'fas fa-circle',
                    text: 'Online',
                    color: '#10b981'
                },
                warning: {
                    class: 'warning-indicator', 
                    icon: 'fas fa-exclamation-triangle',
                    text: 'Cảnh báo',
                    color: '#f59e0b'
                },
                offline: {
                    class: 'offline-indicator',
                    icon: 'fas fa-times-circle', 
                    text: 'Offline',
                    color: '#ef4444'
                }
            };

            const config = statusConfig[status] || statusConfig.offline;
            
            systemStatusElement.className = `system-status ${config.class}`;
            systemStatusElement.innerHTML = `<i class="${config.icon}"></i> ${config.text}`;
            systemStatusElement.title = details || `Hệ thống ${config.text.toLowerCase()}`;
            systemStatusElement.style.setProperty('--status-color', config.color);
        }
    }

    // Enhanced time display with better formatting
    function updateClock() {
        if (currentTimeElement) {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            const dateString = now.toLocaleDateString('vi-VN', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            currentTimeElement.innerHTML = `
                <span class="time">${timeString}</span>
                <span class="date">${dateString}</span>
            `;
        }
    }

    function startClock() {
        updateClock();
        setInterval(updateClock, 1000);
    }

    // Update the existing functions to use enhanced versions
    function toggleNotifications() {
        const dropdown = notificationDropdown;
        if (dropdown.style.display === 'block' || dropdown.classList.contains('show')) {
            hideDropdown(dropdown);
        } else {
            hideAllDropdowns();
            showDropdown(dropdown);
        }
    }

    function toggleQuickActions() {
        const dropdown = quickActionsMenu;
        if (dropdown.style.display === 'block' || dropdown.classList.contains('show')) {
            hideDropdown(dropdown);
        } else {
            hideAllDropdowns();
            showDropdown(dropdown);
        }
    }

    function toggleUserMenu() {
        const dropdown = userMenuDropdown;
        if (dropdown.style.display === 'block' || dropdown.classList.contains('show')) {
            hideDropdown(dropdown);
            userMenu.classList.remove('open');
        } else {
            hideAllDropdowns();
            showDropdown(dropdown);
            userMenu.classList.add('open');
        }
    }

    function hideAllDropdowns() {
        [notificationDropdown, quickActionsMenu, userMenuDropdown, searchResults].forEach(dropdown => {
            if (dropdown) {
                hideDropdown(dropdown);
            }
        });
        
        if (userMenu) {
            userMenu.classList.remove('open');
        }
    }

    // Enhanced keyboard shortcuts
    function handleKeyboardShortcuts(event) {
        // Global search shortcut (Ctrl+K)
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Quick actions shortcut (Ctrl+Shift+F)
        if (event.ctrlKey && event.shiftKey && event.key === 'F') {
            event.preventDefault();
            toggleQuickActions();
        }
        
        // Notifications shortcut (Ctrl+Shift+H)
        if (event.ctrlKey && event.shiftKey && event.key === 'H') {
            event.preventDefault();
            toggleNotifications();
        }
        
        // Escape to close dropdowns
        if (event.key === 'Escape') {
            hideAllDropdowns();
            searchInput?.blur();
        }
    }

    // Enhanced tooltip positioning for system-status
    function setupSystemStatusTooltip() {
        if (systemStatusElement) {
            // Mark element as having JS tooltip
            systemStatusElement.setAttribute('data-js-tooltip', 'true');
            
            // Create custom tooltip elements
            const tooltip = document.createElement('div');
            tooltip.className = 'system-status-tooltip';
            tooltip.style.cssText = `
                position: absolute;
                background: #1f2937;
                color: white;
                padding: 0.5rem 0.75rem;
                border-radius: 0.5rem;
                font-size: 0.75rem;
                white-space: nowrap;
                z-index: 10002;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transform: translateX(-50%);
            `;
            
            const arrow = document.createElement('div');
            arrow.className = 'system-status-tooltip-arrow';
            arrow.style.cssText = `
                position: absolute;
                top: -0.25rem;
                left: 50%;
                transform: translateX(-50%);
                border: 0.25rem solid transparent;
                border-bottom-color: #1f2937;
                z-index: 10002;
            `;
            
            tooltip.appendChild(arrow);
            document.body.appendChild(tooltip);
            
            let tooltipTimeout;
            
            systemStatusElement.addEventListener('mouseenter', () => {
                clearTimeout(tooltipTimeout);
                
                const rect = systemStatusElement.getBoundingClientRect();
                const tooltipText = systemStatusElement.getAttribute('title') || 
                                   systemStatusElement.getAttribute('data-original-title') || 
                                   'Hệ thống đang hoạt động';
                
                tooltip.textContent = tooltipText;
                tooltip.style.left = (rect.left + rect.width / 2) + 'px';
                tooltip.style.top = (rect.bottom + 8) + 'px';
                tooltip.style.opacity = '1';
                
                // Remove title attribute to prevent default tooltip
                if (systemStatusElement.hasAttribute('title')) {
                    systemStatusElement.setAttribute('data-original-title', tooltipText);
                    systemStatusElement.removeAttribute('title');
                }
            });
            
            systemStatusElement.addEventListener('mouseleave', () => {
                tooltipTimeout = setTimeout(() => {
                    tooltip.style.opacity = '0';
                }, 100);
            });
        }
    }

    // Helper functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    async function markNotificationAsRead(notificationId) {
        try {
            await fetch(`${window.location.origin}/public/api/notifications.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_read',
                    notification_id: notificationId
                })
            });
            
            // Refresh notifications
            loadNotifications();
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async function markAllNotificationsRead() {
        try {
            await fetch(`${window.location.origin}/public/api/notifications.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_all_read'
                })
            });
            
            // Refresh notifications
            loadNotifications();
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }

    function showSearchResults() {
        if (searchInput && searchInput.value.trim().length >= 2) {
            if (searchResults) {
                searchResults.style.display = 'block';
            }
        }
    }

    function hideSearchResults() {
        if (searchResults) {
            searchResults.style.display = 'none';
        }
    }

    function handleSearchKeydown(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const firstResult = searchResults?.querySelector('.search-result-item');
            if (firstResult) {
                firstResult.click();
            }
        }
    }

    function handleOutsideClick(event) {
        // Close dropdowns when clicking outside
        if (!event.target.closest('.notification-bell') && 
            !event.target.closest('.notification-dropdown')) {
            hideDropdown(notificationDropdown);
        }
        
        if (!event.target.closest('.quick-actions') && 
            !event.target.closest('.quick-actions-menu')) {
            hideDropdown(quickActionsMenu);
        }
        
        if (!event.target.closest('.user-menu') && 
            !event.target.closest('.user-menu-dropdown')) {
            hideDropdown(userMenuDropdown);
            userMenu?.classList.remove('open');
        }
        
        if (!event.target.closest('.quick-search') && 
            !event.target.closest('.search-results')) {
            hideSearchResults();
        }
    }

    function handleEscapeKey(event) {
        if (event.key === 'Escape') {
            hideAllDropdowns();
        }
    }

    // Add the missing closeAllDropdowns function
    function closeAllDropdowns() {
        hideAllDropdowns();
    }

    // Expose functions for external use
    window.ContentHeader = {
        loadNotifications,
        updateNotificationBadge,
        closeAllDropdowns,
        hideAllDropdowns
    };

    // Global fallback functions to prevent errors
    window.closeAllDropdowns = closeAllDropdowns;
    window.hideAllDropdowns = hideAllDropdowns;

})(window, document);
