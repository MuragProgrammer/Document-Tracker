document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('actionModal');
    if (!modal) return;

    const cancelBtn = modal.querySelector('.modal-cancel');
    const sectionContainer = modal.querySelector('#sectionContainer');
    const actionTypeInput = modal.querySelector('#action_type');
    const modalSectionInput = modal.querySelector('#modal_section_id');
    const selectSection = modal.querySelector('#select_section');
    const actionForm = modal.querySelector('#actionForm');

    document.querySelectorAll('.modal-open').forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action || 'Action';
            modal.querySelector('.modal-title').textContent = action + ' Document';
            actionTypeInput.value = action;

            // Show section select only if Forward
            sectionContainer.style.display = action === 'Forward' ? 'block' : 'none';
            modalSectionInput.value = '';
            if (selectSection) selectSection.value = '';

            modal.classList.remove('hidden');
        });
    });

    // Sync section select
    if (selectSection) {
        selectSection.addEventListener('change', () => {
            modalSectionInput.value = selectSection.value;
        });
    }

    // Cancel button
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', e => {
        if (e.target === modal) modal.classList.add('hidden');
    });
});
