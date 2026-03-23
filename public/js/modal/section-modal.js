document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('sectionModal');
    if (!modal) return;

    const addBtn = document.getElementById('addSectionBtn');
    const form = modal.querySelector('#sectionForm');
    const submitBtn = modal.querySelector('#sectionModalSubmitBtn');
    const nameInput = modal.querySelector('#modal_section_name');
    const departmentSelect = modal.querySelector('#modal_department_id');
    const statusInput = modal.querySelector('#modal_is_active');
    const sectionIdInput = modal.querySelector('#section_id');
    const modalTitle = modal.querySelector('#sectionModalTitle');
    const cancelBtns = modal.querySelectorAll('.modal-close');

    const validity = { name: false, department: false };

    const updateSubmitState = () => {
        submitBtn.disabled = !validity.name || !departmentSelect.value;
    };

    const resetInputs = () => {
        [nameInput, departmentSelect].forEach(input => {
            input.classList.remove('valid', 'invalid');
            const feedback = input.parentElement.querySelector('.input-feedback, .input-feedback-department');
            if (feedback) feedback.textContent = '';
        });
        Object.keys(validity).forEach(key => validity[key] = false);
        updateSubmitState();
    };

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

    const openModal = (title, data = null, method = 'POST', actionUrl = '') => {
        modal.classList.remove('hidden');
        modalTitle.textContent = title;
        form.reset();
        resetInputs();
        statusInput.updateStatus(false);

        if (data) {
            sectionIdInput.value = data.section_id || '';
            nameInput.value = data.section_name || '';
            departmentSelect.value = data.department_id || '';
            statusInput.updateStatus(data.is_active === '1' || data.is_active === 1 || data.is_active === true);
            setMethodInput(method);
            form.action = actionUrl || form.action;
            validity.name = true;
            validity.department = true;
        } else {
            sectionIdInput.value = '';
            removeMethodInput();
            form.action = addBtn.dataset.storeRoute;
        }
        updateSubmitState();
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        form.reset();
        resetInputs();
        statusInput.updateStatus(false);
    };

    cancelBtns.forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    addBtn.addEventListener('click', () => openModal('Add Section'));

    // Edit buttons
    document.querySelectorAll('.editSectionBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = {
                section_id: btn.dataset.sectionId,
                section_name: btn.dataset.name,
                department_id: btn.dataset.department,
                is_active: btn.dataset.active
            };
            openModal('Edit Section', section, 'PUT', `/sections/${section.section_id}`);
        });
    });

    // Optional: uniqueness validator
    if (window.validateUniqueAndSuggest) {
        window.validateUniqueAndSuggest({
            input: nameInput,
            table: 'sections',
            column: 'section_name',
            fieldKey: 'name',
            csrfToken: form.querySelector('input[name="_token"]').value,
            feedbackClass: 'input-feedback',
            endpoint: addBtn.dataset.searchRoute,
            validity: validity,
            updateSubmitState: updateSubmitState
        });
    }
});
