<!-- Generic Modal -->
<div id="genericModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="genericModalTitle">Tiêu đề Modal</h4>
            <span class="modal-close" onclick="helpers.closeModal('genericModal')">&times;</span>
        </div>
        <div class="modal-body" id="genericModalBody">
            <!-- Nội dung modal sẽ được tải vào đây -->
            <p>Đang tải...</p>
        </div>
        <div class="modal-footer" id="genericModalFooter" style="display: flex; justify-content: flex-end;">
            <button type="button" class="btn btn-secondary" onclick="helpers.closeModal('genericModal')" style="margin-right: 8px;">Đóng</button>
            <button type="button" class="btn btn-primary" id="genericModalPrimaryButton">Lưu</button>
        </div>
    </div>
</div>
