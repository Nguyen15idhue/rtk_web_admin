(function(window){
    function closeModal(id){
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'none';
        // reset forms & errors
        const form = m.querySelector('form');
        if(form){ form.reset(); }
        const err = m.querySelector('.error-message');
        if(err){ err.textContent = ''; }
        const cf = m.querySelector('.company-fields');
        if(cf){ cf.classList.remove('visible'); }
        ['CompanyName','TaxCode'].forEach(f => {
            const inp = m.querySelector(`#${id === 'createUserModal' ? 'create' : 'edit'}${f}`);
            if(inp) inp.required = false;
        });
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

    // also expose as globals for inline onclick handlers
    window.closeModal = closeModal;
    window.toggleCompanyFields = toggleCompanyFields;

})(window);
