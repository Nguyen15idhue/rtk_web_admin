(function($){
    const apiUrl = basePath + '/public/handlers/guide/index.php';
    const canEditGuide = window.appConfig && window.appConfig.permissions && window.appConfig.permissions.guide_management_edit;

    function loadData(q='') {
        api.getJson(`${apiUrl}?action=fetch_guides&search=${encodeURIComponent(q)}`)
            .then(env => {
                if (!env.success) throw new Error(env.message || 'Lỗi tải danh sách hướng dẫn');
                const list = env.data;
                let rows = list.map(g => `
                    <tr>
                        <td>${g.id}</td>
                        <td>${g.title}</td>
                        <td>${g.author_name || '<em>Chưa rõ</em>'}</td>
                        <td class="status" style="text-align:center">
                            ${g.status==='published'
                                ? '<span class="status-badge badge-success">Đã xuất bản</span>'
                                : '<span class="status-badge badge-secondary">Bản nháp</span>'}
                        </td>
                        <td class="actions">
                            ${canEditGuide ? `
                            <button class="btn-icon btn-edit" data-id="${g.id}" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                class="btn-icon btn-toggle ${g.status==='published'?'btn-success':'btn-secondary'}"
                                data-id="${g.id}"
                                data-status="${g.status==='published'?'draft':'published'}"
                                title="${g.status==='published'?'Chuyển sang Nháp':'Xuất bản'}">
                                <i class="fas fa-toggle-${g.status==='published'?'on':'off'}"></i>
                            </button>
                            ` : 'Không có quyền'}
                        </td>
                    </tr>`).join('');
                $('#tbl-guides tbody').html(rows);
            })
            .catch(err => window.showToast(err.message, 'error'));
    }

    $(function(){
        const $search = $('input[name=search]');
        loadData($search.val());
        $search.on('input', ()=> loadData($search.val()));

        $(document).on('click', '.btn-edit', function(){
            if (!canEditGuide) return;
            window.location.href = 'edit_guide.php?id=' + $(this).data('id');
        });

        $(document).on('click', '.btn-toggle', function(){
            if (!canEditGuide) return;
            const id = $(this).data('id'), st = $(this).data('status');
            api.postJson(`${apiUrl}?action=toggle_guide_status`, { id, status: st })
                .then(env => {
                    if (!env.success) throw new Error(env.message || 'Toggle thất bại');
                    loadData($search.val());
                })
                .catch(err => window.showToast(err.message, 'error'));
        });
    });
})(jQuery);
