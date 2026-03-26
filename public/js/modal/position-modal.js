document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('positionModal');
    if (!modal) return;

    const addBtn = document.getElementById('addPositionBtn');
    const form = modal.querySelector('#positionForm');
    const submitBtn = form.querySelector('#positionModalSubmitBtn');

    const titleInput = modal.querySelector('#modal_position_title');
    const statusInput = modal.querySelector('#modal_is_active');
    const positionIdInput = modal.querySelector('#position_id');
    const modalTitle = modal.querySelector('#positionModalTitle');
    const cancelBtns = modal.querySelectorAll('.modal-close');

    const validity = { title: false }; // Only title now

    // ------------------- Helpers -------------------
    const updateSubmitState = () => submitBtn.disabled = !validity.title;

    const resetInputs = () => {
        [titleInput].forEach(input => {
            input.classList.remove('valid', 'invalid');
            const feedback = input.parentElement.querySelector('.input-feedback');
            if (feedback) feedback.textContent = '';
            input.value = '';
        });
        Object.keys(validity).forEach(k => validity[k] = false);
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

    // ------------------- Modal Control -------------------
    const openModal = (title, data = null, method = 'POST', actionUrl = '') => {
        modal.classList.remove('hidden');
        modalTitle.textContent = title;

        form.reset();
        resetInputs();

        if (data) {
            positionIdInput.value = data.position_id || '';
            titleInput.value = data.position_title || '';
            validity.title = !!data.position_title;

            // Set checkbox
            const isActive = data.is_active == 1 || data.is_active === '1';
            statusInput.checked = isActive;
            statusInput.updateStatus(isActive);

            setMethodInput(method);
            form.action = actionUrl || form.action;
        } else {
            positionIdInput.value = '';
            removeMethodInput();
            form.action = addBtn.dataset.storeRoute;
        }

        updateSubmitState();
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        form.reset();
        resetInputs();
    };

    // ------------------- Init -------------------
    cancelBtns.forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    addBtn.addEventListener('click', () => openModal('Add Position'));

    document.querySelectorAll('.editPositionBtn').forEach(btn => {
        btn.addEventListener('click', () => openModal(
            'Edit Position',
            {
                position_id: btn.dataset.positionId,
                position_title: btn.dataset.positionTitle,
                is_active: btn.dataset.active
            },
            'PUT',
            `/positions/${btn.dataset.positionId}`
        ));
    });

    // ------------------- Validation -------------------
    titleInput.addEventListener('input', () => {
        validity.title = titleInput.value.trim().length > 0;
        updateSubmitState();
    });
});
