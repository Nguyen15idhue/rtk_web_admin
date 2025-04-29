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
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/layouts/header.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-buttons.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-badges.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/badges.css">
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
            <button class="btn btn-primary" onclick="window.location.href='edit_guide.php'">
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
                        <th>ID</th><th>Tiêu đề</th><th>Tác giả</th><th class="status" style="text-align:center">Trạng thái</th><th class="actions" style="text-align:center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- rows populated by JS -->
                </tbody>
            </table>
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
            <td>${g.author_name 
                    ? g.author_name 
                    : '<em>Chưa rõ</em>'}</td>
            <td class="status" style="text-align:center">
                ${g.status==='published'
                    ? '<span class="status-badge badge-success">Đã xuất bản</span>'
                    : '<span class="status-badge badge-secondary">Bản nháp</span>'}
            </td>
            <td class="actions">
                <button class="btn-icon btn-edit" title="Sửa" data-id="${g.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon btn-toggle ${g.status==='published'?'btn-success':'btn-secondary'}"
                        title="${g.status==='published'?'Chuyển sang Nháp':'Xuất bản'}"
                        data-id="${g.id}" data-status="${g.status==='published'?'draft':'published'}">
                    <i class="fas fa-toggle-${g.status==='published'?'on':'off'}"></i>
                </button>
            </td>
        </tr>`).join('');
        $('#tbl-guides tbody').html(rows);
    });
}

$(function(){
    loadData($('input[name=search]').val());
    $('input[name=search]').on('input', ()=> loadData($('input[name=search]').val()));

    // Chuyển sang trang edit_guide.php
    $(document).on('click','.btn-edit',function(){
        window.location.href = 'edit_guide.php?id=' + $(this).data('id');
    });

    // giữ nguyên toggle status
    $(document).on('click','.btn-toggle',function(){
        let id=$(this).data('id'), st=$(this).data('status');
        $.post(basePath + '/private/actions/guide/toggle_guide_status.php',{id, status:st},function(r){
            if(r.success) loadData($('input[name=search]').val());
        },'json');
    });
});
</script>

<?php include $private_includes . 'admin_footer.php'; ?>
