// Cart page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page script loaded');
    
    // Cart container in page
    const cartContainer = document.getElementById('cart-container');
    const checkoutContainer = document.getElementById('checkout-items');
    const cartCount = document.querySelector('.cart-count');
    
    // Load cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Update cart count in header
    if (cartCount) {
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    }
    
    // Render cart for the cart page
    function renderCart() {
        if (!cartContainer) return;
        
        if (cart.length === 0) {
            // Show empty cart message
            cartContainer.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any products to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            `;
        } else {
            // Calculate totals
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            const shipping = 0; // Free shipping for all orders
            const total = subtotal + shipping;
            
            // Generate HTML for cart items
            const cartItemsHTML = cart.map((item, index) => `
                <div class="cart-item" data-id="${item.id}">
                    <div class="cart-product">
                        <div class="cart-product-image">
                            <img src="${item.image}" alt="${item.name}" onerror="this.src='images/placeholder.jpg'">
                        </div>
                        <div class="cart-product-details">
                            <h3>${item.name}</h3>
                            <p>Product ID: ${item.id}</p>
                        </div>
                    </div>
                    <div class="cart-price">€${item.price.toFixed(2)}</div>
                    <div class="cart-quantity">
                        <button class="quantity-btn decrease" data-id="${item.id}">-</button>
                        <input type="text" class="quantity-input" value="${item.quantity}" readonly>
                        <button class="quantity-btn increase" data-id="${item.id}">+</button>
                    </div>
                    <div class="cart-total">€${(item.price * item.quantity).toFixed(2)}</div>
                    <button class="cart-remove" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `).join('');
            
            // Generate full cart HTML
            cartContainer.innerHTML = `
                <div class="cart-items-container">
                    <div class="cart-header">
                        <div>Product</div>
                        <div>Price</div>
                        <div>Quantity</div>
                        <div>Total</div>
                        <div></div>
                    </div>
                    ${cartItemsHTML}
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <!-- Empty column for layout balance -->
                    </div>
                    <div class="col-md-6">
                        <div class="cart-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>€${subtotal.toFixed(2)}</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span>${shipping === 0 ? 'Free' : '€' + shipping.toFixed(2)}</span>
                            </div>
                            <div class="summary-total">
                                <span>Total</span>
                                <span>€${total.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="cart-actions">
                    <a href="shop.php" class="continue-shopping">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            `;
            
            // Add event listeners to buttons
            document.querySelectorAll('.decrease').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-id');
                    decreaseQuantity(itemId);
                });
            });
            
            document.querySelectorAll('.increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-id');
                    increaseQuantity(itemId);
                });
            });
            
            document.querySelectorAll('.cart-remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    removeFromCart(index);
                });
            });
        }
    }
    
    // Render checkout items
    function renderCheckoutItems() {
        if (!checkoutContainer) return;
        
        if (cart.length === 0) {
            window.location.href = 'cart.php'; // Redirect if cart is empty
        } else {
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            
            // Generate HTML for cart items in checkout
            let checkoutItemsHtml = '';
            cart.forEach(item => {
                checkoutItemsHtml += `
                    <div class="checkout-item">
                        <div class="checkout-item-details">
                            <h4>${item.name}</h4>
                            <div class="checkout-item-meta">
                                <span class="cart-item-price">€${item.price.toFixed(2)}</span>
                                <span class="cart-item-quantity">Qty: ${item.quantity}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            checkoutContainer.innerHTML = checkoutItemsHtml;
            
            // Update totals
            const subtotalElement = document.getElementById('checkout-subtotal');
            const totalElement = document.getElementById('checkout-total');
            
            if (subtotalElement) {
                subtotalElement.textContent = `€${subtotal.toFixed(2)}`;
            }
            
            if (totalElement) {
                totalElement.textContent = `€${subtotal.toFixed(2)}`;
            }
        }
    }
    
    // Update cart cookie and localStorage
    function updateCartStorage() {
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart cookie (30 days expiration)
        let expires = "";
        const days = 30;
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
        document.cookie = "cart=" + JSON.stringify(cart) + expires + "; path=/";
    }
    
    // Remove item from cart
    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartStorage();
        if (cartCount) {
            cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
        }
        renderCart();
    }
    
    // Increase item quantity
    function increaseQuantity(itemId) {
        const item = cart.find(item => item.id === itemId);
        if (item) {
            item.quantity += 1;
            updateCartStorage();
            renderCart();
            if (cartCount) {
                cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
            }
        }
    }
    
    // Decrease item quantity
    function decreaseQuantity(itemId) {
        const item = cart.find(item => item.id === itemId);
        if (item && item.quantity > 1) {
            item.quantity -= 1;
            updateCartStorage();
            renderCart();
            if (cartCount) {
                cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
            }
        } else if (item && item.quantity === 1) {
            const index = cart.findIndex(item => item.id === itemId);
            removeFromCart(index);
        }
    }
    
    // Initial render
    renderCart();
    renderCheckoutItems();
}); 