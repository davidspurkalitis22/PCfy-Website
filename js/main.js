// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const nav = document.querySelector('nav');
    const navContainer = document.querySelector('.nav-container');
    
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

// Repair booking form handling
document.addEventListener('DOMContentLoaded', function() {
    const repairForm = document.getElementById('repair-booking-form');
    
    if (repairForm) {
        repairForm.addEventListener('submit', function(e) {
            // Show loading indicator if you have one
            const submitBtn = repairForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitBtn.disabled = true;
                
                // Safety timeout - re-enable the button after 10 seconds in case of errors
                setTimeout(function() {
                    if (document.body.contains(submitBtn)) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 10000);
            }
            
            // Form will submit normally - no need to prevent default
            // The PHP will handle redirecting back to the form with success/error messages
        });
    }
});

// Contact form handling
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Show loading indicator if you have one
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                submitBtn.disabled = true;
                
                // Safety timeout - re-enable the button after 10 seconds in case of errors
                setTimeout(function() {
                    if (document.body.contains(submitBtn)) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 10000);
            }
            
            // Form will submit normally - no need to prevent default
            // The PHP will handle redirecting back to the form with success/error messages
        });
    }
});

// User dropdown menu and cart dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    // User dropdown functionality
    const userMenu = document.querySelector('.user-menu');
    
    if (userMenu) {
        const userIcon = userMenu.querySelector('.user-icon');
        const userDropdown = userMenu.querySelector('.user-dropdown');
        
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close cart dropdown if it's open
            const cartDropdown = document.querySelector('.cart-dropdown');
            if (cartDropdown && cartDropdown.classList.contains('show')) {
                cartDropdown.classList.remove('show');
            }
            
            userDropdown.classList.toggle('show');
        });
    }
    
    // Cart dropdown functionality
    const cartIcon = document.getElementById('header-cart-icon');
    
    if (cartIcon) {
        const cartDropdown = cartIcon.querySelector('.cart-dropdown');
        
        cartIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close user dropdown if it's open
            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown && userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
            }
            
            cartDropdown.classList.toggle('show');
        });
    }
    
    // Close all dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const userDropdown = document.querySelector('.user-dropdown');
        const cartDropdown = document.querySelector('.cart-dropdown');
        
        if (userDropdown && userDropdown.classList.contains('show') && 
            !e.target.closest('.user-menu')) {
            userDropdown.classList.remove('show');
        }
        
        if (cartDropdown && cartDropdown.classList.contains('show') && 
            !e.target.closest('#header-cart-icon')) {
            cartDropdown.classList.remove('show');
        }
    });
}); 