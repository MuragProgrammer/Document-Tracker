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


function initLiveSearch({
    inputId,
    containerId,
    url,
    extraParams = () => ({})
}) {
    const input = document.getElementById(inputId);
    const container = document.getElementById(containerId);

    if (!input || !container) return;

    let timeout = null;

    const fetchData = (customUrl = null) => {
        const params = new URLSearchParams({
            search: input.value,
            ...extraParams()
        });

        const fetchUrl = customUrl || `${url}?${params.toString()}`;

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
        });
    };

    input.addEventListener('keyup', () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fetchData(), 300);
    });

    document.addEventListener('change', () => {
        fetchData();
    });

    document.addEventListener('click', (e) => {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            fetchData(e.target.closest('a').href);
        }
    });
}

