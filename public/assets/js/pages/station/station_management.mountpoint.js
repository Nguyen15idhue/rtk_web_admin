// Mountpoint IP/Port inline edit logic
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        if (!(window.appConfig && window.appConfig.permissions.station_management_edit)) return;

        // Helper: debounce
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Track last value to avoid duplicate requests
        const lastValues = {};

        // Save IP/Port AJAX
        function saveMountpointIpPort(mountpointId, ip, port, btn, inputType) {
            if (!mountpointId) return;
            const key = mountpointId + '_' + (inputType || 'all');
            const newVal = (inputType === 'ip') ? ip : port;
            if (lastValues[key] === newVal) return; // Only send if changed
            lastValues[key] = newVal;
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Đang lưu...';
            }
            fetch(window.basePath + '/public/handlers/station/mountpoint_index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'update_mountpoint_ip_port',
                    mountpoint_id: mountpointId,
                    ip: ip,
                    port: port
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.innerText = 'Lưu';
                    btn.disabled = false;
                }
                if (data.success && data.message !== 'Không có thay đổi.') {
                    showToast(data.message || 'Cập nhật thành công!', 'success');
                } else if (!data.success) {
                    showToast(data.message || 'Có lỗi khi cập nhật!', 'error');
                }
                // Nếu message là 'Không có thay đổi.' thì không hiện toast gì cả
            })
            .catch(() => {
                if (btn) {
                    btn.innerText = 'Lưu';
                    btn.disabled = false;
                }
                showToast('Lỗi mạng hoặc máy chủ!', 'error');
            });
        }

        // Show toast
        function showToast(message, type) {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
            } else if (window.helpers && typeof window.helpers.showToast === 'function') {
                window.helpers.showToast(type, message);
            } else {
                alert(message);
            }
        }

        // Save on button click (IP)
        document.querySelectorAll('.save-mountpoint-ip-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var form = btn.closest('.updateMountpointIpPortForm');
                var mountpointId = form.getAttribute('data-mountpoint-id');
                var ip = form.querySelector('input[name="ip"]').value.trim();
                var portInput = form.parentElement.parentElement.querySelector('input[name="port"]');
                var port = portInput ? portInput.value.trim() : undefined;
                saveMountpointIpPort(mountpointId, ip, port, btn, 'ip');
            });
        });
        // Save on button click (Port)
        document.querySelectorAll('.save-mountpoint-port-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var form = btn.closest('.updateMountpointIpPortForm');
                var mountpointId = form.getAttribute('data-mountpoint-id');
                var port = form.querySelector('input[name="port"]').value.trim();
                var ipInput = form.parentElement.parentElement.querySelector('input[name="ip"]');
                var ip = ipInput ? ipInput.value.trim() : undefined;
                saveMountpointIpPort(mountpointId, ip, port, btn, 'port');
            });
        });

        // Auto save on blur (IP)
        document.querySelectorAll('input.mountpoint-ip-input').forEach(function(input) {
            let last = input.value;
            input.addEventListener('blur', debounce(function() {
                var form = input.closest('.updateMountpointIpPortForm');
                var mountpointId = form.getAttribute('data-mountpoint-id');
                var ip = input.value.trim();
                var portInput = form.parentElement.parentElement.querySelector('input[name="port"]');
                var port = portInput ? portInput.value.trim() : undefined;
                if (ip !== last) {
                    saveMountpointIpPort(mountpointId, ip, port, null, 'ip');
                    last = ip;
                }
            }, 300));
        });
        // Auto save on blur (Port)
        document.querySelectorAll('input.mountpoint-port-input').forEach(function(input) {
            let last = input.value;
            input.addEventListener('blur', debounce(function() {
                var form = input.closest('.updateMountpointIpPortForm');
                var mountpointId = form.getAttribute('data-mountpoint-id');
                var port = input.value.trim();
                var ipInput = form.parentElement.parentElement.querySelector('input[name="ip"]');
                var ip = ipInput ? ipInput.value.trim() : undefined;
                if (port !== last) {
                    saveMountpointIpPort(mountpointId, ip, port, null, 'port');
                    last = port;
                }
            }, 300));
        });
    });
})();
