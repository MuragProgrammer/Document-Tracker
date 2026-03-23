// Initialize all toggle switches (table and modal)
function initializeToggleSwitches() {
    const toggles = document.querySelectorAll('.status-toggle');

    toggles.forEach(toggle => {
        const wrapper = toggle.closest('.status-toggle-wrapper');
        if (!wrapper) return;

        let label = wrapper.querySelector('.status-label');

        // Create label if missing
        if (!label) {
            label = document.createElement('span');
            label.classList.add('status-label');
            wrapper.appendChild(label);
        }

        // Function to update label UI
        const updateUI = (isActive) => {
            if (isActive) {
                label.textContent = 'Active';
                label.classList.add('enabled');
                label.classList.remove('disabled');
            } else {
                label.textContent = 'Inactive';
                label.classList.add('disabled');
                label.classList.remove('enabled');
            }
        };

        // Initialize label on page load
        updateUI(toggle.checked);

        // Listen for changes
        toggle.addEventListener('change', function () {
            const isModalToggle = !this.dataset.id; // No data-id = modal
            const originalState = this.checked;

            // Optimistically update UI
            updateUI(originalState);

            if (!isModalToggle) {
                // Table toggle -> send AJAX request
                const departmentId = this.dataset.id;

                fetch(`/departments/${departmentId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error();
                    // Update UI based on server response
                    updateUI(data.is_active);
                    this.checked = data.is_active;
                })
                .catch(() => {
                    // Revert UI on failure
                    updateUI(!originalState);
                    this.checked = !originalState;
                    alert('Failed to update status');
                });
            }
            // Else: modal toggle -> just update label (no backend request needed yet)
        });
    });
}

// Auto-run on page load
document.addEventListener('DOMContentLoaded', initializeToggleSwitches);
