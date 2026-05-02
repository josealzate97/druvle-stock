document.addEventListener("DOMContentLoaded", function() {

    const sidebarToggle = document.getElementById('sidebar-toggle');
    const wrapper = document.querySelector('.wrapper');
    const sidebar = document.getElementById('sidebar');
    const sidebarLinks = sidebar ? sidebar.querySelectorAll('.sidebar-link') : [];
    let lastFocusedElement = null;

    const isMobileViewport = () => window.matchMedia('(max-width: 991.98px)').matches;

    const setSidebarState = (isOpen) => {
        if (!wrapper || !sidebarToggle) return;

        wrapper.classList.toggle('sidebar-toggled', isOpen);
        document.body.classList.toggle('sidebar-open', isOpen && isMobileViewport());
        sidebarToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            lastFocusedElement = document.activeElement;
            const firstLink = sidebar ? sidebar.querySelector('.sidebar-link') : null;
            if (firstLink) {
                firstLink.focus();
            }
        } else if (lastFocusedElement instanceof HTMLElement) {
            lastFocusedElement.focus();
        }
    };

    if (sidebarToggle && wrapper && sidebar) {

        sidebarToggle.addEventListener('click', function() {
            const isOpen = wrapper.classList.contains('sidebar-toggled');
            setSidebarState(!isOpen);
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && wrapper.classList.contains('sidebar-toggled')) {
                setSidebarState(false);
            }
        });

        wrapper.addEventListener('click', function(event) {
            if (!isMobileViewport()) return;
            if (!wrapper.classList.contains('sidebar-toggled')) return;

            const clickedInsideSidebar = sidebar.contains(event.target);
            const clickedToggle = sidebarToggle.contains(event.target);

            if (!clickedInsideSidebar && !clickedToggle) {
                setSidebarState(false);
            }
        });

        sidebarLinks.forEach((link) => {
            link.addEventListener('click', () => {
                if (isMobileViewport()) {
                    setSidebarState(false);
                }
            });
        });

        window.addEventListener('resize', () => {
            if (!isMobileViewport()) {
                document.body.classList.remove('sidebar-open');
                sidebarToggle.setAttribute('aria-expanded', 'false');
                wrapper.classList.remove('sidebar-toggled');
            }
        });
    }

});
