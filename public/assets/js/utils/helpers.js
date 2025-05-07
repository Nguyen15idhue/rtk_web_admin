(function(window){
    function closeModal(id){
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'none';
        // reset tất cả các form trong modal
        m.querySelectorAll('form').forEach(f => f.reset());
        // xoá mọi lỗi
        m.querySelectorAll('.error-message').forEach(err => err.textContent = '');
        // gỡ class visible của company-fields
        m.querySelectorAll('.company-fields.visible').forEach(cf => cf.classList.remove('visible'));
    }

    function toggleCompanyFields(formType){
        const chk = document.getElementById(`${formType}IsCompany`);
        const div = document.getElementById(`${formType}CompanyFields`);
        const name = document.getElementById(`${formType}CompanyName`);
        const tax  = document.getElementById(`${formType}TaxCode`);
        if(chk.checked){
            div.classList.add('visible');
            name.required = tax.required = true;
        } else {
            div.classList.remove('visible');
            name.required = tax.required = false;
        }
    }

    // close on outside click
    window.addEventListener('click', e => {
        ['view','edit','create'].forEach(t => {
            const mid = t + 'UserModal';
            if(e.target === document.getElementById(mid)){
                closeModal(mid);
            }
        });
    });

    // expose to helpers namespace
    window.helpers = { closeModal, toggleCompanyFields };

})(window);
