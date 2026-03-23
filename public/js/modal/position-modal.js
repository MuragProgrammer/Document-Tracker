document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('positionModal');
    if (!modal) return;

    const addBtn = document.getElementById('addPositionBtn');
    const form = modal.querySelector('#positionForm');
    const submitBtn = form.querySelector('#positionModalSubmitBtn');
    const csrfToken = form.querySelector('input[name="_token"]').value;

    const titleInput = modal.querySelector('#modal_position_title');
    const plantillaInput = modal.querySelector('#modal_plantilla_number');
    const statusInput = modal.querySelector('#modal_is_active');
    const positionIdInput = modal.querySelector('#position_id');
    const modalTitle = modal.querySelector('#positionModalTitle');
    const cancelBtns = modal.querySelectorAll('.modal-close');

    const validity = { title: false, plantilla: false };

    // ------------------- Helpers -------------------
    const updateSubmitState = () => submitBtn.disabled = !validity.title || !validity.plantilla;

    const resetInputs = () => {
        [titleInput, plantillaInput].forEach(input => {
            input.classList.remove('valid', 'invalid');
            const feedback = input.parentElement.querySelector('.input-feedback, .input-feedback-plantilla');
            if (feedback) feedback.textContent = '';
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
        statusInput.value = '1';

        if (data) {
            positionIdInput.value = data.position_id || '';
            titleInput.value = data.position_title || '';
            plantillaInput.value = data.plantilla_number || '';

            // Correctly set checkbox
            const isActive = data.is_active == 1 || data.is_active === '1';
            statusInput.checked = isActive; // ✅ set checked
            statusInput.updateStatus(isActive); // ✅ update label

            setMethodInput(method);
            form.action = actionUrl || form.action;
            validity.title = true;
            validity.plantilla = true;
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
        statusInput.value = '1';
    };

    // ------------------- Init -------------------
    setupStatusToggle(statusInput);
    cancelBtns.forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    addBtn.addEventListener('click', () => openModal('Add Position'));

    document.querySelectorAll('.editPositionBtn').forEach(btn => {
        btn.addEventListener('click', () => openModal(
            'Edit Position',
            {
                position_id: btn.dataset.positionId,
                position_title: btn.dataset.title,
                plantilla_number: btn.dataset.plantilla,
                is_active: btn.dataset.active
            },
            'PUT',
            `/positions/${btn.dataset.positionId}`
        ));
    });
});
