// Shopping Cart Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Cart elements
    const cartIcons = document.querySelectorAll('.cart-icon, .nav-cart-icon');
    const cartDropdown = document.querySelector('.cart-dropdown');
    const cartCount = document.querySelector('.cart-count');
    const cartItems = document.querySelector('.cart-items');
    const subtotalAmount = document.querySelector('.subtotal-amount');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    // Initialize cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Set cookie function
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }
    
    // Get cookie function
    function getCookie(name) {
        const cookieName = name + "=";
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.indexOf(cookieName) === 0) {
                return cookie.substring(cookieName.length, cookie.length);
            }
        }
        return "";
    }
    
    // Initialize from cookie if available
    const cartCookie = getCookie('cart');
    if (cartCookie && (!cart || cart.length === 0)) {
        try {
            cart = JSON.parse(decodeURIComponent(cartCookie));
            localStorage.setItem('cart', JSON.stringify(cart));
        } catch (e) {
            console.error('Error parsing cart cookie:', e);
        }
    }
    
    // Update cart cookie
    function updateCartCookie() {
        localStorage.setItem('cart', JSON.stringify(cart));
        setCookie('cart', encodeURIComponent(JSON.stringify(cart)), 30);
    }
    
    // Update cart UI
    function updateCartUI() {
        if (!cartCount) return;
        
        // Update cart count
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
        
        // Update cart items
        if (!cartItems || !subtotalAmount) return;
        
        if (cart.length === 0) {
            cartItems.innerHTML = '<div class="empty-cart">Your cart is empty</div>';
            subtotalAmount.textContent = '€0.00';
        } else {
            let itemsHTML = '';
            let subtotal = 0;
            
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                itemsHTML += `
                    <div class="cart-item" data-id="${item.id}">
                        <div class="cart-item-image">
                            <img src="${item.image}" alt="${item.name}" onerror="this.src='images/placeholder.jpg'">
                        </div>
                        <div class="cart-item-details">
                            <h4 class="cart-item-title">${item.name}</h4>
                            <div class="cart-item-price">€${item.price}</div>
                            <div class="cart-item-quantity">
                                <div class="quantity-btn decrease">-</div>
                                <input type="text" class="quantity-input" value="${item.quantity}" readonly>
                                <div class="quantity-btn increase">+</div>
                            </div>
                        </div>
                        <div class="cart-item-remove" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                `;
            });
            
            cartItems.innerHTML = itemsHTML;
            subtotalAmount.textContent = `€${subtotal.toFixed(2)}`;
            
            // Add event listeners to quantity buttons and remove buttons
            document.querySelectorAll('.cart-item .decrease').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Prevent event from bubbling up to document
                    e.stopPropagation();
                    const itemId = this.closest('.cart-item').getAttribute('data-id');
                    decreaseQuantity(itemId);
                });
            });
            
            document.querySelectorAll('.cart-item .increase').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Prevent event from bubbling up to document
                    e.stopPropagation();
                    const itemId = this.closest('.cart-item').getAttribute('data-id');
                    increaseQuantity(itemId);
                });
            });
            
            document.querySelectorAll('.cart-item-remove').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Prevent event from bubbling up to document
                    e.stopPropagation();
                    const index = parseInt(this.getAttribute('data-index'));
                    removeFromCart(index);
                });
            });
        }
    }
    
    // Add item to cart
    function addToCart(product) {
        const existingItemIndex = cart.findIndex(item => item.id === product.id);
        
        if (existingItemIndex !== -1) {
            // Item already exists, increase quantity
            cart[existingItemIndex].quantity += 1;
        } else {
            // Add new item to cart
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1
            });
        }
        
        updateCartCookie();
        updateCartUI();
    }
    
    // Remove item from cart
    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartCookie();
        updateCartUI();
    }
    
    // Increase item quantity
    function increaseQuantity(itemId) {
        const item = cart.find(item => item.id === itemId);
        if (item) {
            item.quantity += 1;
            updateCartCookie();
            updateCartUI();
        }
    }
    
    // Decrease item quantity
    function decreaseQuantity(itemId) {
        const item = cart.find(item => item.id === itemId);
        if (item && item.quantity > 1) {
            item.quantity -= 1;
            updateCartCookie();
            updateCartUI();
        } else if (item && item.quantity === 1) {
            const index = cart.findIndex(item => item.id === itemId);
            removeFromCart(index);
        }
    }
    
    // Toggle cart dropdown - only add this if not already handled by the page
    // We're using a custom data attribute to check if the click handler is already attached
    if (cartIcons.length > 0) {
        cartIcons.forEach(icon => {
            if (!icon.getAttribute('data-cart-click-initialized')) {
                icon.setAttribute('data-cart-click-initialized', 'true');
                icon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    // Toggle the dropdown
                    if (cartDropdown) {
                        cartDropdown.classList.toggle('show');
                    }
                });
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (cartDropdown && 
            cartDropdown.classList.contains('show') && 
            !cartDropdown.contains(e.target) && 
            !e.target.closest('.cart-icon') && 
            !e.target.closest('.nav-cart-icon')) {
            cartDropdown.classList.remove('show');
        }
    });
    
    // Add event listeners to "Add to Cart" buttons
    if (addToCartButtons.length > 0) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const product = {
                    id: this.getAttribute('data-id'),
                    name: this.getAttribute('data-name'),
                    price: parseFloat(this.getAttribute('data-price')),
                    image: this.getAttribute('data-image')
                };
                
                addToCart(product);
                
                // Don't automatically show the cart dropdown
                // Let the user click the cart icon when they want to see it
            });
        });
    }
    
    // Initialize cart UI
    updateCartUI();
    
    // Check if we're on a confirmation page (order complete)
    if (typeof orderComplete !== 'undefined' && orderComplete === true) {
        // Clear the cart
        cart = [];
        localStorage.removeItem('cart');
        updateCartUI();
    }
}); 