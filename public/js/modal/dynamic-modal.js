window.DynamicModal = {
    init({ modalId = 'dynamicModal', formId = 'dynamicModalForm' }) {
        this.modal = document.getElementById(modalId);
        this.form = document.getElementById(formId);
        this.title = this.modal.querySelector('#dynamicModalTitle');
        this.fieldsContainer = this.modal.querySelector('#dynamicModalFields');
        this.submitBtn = this.modal.querySelector('#dynamicModalSubmitBtn');
        this.cancelBtn = this.modal.querySelector('#dynamicModalCancelBtn');

        this.cancelBtn.addEventListener('click', () => this.close());
        this.modal.addEventListener('click', e => {
            if (e.target === this.modal) this.close();
        });
    },

    async open({ title, action, method = 'POST', fields = [], dataEndpoint = null, editId = null }) {
        this.title.textContent = title;
        this.form.action = action;
        this.setMethod(method);
        this.fieldsContainer.innerHTML = '';

        let remoteData = {};
        if (dataEndpoint && editId) {
            try {
                const res = await fetch(`${dataEndpoint}/${editId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) remoteData = await res.json();
            } catch (e) {
                console.error('Failed loading edit data', e);
            }
        }

        for (let field of fields) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('form-group');

            let value = field.value ?? remoteData[field.name] ?? '';

            /* ---------- CHECKBOX (STATUS) ---------- */
            if (field.type === 'checkbox') {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = field.name;
                hidden.value = '0';

                const toggleWrap = document.createElement('div');
                toggleWrap.classList.add('status-toggle-wrapper');

                const toggleLabel = document.createElement('label');
                toggleLabel.classList.add('toggle-switch');

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = field.name;
                checkbox.value = '1';
                checkbox.checked = !!value;

                const slider = document.createElement('span');
                slider.classList.add('slider');

                const statusLabel = document.createElement('span');
                statusLabel.classList.add('status-label');

                const updateLabel = (active) => {
                    statusLabel.textContent = active ? 'Active' : 'Inactive';
                    statusLabel.classList.toggle('enabled', active);
                    statusLabel.classList.toggle('disabled', !active);
                };

                checkbox.addEventListener('change', () => updateLabel(checkbox.checked));
                updateLabel(checkbox.checked);

                toggleLabel.appendChild(checkbox);
                toggleLabel.appendChild(slider);
                toggleWrap.appendChild(hidden);
                toggleWrap.appendChild(toggleLabel);
                toggleWrap.appendChild(statusLabel);

                wrapper.appendChild(toggleWrap);
                this.fieldsContainer.appendChild(wrapper);
                continue;
            }

            /* ---------- LABEL ---------- */
            const label = document.createElement('label');
            label.textContent = field.label;
            wrapper.appendChild(label);

            /* ---------- SELECT ---------- */
            let input;
            if (field.type === 'select') {
                input = document.createElement('select');
                input.name = field.name;

                if (field.optionsEndpoint) {
                    const res = await fetch(field.optionsEndpoint);
                    field.options = await res.json();
                }

                field.options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.label;
                    if (opt.value == value) option.selected = true;
                    input.appendChild(option);
                });

            } else {
                input = document.createElement('input');
                input.type = field.type || 'text';
                input.name = field.name;
                input.value = value;
            }

            wrapper.appendChild(input);
            this.fieldsContainer.appendChild(wrapper);
        }

        this.modal.classList.remove('hidden');
    },

    setMethod(method) {
        let methodInput = this.form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            this.form.appendChild(methodInput);
        }
        methodInput.value = method;
    },

    close() {
        this.modal.classList.add('hidden');
        this.fieldsContainer.innerHTML = '';
        this.form.reset();
    }
};
