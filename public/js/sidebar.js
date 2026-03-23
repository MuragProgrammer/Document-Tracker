const menuCheckbox = document.getElementById('menuCheckbox');
const menuToggle = document.getElementById('menuToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const sidebarContainer = document.querySelector('.sidebar-container');

function openSidebar() {
    sidebarContainer.classList.add('active');
    sidebarOverlay.classList.add('active');
    menuToggle.classList.add('active');
}

function closeSidebar() {
    sidebarContainer.classList.remove('active');
    sidebarOverlay.classList.remove('active');
    menuToggle.classList.remove('active');
    menuCheckbox.checked = false;
}

// Toggle sidebar when burger clicked
menuCheckbox.addEventListener('change', () => {
    if (menuCheckbox.checked) {
        openSidebar();
    } else {
        closeSidebar();
    }
});

// Click overlay to close
sidebarOverlay.addEventListener('click', closeSidebar);

// Close on window resize
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        closeSidebar();
    }
});
