(function($) {
    'use strict';
    $(function() {
        let currentLevel = 'all';
        const baseUrl = '?ajax=1';
        // Cache selectors
        const $errorCount = $('#error-count'),
              $warningCount = $('#warning-count'),
              $infoCount = $('#info-count'),
              $debugCount = $('#debug-count'),
              $logsBody = $('#logs-table-body'),
              $refreshBtn = $('#refresh-logs');

        init();

        function init() {
            loadStats();
            loadLogs();
            startAutoRefresh();
        }

        // Auto refresh control ensuring sequential calls
        function startAutoRefresh() {
            setTimeout(function tick() {
                loadStats();
                loadLogs();
                setTimeout(tick, 30000);
            }, 30000);
        }

        // Delegate filter and action events
        $(document)
            .on('click', '.log-filter', onFilterClick)
            .on('click', '.clear-logs', onClearClick)
            .on('click', '#export-logs', onExportClick)
            .on('click', '#refresh-logs', function() {
                loadStats();
                loadLogs();
            });

        function onFilterClick() {
            $('.log-filter').removeClass('active');
            $(this).addClass('active');
            currentLevel = $(this).data('level');
            loadLogs();
        }

        function onClearClick(e) {
            e.preventDefault();
            const level = $(this).data('level');
            const $btn = $(this);
            const orig = $btn.html();
            if (!confirm(`Bạn có chắc chắn muốn xóa nhật ký ${level} hôm nay?`)) return;
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            $.get(`${baseUrl}&action=clear_logs&level=${level}`)
             .always(function() { $btn.prop('disabled', false).html(orig); })
             .done(function(res) {
                 // Sử dụng hệ thống toast tự build thay vì toastr
                 if (typeof window.showToast === 'function') {
                     window.showToast(res.message, res.success ? 'success' : 'error');
                 }
                 loadStats(); loadLogs();
             })
             .fail(function() { 
                 if (typeof window.showToast === 'function') {
                     window.showToast('Không thể xóa nhật ký', 'error');
                 }
             });
        }

        function onExportClick(e) {
            e.preventDefault();
            const $btn = $(this);
            const orig = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            const lvlParam = currentLevel === 'all' ? '' : `&level=${currentLevel}`;
            window.open(`${baseUrl}&action=export_logs${lvlParam}`, '_blank');
            setTimeout(() => $btn.prop('disabled', false).html(orig), 1000);
        }

        function loadStats() {
            $.get(`${baseUrl}&action=get_stats`)
                .done(function(res) {
                    if (res.success) {
                        $errorCount.text(res.stats.error);
                        $warningCount.text(res.stats.warning);
                        $infoCount.text(res.stats.info);
                        $debugCount.text(res.stats.debug);
                    }
                });
        }

        function loadLogs() {
            const lvlParam = currentLevel === 'all' ? '' : `&level=${currentLevel}`;
            $.get(`${baseUrl}&action=get_logs${lvlParam}&limit=100`)
                .done(function(res) {
                    if (res.success) displayLogs(res.logs);
                    else showError('<span>Không thể tải nhật ký</span>');
                })
                .fail(function() { showError('<span>Không thể tải nhật ký. Vui lòng thử lại.</span>'); });
        }

        function showError(msgHtml) {
            $logsBody.html(`<tr><td colspan="3" class="text-center text-danger"><div class="error-state"><i class="fas fa-exclamation-triangle"></i>${msgHtml}</div></td></tr>`);
        }

        function displayLogs(logs) {
            logs.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            if (!logs.length) {
                $logsBody.html(
                    `<tr><td colspan="3" class="empty-state"><i class="fas fa-file-alt"></i><h5>Không tìm thấy nhật ký</h5><p>Không có nhật ký nào phù hợp với tiêu chí lọc hiện tại.</p></td></tr>`
                );
                return;
            }
            let html = '';
            logs.forEach(log => {
                const lvl = log.level.toLowerCase();
                const time = new Date(log.timestamp);
                html +=
                    `<tr class="log-entry">` +
                    `<td class="log-timestamp"><div class="text-nowrap"><strong>${time.toLocaleTimeString('vi-VN')}</strong><br><small class="text-muted">${time.toLocaleDateString('vi-VN')}</small></div></td>` +
                    `<td><span class="badge log-level-badge log-level-${lvl}"><i class="${getLevelIcon(lvl)}"></i> ${log.level}</span></td>` +
                    `<td class="log-message">${escapeHtml(log.message)}</td></tr>`;
            });
            $logsBody.html(html);
            updateLastUpdated();
        }

        function getLevelIcon(level) {
            const icons = { error: 'fas fa-exclamation-triangle', warning: 'fas fa-exclamation', info: 'fas fa-info-circle', debug: 'fas fa-code' };
            return icons[level] || 'fas fa-circle';
        }

        function updateLastUpdated() {
            $('#last-updated').text(new Date().toLocaleTimeString('vi-VN'));
        }

        function escapeHtml(text) {
            return $('<div/>').text(text).html();
        }
    });
})(jQuery);
