document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('sectionModal');
    if (!modal) return;

    const addBtn = document.getElementById('addSectionBtn');
    const form = modal.querySelector('#sectionForm');
    const submitBtn = modal.querySelector('#sectionModalSubmitBtn');

    const nameInput = modal.querySelector('#modal_section_name');
    const codeInput = modal.querySelector('#modal_section_code');
    const departmentSelect = modal.querySelector('#modal_department_id');
    const statusInput = modal.querySelector('#modal_is_active');
    const sectionIdInput = modal.querySelector('#section_id');
    const modalTitle = modal.querySelector('#sectionModalTitle');
    const cancelBtns = modal.querySelectorAll('.modal-close');

    const validity = { name: false, code: false };

    const updateSubmitState = () => {
        submitBtn.disabled = !validity.name || !validity.code;
    };
    
    // ------------------- Helpers -------------------
    const setupStatusToggle = (checkbox) => {
        const wrapper = checkbox.closest('.status-toggle-wrapper');
        const label = wrapper.querySelector('.status-label');
        const updateLabel = (isActive) => {
            label.textContent = isActive ? 'Active' : 'Inactive';
            label.classList.toggle('enabled', isActive);
            label.classList.toggle('disabled', !isActive);
        };
        checkbox.addEventListener('change', () => updateLabel(checkbox.checked));
        checkbox.updateStatus = (val) => { checkbox.checked = !!val; updateLabel(checkbox.checked); };
        updateLabel(checkbox.checked);
    };
    setupStatusToggle(statusInput);

    const setUppercase = (input) => { input.value = input.value.toUpperCase(); };

    const setMethodInput = (method) => {
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = method;
    };

    const removeMethodInput = () => {
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
    };

    const resetForm = () => {
        form.reset();
        sectionIdInput.value = '';
        removeMethodInput();
        statusInput.updateStatus(false);
    };

    // ------------------- Modal Open/Close -------------------
    const openModal = (title, data = null) => {
        modal.classList.remove('hidden');
        modalTitle.textContent = title;
        resetForm();

        if (data) {
            // EDIT
            sectionIdInput.value = data.section_id || '';
            nameInput.value = data.section_name || '';
            codeInput.value = data.section_code || '';
            departmentSelect.value = data.department_id || '';
            statusInput.updateStatus(data.is_active === '1' || data.is_active === 1 || data.is_active === true);

            setMethodInput('PUT');
            form.action = addBtn.dataset.updateRoute.replace(':id', data.section_id);
        } else {
            // ADD
            form.action = addBtn.dataset.storeRoute;
            form.method = 'POST';
        }

        submitBtn.disabled = false;
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        resetForm();
    };

    cancelBtns.forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    // ------------------- Event Listeners -------------------
    addBtn.addEventListener('click', () => openModal('Add Section'));

    document.querySelectorAll('.editSectionBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = {
                section_id: btn.dataset.sectionId,
                section_name: btn.dataset.name,
                section_code: btn.dataset.code,
                department_id: btn.dataset.department,
                is_active: btn.dataset.active
            };
            openModal('Edit Section', section);
        });
    });

    codeInput.addEventListener('input', () => setUppercase(codeInput));

    // ------------------- Optional Validators -------------------
    if (window.validateUniqueAndSuggest) {
        const csrfToken = form.querySelector('input[name="_token"]').value;

        window.validateUniqueAndSuggest({
            input: nameInput,
            table: 'sections',
            column: 'section_name',
            fieldKey: 'name',
            csrfToken,
            feedbackClass: 'input-feedback',
            endpoint: addBtn.dataset.searchRoute,
            validity: validity,
            updateSubmitState: updateSubmitState
        });

        window.validateUniqueAndSuggest({
            input: codeInput,
            table: 'sections',
            column: 'section_code',
            fieldKey: 'code',
            csrfToken,
            feedbackClass: 'input-feedback-code',
            endpoint: addBtn.dataset.searchRoute,
            validity: validity,
            updateSubmitState: updateSubmitState
        });
    }
});
