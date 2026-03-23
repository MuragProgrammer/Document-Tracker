document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('departmentModal');
    if (!modal) return;

    const addBtn = document.getElementById('addDeptBtn');
    const form = modal.querySelector('#departmentForm');
    const submitBtn = modal.querySelector('#modalSubmitBtn');
    const csrfToken = form.querySelector('input[name="_token"]').value;

    const nameInput = modal.querySelector('#modal_department_name');
    const codeInput = modal.querySelector('#modal_department_code');
    const statusInput = modal.querySelector('#modal_is_active');
    const deptIdInput = modal.querySelector('#department_id');
    const modalTitle = modal.querySelector('#modalTitle');
    const cancelBtns = modal.querySelectorAll('.modal-close');

    // Track validity
    const validity = { name: false, code: false };

    // ------------------- Helpers -------------------
    const updateSubmitState = () => {
        submitBtn.disabled = !validity.name || !validity.code;
    };

    const resetInputs = () => {
        [nameInput, codeInput].forEach(input => {
            input.classList.remove('valid', 'invalid');
            const feedback = input.parentElement.querySelector('.input-feedback, .input-feedback-code');
            if (feedback) feedback.textContent = '';
        });
        Object.keys(validity).forEach(key => validity[key] = false);
        updateSubmitState();
    };

    const setUppercase = (input) => {
        input.value = input.value.toUpperCase();
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

        // Expose programmatic setter
        checkbox.updateStatus = (val) => {
            checkbox.checked = !!val;
            updateLabel(checkbox.checked);
        };

        // Initialize label
        updateLabel(checkbox.checked);
    };

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

    // ------------------- Modal Functions -------------------
    const openModal = (title, data = null, method = 'POST', actionUrl = '') => {
        modal.classList.remove('hidden');
        modalTitle.textContent = title;

        form.reset();
        resetInputs();
        statusInput.updateStatus(false);

        if (data) {
            deptIdInput.value = data.department_id || '';
            nameInput.value = data.department_name || '';
            codeInput.value = data.department_code || '';
            statusInput.updateStatus(data.is_active === '1' || data.is_active === 1 || data.is_active === true);
            setMethodInput(method);
            form.action = actionUrl || form.action;
            validity.name = true;
            validity.code = true;
        } else {
            deptIdInput.value = '';
            removeMethodInput();
            form.action = '/departments/store';
        }

        updateSubmitState();
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        resetInputs();
        form.reset();
        statusInput.updateStatus(false);
    };

    // ------------------- Initialize -------------------
    // Uppercase for code
    codeInput.addEventListener('input', () => setUppercase(codeInput));

    // Status toggle
    setupStatusToggle(statusInput);

    // Cancel buttons
    cancelBtns.forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    // Open Add modal
    addBtn.addEventListener('click', () => openModal('Add Department'));

    // Open Edit modal
    document.querySelectorAll('.editDeptBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const department = {
                department_id: btn.dataset.departmentId,
                department_name: btn.dataset.name,
                department_code: btn.dataset.code,
                is_active: btn.dataset.active
            };
            openModal(
                'Edit Department',
                department,
                'PUT',
                `/departments/${department.department_id}`
            );
        });
    });

    // ------------------- Validators -------------------
    const endpoint = addBtn.dataset.searchRoute;
    window.validateUniqueAndSuggest({
        input: nameInput,
        table: 'departments',
        column: 'department_name',
        fieldKey: 'name',
        csrfToken: csrfToken,
        feedbackClass: 'input-feedback',
        endpoint: endpoint,
        strict: true,
        validity: validity,
        updateSubmitState: updateSubmitState
    });

    window.validateUniqueAndSuggest({
        input: codeInput,
        table: 'departments',
        column: 'department_code',
        fieldKey: 'code',
        csrfToken: csrfToken,
        feedbackClass: 'input-feedback-code',
        endpoint: endpoint,
        validity: validity,
        updateSubmitState: updateSubmitState
    });
});
