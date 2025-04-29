(function($){
    // ...existing code/comments...
    const base = window.basePath;
    const apiUrl = base + '/public/actions/guide/index.php';

    function loadData(q='') {
        $.getJSON(apiUrl, { action: 'fetch', search: q }, function(data){
            let rows = data.map(g => `
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
            $.post(apiUrl + '?action=toggle',
                   { id: id, status: st },
                   function(r){ if(r.success) loadData($search.val()); },
                   'json');
        });
    });
})(jQuery);
