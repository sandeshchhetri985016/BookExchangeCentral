// Add any interactive functionality here
document.addEventListener('DOMContentLoaded', function() {
    // Example: Toggle dropdown menus
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function() {
            this.querySelector('.dropdown-content').classList.toggle('show');
        });
    });
});