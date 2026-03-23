document.addEventListener('DOMContentLoaded', function () {
    const sectionSelect = document.querySelector('select[name="section_id"]');
    const departmentInput = document.querySelector('input[name="department_name"]');

    if (sectionSelect && departmentInput) {
        // Update department when section changes
        sectionSelect.addEventListener('change', function () {
            const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];
            const departmentName = selectedOption.dataset.departmentName || '';
            departmentInput.value = departmentName;
        });

        // Initial value
        departmentInput.value = sectionSelect.options[sectionSelect.selectedIndex].dataset.departmentName || '';
    }

    // Confirm before submitting
    const form = document.querySelector('.user-edit-card');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to update this user?')) {
                e.preventDefault();
            }
        });
    }
});
