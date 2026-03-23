document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('document-search');
    const tableBody   = document.getElementById('documents-table-body');

    if (!searchInput || !tableBody) return;

    let timeout = null;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            const query = this.value.trim();

            fetch(`/dashboard/search?search=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableBody.innerHTML = html;
            })
            .catch(err => console.error('Search error:', err));

        }, 300); // debounce
    });
});
