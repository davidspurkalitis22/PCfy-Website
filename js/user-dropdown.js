// User dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('User dropdown script loaded');
    
    // User dropdown functionality
    const userMenus = document.querySelectorAll('.user-menu');
    console.log('Found user menus:', userMenus.length);
    
    userMenus.forEach(menu => {
        const userIcon = menu.querySelector('.user-icon');
        const dropdown = menu.querySelector('.user-dropdown');
        
        if (userIcon && dropdown) {
            userIcon.addEventListener('click', function(e) {
                console.log('User icon clicked');
                e.stopPropagation();
                dropdown.classList.toggle('show');
                console.log('Dropdown classes after toggle:', dropdown.className);
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.user-dropdown.show');
        dropdowns.forEach(dropdown => {
            if (!dropdown.parentNode.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    });
    
    // Prevent dropdown from closing when clicking inside it
    const userDropdowns = document.querySelectorAll('.user-dropdown');
    userDropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
}); 