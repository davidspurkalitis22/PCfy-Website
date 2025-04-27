// Hamburger menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const nav = document.querySelector('nav');
    
    // Toggle mobile menu
    hamburger.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent click from bubbling to document
        hamburger.classList.toggle('active');
        nav.classList.toggle('active');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = nav.contains(event.target);
        const isClickOnHamburger = hamburger.contains(event.target);
        
        if (!isClickInsideNav && !isClickOnHamburger && nav.classList.contains('active')) {
            hamburger.classList.remove('active');
            nav.classList.remove('active');
        }
    });
    
    // Close menu when clicking a nav link
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            hamburger.classList.remove('active');
            nav.classList.remove('active');
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            hamburger.classList.remove('active');
            nav.classList.remove('active');
        }
    });
}); 