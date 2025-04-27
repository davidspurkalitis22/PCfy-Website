// Simple Cart Icon Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('cart-icon.js loaded');
    
    // Find all cart icons
    const cartIcons = document.querySelectorAll('.cart-icon, .nav-cart-icon');
    const cartDropdown = document.querySelector('.cart-dropdown');
    
    console.log('Cart icons found:', cartIcons.length);
    console.log('Cart dropdown found:', cartDropdown ? 'Yes' : 'No');
    
    // Make sure dropdown starts hidden with inline style (overrides any CSS)
    if (cartDropdown) {
        // Initialize with display:none to ensure it starts hidden
        cartDropdown.style.display = 'none';
    }
    
    // Add click event to all cart icons
    if (cartIcons.length > 0) {
        cartIcons.forEach(icon => {
            icon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Cart icon clicked from cart-icon.js');
                
                if (cartDropdown) {
                    // Toggle using inline style directly instead of classes
                    if (cartDropdown.style.display === 'block') {
                        cartDropdown.style.display = 'none';
                    } else {
                        cartDropdown.style.display = 'block';
                    }
                    console.log('Cart dropdown toggled, display:', cartDropdown.style.display);
                }
            });
        });
    }
    
    // Close cart dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (cartDropdown && 
            cartDropdown.style.display === 'block' && 
            !cartDropdown.contains(e.target) && 
            !e.target.closest('.cart-icon') && 
            !e.target.closest('.nav-cart-icon')) {
            cartDropdown.style.display = 'none';
        }
    });
}); 