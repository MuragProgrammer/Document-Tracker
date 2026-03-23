function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerText = message;

    container.appendChild(toast);

    setTimeout(() => toast.remove(), 5000);
}

document.addEventListener('DOMContentLoaded', () => {

    // Success
    if (window.toastSuccess) {
        showToast(window.toastSuccess, 'success');
    }

    // Error
    if (window.toastError) {
        showToast(window.toastError, 'error');
    }

    // Validation Errors
    if (Array.isArray(window.toastErrors)) {
        window.toastErrors.forEach(err => showToast(err, 'error'));
    }
});
