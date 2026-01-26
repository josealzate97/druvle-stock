document.addEventListener("DOMContentLoaded", function() {

    const sidebarToggle = document.getElementById('sidebar-toggle');
    const wrapper = document.querySelector('.wrapper');

    if (sidebarToggle && wrapper) {

        sidebarToggle.addEventListener('click', function() {
            wrapper.classList.toggle('sidebar-toggled');
        });
        
    }

});