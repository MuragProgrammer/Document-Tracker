document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('userModal');
    if (!modal) return;

    const addBtn = document.getElementById('addUserBtn'); // Your "Add User" button
    const form = modal.querySelector('#userForm');
    const submitBtn = form.querySelector('#userModalSubmitBtn');
    const modalTitle = modal.querySelector('#userModalTitle');
    const cancelBtns = modal.querySelectorAll('.modal-close');

    // Inputs
    const userIdInput = modal.querySelector('#user_id');
    const fullNameInput = modal.querySelector('#modal_full_name');
    const usernameInput = modal.querySelector('#modal_username');
    const passwordInput = modal.querySelector('#modal_password');
    const sectionSelect = modal.querySelector('#modal_section_id');
    const departmentInput = modal.querySelector('#modal_department_name');
    const positionSelect = modal.querySelector('#modal_position_id');
    const roleSelect = modal.querySelector('#modal_role');
    const statusCheckbox = modal.querySelector('#modal_is_active');

    // ----------------- Helpers -----------------
    const resetInputs = () => {
        [fullNameInput, usernameInput, passwordInput].forEach(input => {
            input.classList.remove('valid', 'invalid');
            const feedback = input.parentElement.querySelector('.input-feedback');
            if (feedback) feedback.textContent = '';
            input.value = '';
        });
        sectionSelect.value = '';
        positionSelect.value = '';
        departmentInput.value = '';
        roleSelect.value = 'EMPLOYEE';
        statusCheckbox.checked = true;
        statusCheckbox.updateStatus(true);
        userIdInput.value = '';
    };

    // Status toggle setup (like positions modal)
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
    setupStatusToggle(statusCheckbox);

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

    // ----------------- Modal Control -----------------
    const openModal = (title, data = null, method = 'POST', actionUrl = '') => {
        modal.classList.remove('hidden');
        modalTitle.textContent = title;

        resetInputs();

        if (data) {
            userIdInput.value = data.id || '';
            fullNameInput.value = data.full_name || '';
            usernameInput.value = data.username || '';
            sectionSelect.value = data.section_id || '';
            departmentInput.value = data.department_name || '';
            positionSelect.value = data.position_id || '';
            roleSelect.value = data.role || 'EMPLOYEE';
            statusCheckbox.updateStatus(data.is_active == 1 || data.is_active === '1');

            setMethodInput(method);
            form.action = actionUrl || form.action;
        } else {
            removeMethodInput();
            form.action = addBtn.dataset.storeRoute;
        }
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        resetInputs();
    };

    // ----------------- Event Listeners -----------------
    cancelBtns.forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    if (addBtn) addBtn.addEventListener('click', () => openModal('Add User'));

    document.querySelectorAll('.editUserBtn').forEach(btn => {
        btn.addEventListener('click', () => openModal(
            'Edit User',
            {
                id: btn.dataset.userId,
                full_name: btn.dataset.fullName,
                username: btn.dataset.username,
                section_id: btn.dataset.sectionId,
                department_name: btn.dataset.departmentName,
                position_id: btn.dataset.positionId,
                role: btn.dataset.role,
                is_active: btn.dataset.active
            },
            'PUT',
            `/users/${btn.dataset.userId}`
        ));
    });

    // Update department on section change
    sectionSelect.addEventListener('change', () => {
        const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];
        departmentInput.value = selectedOption.dataset.departmentName || '';
    });
});
