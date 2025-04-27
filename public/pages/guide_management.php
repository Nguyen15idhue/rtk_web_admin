<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}
// --- Base Path & Includes ---
$protocol = (!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'||$_SERVER['SERVER_PORT']==443)?"https://":"http://";
$host = $_SERVER['HTTP_HOST'];
$parts = explode('/', $_SERVER['SCRIPT_NAME']);
$idx = array_search('rtk_web_admin',$parts);
$base_seg = $idx!==false? implode('/',array_slice($parts,0,$idx+1)).'/':'/';
$base_path = $protocol.$host.$base_seg;
$private_includes = __DIR__ . '/../../private/includes/';
$page_title = 'Quản lý Guide';
include $private_includes . 'admin_header.php';
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">

<style>
/* Modal overlay */
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}
/* Modal box */
.modal-content {
    background: #fff;
    margin: 5% auto;
    padding: 20px;
    border-radius: 4px;
    width: 80%;
    max-width: 600px;
}
</style>
<?php include $private_includes . 'admin_sidebar.php'; ?>

<main class="content-wrapper">
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <!-- ...optionally user info here... -->
    </div>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách Guide</h3>
            <button class="btn btn-primary" onclick="openCreateGuideModal()">
                <i class="fas fa-plus"></i> Thêm mới
            </button>
        </div>
        <form method="GET" class="filter-bar">
            <input type="search" name="search" placeholder="Tìm tiêu đề/topic" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            <a href="<?php echo strtok($_SERVER['REQUEST_URI'],'?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa</a>
        </form>

        <div class="transactions-table-wrapper">
            <table class="transactions-table" id="tbl-guides">
                <thead>
                    <tr>
                        <th>ID</th><th>Tiêu đề</th><th>Tác giả</th><th>Trạng thái</th><th class="actions">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- rows populated by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal form tạo/sửa -->
    <div id="modal-form" class="modal">
        <div class="modal-content">
            <form id="frm-guide">
                <div class="modal-header">
                    <h4 id="modal-title">Thêm/Sửa Guide</h4>
                    <span class="modal-close" onclick="closeModal('modal-form')">&times;</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <label for="guideTitle">Tiêu đề</label>
                        <input type="text" id="guideTitle" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="guideSlug">Slug</label>
                        <input type="text" id="guideSlug" name="slug" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="guideTopic">Topic</label>
                        <input type="text" id="guideTopic" name="topic" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="guideStatus">Status</label>
                        <select id="guideStatus" name="status" class="form-control">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="guideThumbnail">Thumbnail URL</label>
                        <input type="text" id="guideThumbnail" name="thumbnail" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="guideImage">Image URL</label>
                        <input type="text" id="guideImage" name="image" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="guideContent">Nội dung</label>
                        <textarea id="guideContent" name="content" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-form')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const basePath = '<?php echo rtrim($base_path,'/'); ?>';
function loadData(q='') {
    $.getJSON(basePath + '/private/actions/guide/fetch_guides.php', {search: q}, function(data){
        let rows = data.map(g => `<tr>
            <td>${g.id}</td>
            <td>${g.title}</td>
            <td>${g.author_name}</td>
            <td>${g.status}</td>
            <td class="actions">
                <button class="btn-icon btn-edit" title="Sửa" data-id="${g.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon btn-toggle" title="${g.status==='published'?'Chuyển sang Draft':'Xuất bản'}"
                        data-id="${g.id}" data-status="${g.status==='published'?'draft':'published'}">
                    <i class="fas fa-toggle-${g.status==='published'?'on':'off'}"></i>
                </button>
            </td>
        </tr>`).join('');
        $('#tbl-guides tbody').html(rows);
    });
}
function openCreateGuideModal(){
    $('#modal-title').text('Thêm mới Guide');
    $('#frm-guide')[0].reset();
    $('input[name=id]').val(''); // đảm bảo clear id cũ
    document.getElementById('modal-form').style.display = 'block';
}
function closeModal(modalId='modal-form'){
    document.getElementById(modalId).style.display = 'none';
}
$(function(){
    loadData($('input[name=search]').val());
    $('input[name=search]').on('input', ()=> loadData($('input[name=search]').val()));

    $(document).on('click','.btn-edit',function(){
        $('#modal-title').text('Sửa Guide');
        $.getJSON(basePath + '/private/actions/guide/get_guide_details.php',{id:$(this).data('id')},function(d){
            for(let k in d) $(`[name=${k}]`).val(d[k]);
            document.getElementById('modal-form').style.display = 'block';
        });
    });

    $('#frm-guide').submit(function(e){
        e.preventDefault();
        let url = $('input[name=id]').val() ? 'update_guide.php' : 'create_guide.php';
        $.post(basePath + '/private/actions/guide/'+url, $(this).serialize(), function(res){
            if(res.success){ $('#modal-form').hide(); loadData(); }
        },'json');
    });

    $(document).on('click','.btn-toggle',function(){
        let id=$(this).data('id'), st=$(this).data('status');
        $.post(basePath + '/private/actions/guide/toggle_guide_status.php',{id, status:st},function(r){
            if(r.success) loadData($('input[name=search]').val());
        },'json');
    });

    // đóng modal khi click ngoài
    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-form')) {
            closeModal('modal-form');
        }
    };
});
</script>

<?php include $private_includes . 'admin_footer.php'; ?>
