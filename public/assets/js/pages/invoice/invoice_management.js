(function(window){
    document.addEventListener('DOMContentLoaded', function(){
        const appBase = (window.appConfig && window.appConfig.basePath) ? window.appConfig.basePath : '';
        // modal elements
        const proofModal = document.getElementById('proofModal');
        const proofModalImage = document.getElementById('proofModalImage');
        const proofModalTitle = document.getElementById('proofModalTitle');
        const detailsModal = document.getElementById('transaction-details-modal');

        // VIEW PROOF
        window.viewProofModal = function(id, url){
            if(!url){ alert("Không có hình ảnh minh chứng."); return; }
            proofModalTitle.textContent = `Minh chứng Giao dịch #${id}`;
            proofModalImage.src = url;
            proofModal.classList.add('active');
        };
        window.closeProofModal = function(){
            proofModal.classList.remove('active');
        };

        // VIEW DETAILS
        window.showTransactionDetails = function(data){
            if(!detailsModal||!data) return;
            document.getElementById('modal-title').textContent = `Chi Tiết Giao Dịch #${data.id}`;
            document.getElementById('modal-tx-id').textContent = data.id;
            document.getElementById('modal-tx-email').textContent = data.email;
            document.getElementById('modal-tx-package').textContent = data.package_name;
            document.getElementById('modal-tx-amount').textContent = data.amount;
            document.getElementById('modal-tx-request-date').textContent = data.request_date;
            const badge = document.getElementById('modal-tx-status-badge');
            badge.className = 'status-badge status-badge-modal ' + data.status_class;
            document.getElementById('modal-tx-status-text').textContent = data.status_text;

            // proof link
            const proofLink = document.getElementById('modal-tx-proof-link');
            if(data.proof_image){
                proofLink.innerHTML = `<a href="${data.proof_image}" target="_blank">Xem hình ảnh&nbsp;</a>
                    | <button class="btn-link" onclick="viewProofModal('${data.id}','${data.proof_image}'); closeDetailsModal();">Xem trong modal</button>`;
            } else proofLink.textContent = 'Không có';

            // rejection reason
            const rejCont = document.getElementById('modal-tx-rejection-reason-container');
            if(data.rejection_reason){
                document.getElementById('modal-tx-rejection-reason').textContent = data.rejection_reason;
                rejCont.style.display = 'flex';
            } else rejCont.style.display = 'none';

            detailsModal.classList.add('active');
        };
        window.closeDetailsModal = function(){
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
        const apiBasePath = `${appBase}public/actions/invoice/index.php`;
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
            if(newStat==='pending'){
                html=`<button class="btn-icon btn-approve" onclick="approveTransaction('${id}',this)"><i class="fas fa-check-circle"></i></button>
                      <button class="btn-icon btn-reject" onclick="openRejectTransactionModal('${id}')"><i class="fas fa-times-circle"></i></button>
                      <button class="btn-icon btn-disabled" disabled><i class="fas fa-undo-alt"></i></button>`;
            } else if(newStat==='active'){
                html=`<button class="btn-icon btn-disabled" disabled><i class="fas fa-check-circle"></i></button>
                      <button class="btn-icon btn-reject" onclick="openRejectTransactionModal('${id}')"><i class="fas fa-times-circle"></i></button>
                      <button class="btn-icon btn-revert" onclick="revertTransaction('${id}',this)"><i class="fas fa-undo-alt"></i></button>`;
            } else if(newStat==='rejected'){
                html=`<button class="btn-icon btn-approve" onclick="approveTransaction('${id}',this)"><i class="fas fa-check-circle"></i></button>
                      <button class="btn-icon btn-disabled" disabled><i class="fas fa-times-circle"></i></button>
                      <button class="btn-icon btn-disabled" disabled><i class="fas fa-undo-alt"></i></button>`;
            }
            cell.innerHTML = html;
        }

        // transaction actions
        window.approveTransaction = async function(id,btn){
            if(!confirm(`Bạn có chắc muốn duyệt #${id}?`)) return;
            const row=btn.closest('tr'); disableActionButtons(row);
            try {
                const resp=await fetch(approveUrl,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({transaction_id:id})});
                const data=await resp.json();
                if(resp.ok&&data.success){
                    alert(`Duyệt thành công!\nUsername:${data.account.username}\nPassword:${data.account.password}`);
                    updateTableRowStatus(id,'active','Đã duyệt','status-approved');
                } else {
                    throw data;
                }
            } catch(e){
                alert('Lỗi không gây ảnh hưởng đến kết quả: '+(e.message||e)); enableActionButtons(row,row.dataset.status);
            }
        };

        window.openRejectTransactionModal = async function(id){
            const reason=prompt(`Lý do từ chối #${id}:`);
            if(reason==null) return;
            const text=reason.trim(); if(!text){ alert('Nhập lý do.'); return; }
            const row=document.querySelector(`tr[data-transaction-id="${id}"]`);
            disableActionButtons(row);
            try {
                const resp=await fetch(rejectUrl,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({transaction_id:id,reason:text})});
                const data=await resp.json();
                if(resp.ok&&data.success){
                    alert('Từ chối thành công!'); updateTableRowStatus(id,'rejected','Bị từ chối','status-rejected');
                } else throw data;
            } catch(e){
                alert('Lỗi từ chối.'); enableActionButtons(row,row.dataset.status);
            }
        };

        window.revertTransaction = async function(id,btn){
            if(!confirm(`Hủy duyệt #${id}?`)) return;
            const row=btn.closest('tr'); disableActionButtons(row);
            try {
                const resp=await fetch(revertUrl,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({transaction_id:id})});
                const data=await resp.json();
                if(resp.ok&&data.success){
                    alert('Hủy duyệt thành công.'); updateTableRowStatus(id,'pending','Chờ duyệt','status-pending');
                } else throw data;
            } catch(e){
                alert('Lỗi hủy duyệt.'); enableActionButtons(row,row.dataset.status);
            }
        };

        // inject link-button style
        const style=document.createElement('style');
        style.innerText=`.btn-link{background:none;border:none;color:var(--primary-600);cursor:pointer;text-decoration:underline}
        .btn-link:hover{color:var(--primary-700)}`;
        document.head.appendChild(style);
    });
})(window);
