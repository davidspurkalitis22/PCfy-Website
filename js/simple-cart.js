// Simple standalone cart toggle functionality
window.addEventListener('load', function() {
    console.log('simple-cart.js loaded');
    
    // Direct access to cart elements
    const cartIcons = document.querySelectorAll('.cart-icon, .nav-cart-icon');
    const cartDropdown = document.querySelector('.cart-dropdown');
    
    console.log('Simple cart found elements: icons=', cartIcons.length, ' dropdown=', cartDropdown ? 'yes' : 'no');
    
    // Check if elements exist
    if (!cartIcons.length || !cartDropdown) {
        console.error('Cart elements not found');
        return;
    }
    
    // Make sure dropdown has inline CSS at load
    if (cartDropdown) {
        cartDropdown.style.display = 'none';
    }
    
    // Add click event to all cart icons
    cartIcons.forEach(function(icon) {
        // Remove existing click event listeners
        const iconClone = icon.cloneNode(true);
        icon.parentNode.replaceChild(iconClone, icon);
        
        // Add new click event listener
        iconClone.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Cart icon clicked [simple-cart.js]');
            
            // Toggle inline display style
            if (cartDropdown.style.display === 'block') {
                cartDropdown.style.display = 'none';
            } else {
                cartDropdown.style.display = 'block';
            }
            
            console.log('Cart dropdown display: ' + cartDropdown.style.display);
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (cartDropdown && 
            cartDropdown.style.display === 'block' && 
            !cartDropdown.contains(e.target) && 
            !e.target.closest('.cart-icon') && 
            !e.target.closest('.nav-cart-icon')) {
            
            cartDropdown.style.display = 'none';
            console.log('Closing cart dropdown from document click');
        }
    });
}); 