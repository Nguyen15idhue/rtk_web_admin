(function($){
    // ...existing code/comments...
    const base = window.basePath;
    const apiUrl = base + '/public/actions/guide/index.php';

    function loadData(q='') {
        fetch(`${apiUrl}?action=fetch&search=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(env => {
                if (env.success) {
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
                            </td>
                        </tr>`).join('');
                    $('#tbl-guides tbody').html(rows);
                } else {
                    window.showToast(env.message || 'Lỗi tải danh sách hướng dẫn','error');
                }
            })
            .catch(err => {
                window.showToast('Lỗi tải danh sách hướng dẫn: ' + err,'error');
            });
    }

    $(function(){
        const $search = $('input[name=search]');
        loadData($search.val());
        $search.on('input', ()=> loadData($search.val()));

        $(document).on('click', '.btn-edit', function(){
            window.location.href = 'edit_guide.php?id=' + $(this).data('id');
        });

        $(document).on('click', '.btn-toggle', function(){
            let id = $(this).data('id'),
                st = $(this).data('status');
            fetch(`${apiUrl}?action=toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id, status: st })
            })
            .then(res => res.json())
            .then(env => {
                if (env.success) loadData($search.val());
                else window.showToast(env.message || 'Toggle thất bại', 'error');
            })
            .catch(err => {
                window.showToast('Lỗi chuyển trạng thái: ' + err, 'error');
            });
        });
    });
})(jQuery);
