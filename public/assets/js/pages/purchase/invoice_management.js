(function(window){
    document.addEventListener('DOMContentLoaded', function(){
        const appBase = (window.appConfig && window.appConfig.basePath) ? window.appConfig.basePath : '';
        const canEditInvoice = (window.appConfig && window.appConfig.permissions && window.appConfig.permissions.invoice_management_edit);

        // modal elements
        const proofModal = document.getElementById('proofModal');
        const proofModalImage = document.getElementById('proofModalImage');
        const proofModalTitle = document.getElementById('proofModalTitle');
        const detailsModal = document.getElementById('transaction-details-modal');

        // Functions to be exposed
        function viewProofModal(id, url){
            if(!url){ window.showToast("Không có hình ảnh minh chứng.", 'warning'); return; }
            proofModalTitle.textContent = `Minh chứng Giao dịch #${id}`;
            proofModalImage.src = url;
            proofModal.classList.add('active');
        };
        function closeProofModal(){
            proofModal.classList.remove('active');
        };

        function showTransactionDetails(data){
            if(!detailsModal||!data) return;
            document.getElementById('modal-title').textContent = `Chi Tiết Giao Dịch #${data.id}`;
            document.getElementById('modal-tx-id').textContent = data.id;
            document.getElementById('modal-tx-email').textContent = data.email;
            document.getElementById('modal-tx-package').textContent = data.package_name;
            // voucher information
            const voucherEl = document.getElementById('modal-tx-voucher-code');
            if (data.voucher_code) {
                let detailsText = "";
                if (data.voucher_type === 'percentage_discount') {
                    detailsText = `Giảm ${data.discount_value}%`;
                } else if (data.voucher_type === 'fixed_discount') {
                    // Assuming data.discount_value is a pre-formatted string or simple number based on original behavior
                    detailsText = `Giảm ${data.discount_value}`;
                } else if (data.voucher_type === 'extend_duration') {
                    detailsText = `Tặng ${data.discount_value} tháng`;
                }
                
                if (detailsText) {
                    voucherEl.textContent = `${data.voucher_code} (${detailsText})`;
                } else {
                    // Fallback for unknown types or if no specific detail text is generated
                    voucherEl.textContent = data.voucher_code;
                }
            } else {
                voucherEl.textContent = 'Không có';
            }
            // populate other voucher details
            document.getElementById('modal-tx-voucher-description').textContent = data.voucher_description || '-';
            document.getElementById('modal-tx-voucher-start-date').textContent = data.voucher_start_date || '-';
            document.getElementById('modal-tx-voucher-end-date').textContent = data.voucher_end_date || '-';
            document.getElementById('modal-tx-amount').textContent = data.amount;
            document.getElementById('modal-tx-request-date').textContent = data.request_date;
            const badge = document.getElementById('modal-tx-status-badge');
            badge.className = 'status-badge status-badge-modal ' + data.status_class;
            document.getElementById('modal-tx-status-text').textContent = data.status_text;

            // proof link
            const proofLink = document.getElementById('modal-tx-proof-link');
            if(data.proof_image){
                proofLink.innerHTML = `<a href="${data.proof_image}" target="_blank">Xem hình ảnh&nbsp;</a>
                    | <button class="btn-link" onclick="PurchaseManagementPageEvents.viewProofModal('${data.id}','${data.proof_image}'); PurchaseManagementPageEvents.closeDetailsModal();">Xem trong modal</button>`;
            } else proofLink.textContent = 'Không có';

            // rejection reason
            const rejCont = document.getElementById('modal-tx-rejection-reason-container');
            if(data.rejection_reason){
                document.getElementById('modal-tx-rejection-reason').textContent = data.rejection_reason;
                rejCont.style.display = 'flex';
            } else rejCont.style.display = 'none';

            detailsModal.classList.add('active');
        };
        function closeDetailsModal(){
            detailsModal.classList.remove('active');
        };

        // outside-click & ESC
        window.addEventListener('click', function(e){
            if(e.target===proofModal) closeProofModal();
            if(e.target===detailsModal) closeDetailsModal();
        });
        document.addEventListener('keydown', function(e){
            if(e.key==='Escape'){
                if(proofModal.classList.contains('active')) closeProofModal();
                if(detailsModal.classList.contains('active')) closeDetailsModal();
            }
        });

        // API URLs
        const apiBasePath = `${appBase}public/handlers/purchase/index.php`;
        const approveUrl = `${apiBasePath}?action=process_transaction_approve`;
        const rejectUrl  = `${apiBasePath}?action=process_transaction_reject`;
        const revertUrl  = `${apiBasePath}?action=process_transaction_revert`;

        // helpers to disable/enable buttons
        function disableActionButtons(row){
            row.querySelectorAll('.action-buttons button').forEach(b=>b.disabled=true);
        }
        function enableActionButtons(row, status){
            const ok = (s)=>!(s!=='pending' && s!=='rejected');
            const ap = row.querySelector('.btn-approve');
            const rj = row.querySelector('.btn-reject');
            const rv = row.querySelector('.btn-revert');
            if(ap) ap.disabled = !(status==='pending'||status==='rejected');
            if(rj) rj.disabled = !(status==='pending'||status==='active');
            if(rv) rv.disabled = !(status==='active');
        }

        // update row UI
        function updateTableRowStatus(id,newStat,text,cls){
            const row = document.querySelector(`tr[data-transaction-id="${id}"]`);
            if(!row) return;
            row.dataset.status = newStat;
            const badge = row.querySelector('.status-badge');
            badge.className = `status-badge ${cls}`;
            badge.textContent = text;
            const cell = row.querySelector('.actions .action-buttons');
            let html='';

            if (canEditInvoice) {
                if(newStat==='pending'){
                    html=`<button class="btn-icon btn-approve" onclick="PurchaseManagementPageEvents.approveTransaction('${id}',this)"><i class="fas fa-check-circle"></i></button>
                          <button class="btn-icon btn-reject" onclick="PurchaseManagementPageEvents.openRejectTransactionModal('${id}')"><i class="fas fa-times-circle"></i></button>
                          <button class="btn-icon btn-disabled" disabled><i class="fas fa-undo-alt"></i></button>`;
                } else if(newStat==='active'){
                    html=`<button class="btn-icon btn-disabled" disabled><i class="fas fa-check-circle"></i></button>
                          <button class="btn-icon btn-reject" onclick="PurchaseManagementPageEvents.openRejectTransactionModal('${id}')"><i class="fas fa-times-circle"></i></button>
                          <button class="btn-icon btn-revert" onclick="PurchaseManagementPageEvents.revertTransaction('${id}',this)"><i class="fas fa-undo-alt"></i></button>`;
                } else if(newStat==='rejected'){
                    html=`<button class="btn-icon btn-approve" onclick="PurchaseManagementPageEvents.approveTransaction('${id}',this)"><i class="fas fa-check-circle"></i></button>
                          <button class="btn-icon btn-disabled" disabled><i class="fas fa-times-circle"></i></button>
                          <button class="btn-icon btn-disabled" disabled><i class="fas fa-undo-alt"></i></button>`;
                } else {
                    html = '<span class="text-muted">Không có hành động</span>';
                }
            } else {
                html = '<span class="text-muted">Không có quyền</span>';
            }
            cell.innerHTML = html;
        }

        // transaction actions
        async function approveTransaction(id,btn){
            if (!canEditInvoice) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            if(!confirm(`Bạn có chắc muốn duyệt #${id}?`)) return;
            const row = document.querySelector(`tr[data-transaction-id="${id}"]`);
            try {
                const data = await api.postJson(approveUrl, { transaction_id: id });
                let msg = data.message || `Duyệt #${id} thành công!`;
                if (Array.isArray(data.accounts) && data.accounts.length) {
                    msg += '\n\nThông tin tài khoản đã tạo:';
                    data.accounts.forEach(acc=>{
                        msg += `\nUsername: ${acc.username_acc}\nPassword: ${acc.password_acc}`;
                    });
                }
                window.showToast(msg, 'success');
                if (row) {
                    updateTableRowStatus(id,'active','Đã duyệt','status-approved');
                }
            } catch(e){
                window.showToast('Lỗi duyệt giao dịch: ' + e.message, 'error');
            }
        };

        async function openRejectTransactionModal(id){
            if (!canEditInvoice) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            const reason = prompt(`Lý do từ chối #${id}:`);
            if(reason==null) return;
            const text = reason.trim(); if(!text){ window.showToast('Nhập lý do.', 'warning'); return; }
            const row = document.querySelector(`tr[data-transaction-id="${id}"]`);
            disableActionButtons(row);
            try {
                await api.postJson(rejectUrl, { transaction_id: id, reason: text });
                window.showToast('Từ chối thành công!', 'success');
                updateTableRowStatus(id,'rejected','Bị từ chối','status-rejected');
            } catch(e){
                window.showToast('Lỗi từ chối giao dịch: ' + e.message, 'error');
                enableActionButtons(row,row.dataset.status);
            }
        };

        async function revertTransaction(id,btn){
            if (!canEditInvoice) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            if(!confirm(`Hủy duyệt #${id}?`)) return;
            const row = btn.closest('tr'); disableActionButtons(row);
            try {
                await api.postJson(revertUrl, { transaction_id: id });
                window.showToast('Hủy duyệt thành công.', 'success');
                updateTableRowStatus(id,'pending','Chờ duyệt','status-pending');
            } catch(e){
                window.showToast('Lỗi hoàn tác giao dịch: ' + e.message, 'error');
                enableActionButtons(row,row.dataset.status);
            }
        };

        // select all checkbox
        const selAll = document.getElementById('selectAllTx');
        if(selAll){
            selAll.addEventListener('change', e=>{
                document.querySelectorAll('.tx-checkbox').forEach(cb=>cb.checked = e.target.checked);
            });
        }

        // helper to get selected IDs
        function getSelectedTxIds(){
            return Array.from(document.querySelectorAll('.tx-checkbox:checked'))
                        .map(cb=>cb.value);
        }

        // bulk approve
        async function bulkApproveTransactions(){
            if (!canEditInvoice) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            const ids = getSelectedTxIds();
            if(!ids.length){ window.showToast('Chọn ít nhất một giao dịch.', 'warning'); return; }
            if(!confirm(`Duyệt ${ids.length} giao dịch đã chọn?`)) return;
            try {
                for(const id of ids){
                    await api.postJson(approveUrl, { transaction_id: id });
                }
                window.showToast('Đã duyệt tất cả giao dịch được chọn.', 'success');
                window.location.reload();
            } catch(e){
                window.showToast('Lỗi khi duyệt: ' + e.message, 'error');
            }
        }

        // bulk revert
        async function bulkRevertTransactions(){
            if (!canEditInvoice) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            const ids = getSelectedTxIds();
            if(!ids.length){ window.showToast('Chọn ít nhất một giao dịch.', 'warning'); return; }
            if(!confirm(`Hủy duyệt ${ids.length} giao dịch đã chọn?`)) return;
            try {
                for(const id of ids){
                    await api.postJson(revertUrl, { transaction_id: id });
                }
                window.showToast('Đã hủy duyệt tất cả giao dịch được chọn.', 'success');
                window.location.reload();
            } catch(e){
                window.showToast('Lỗi khi hủy duyệt: ' + e.message, 'error');
            }
        }

        // bulk reject
        async function bulkRejectTransactions(){
            if (!canEditInvoice) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            const ids = getSelectedTxIds();
            if(!ids.length){ window.showToast('Chọn ít nhất một giao dịch.', 'warning'); return; }
            const reason = prompt(`Lý do từ chối cho ${ids.length} giao dịch:`) || '';
            const txt = reason.trim();
            if(!txt){ window.showToast('Lý do không được để trống.', 'warning'); return; }
            if(!confirm(`Xác nhận từ chối ${ids.length} giao dịch?`)) return;
            try {
                for(const id of ids){
                    await api.postJson(rejectUrl, { transaction_id: id, reason: txt });
                }
                window.showToast('Đã từ chối tất cả giao dịch được chọn.', 'success');
                window.location.reload();
            } catch(e){
                window.showToast('Lỗi khi từ chối: ' + e.message, 'error');
            }
        }

        // inject link-button style
        const style=document.createElement('style');
        style.innerText=`.btn-link{background:none;border:none;color:var(--primary-600);cursor:pointer;text-decoration:underline}
        .btn-link:hover{color:var(--primary-700)}`;
        document.head.appendChild(style);

        // Expose functions to window object under a namespace
        window.PurchaseManagementPageEvents = {
            viewProofModal,
            closeProofModal,
            showTransactionDetails,
            closeDetailsModal,
            approveTransaction,
            openRejectTransactionModal,
            revertTransaction,
            bulkApproveTransactions,
            bulkRevertTransactions,
            bulkRejectTransactions
        };
    });
})(window);
