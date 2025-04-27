<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Store the fact that they were trying to checkout
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'config.php';
require_once 'email_helper.php';
require_once 'send_order_confirmation.php';
require_once 'stripe-config.php'; // Add Stripe configuration

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Process form submission
$orderComplete = false;
$orderNumber = '';

// Check if payment error exists
$paymentError = '';
if (isset($_SESSION['payment_error']) && !empty($_SESSION['payment_error'])) {
    $paymentError = $_SESSION['payment_error'];
    unset($_SESSION['payment_error']);
}

// Check if cart is empty
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
if (empty($cart)) {
    header('Location: shop.php');
    exit();
}

// Set user menu options
$userMenu = '<a href="login.php" class="login-icon"><i class="fas fa-user"></i></a>';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $firstName = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : 'User';
    $userMenu = '<li class="user-menu">
                    <div class="user-icon">
                        <span>' . $firstName . '</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>';
}

// Get cart total
$cartTotal = 0;
foreach ($cart as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

// Shipping cost
$shippingCost = 0.00; // Free shipping
$orderTotal = $cartTotal + $shippingCost;

// We'll use Stripe for payment processing
// The actual payment processing is moved to stripe-payment.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout - PCFY</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart-dropdown.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-menu {
            position: relative;
            display: inline-block;
        }
        .user-icon {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 5px 12px;
            border-radius: 4px;
            margin-top: -5px;
        }
        .user-icon span {
            margin-right: 8px;
            font-weight: 500;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        .user-icon i {
            color: #fff;
        }
        .user-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            border-radius: 5px;
            width: 180px;
            z-index: 1000;
            display: none;
            margin-top: 5px;
        }
        .user-dropdown.show {
            display: block;
        }
        .user-dropdown a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
        }
        .user-dropdown a:hover {
            background-color: #f8f9fa;
            color: #6c7ae0;
        }
        .user-dropdown a i {
            margin-right: 10px;
            width: 16px;
            text-align: center;
            color: #6c7ae0;
        }
        .login-icon {
            color: #fff !important;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.2);
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }
        
        /* Checkout Styles */
        main {
            min-height: calc(100vh - 200px);
            padding-bottom: 80px;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        @media (min-width: 768px) {
            .checkout-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .checkout-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .checkout-summary {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h3 {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .form-group {
            flex: 1 1 250px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #6c7ae0;
            box-shadow: 0 0 0 3px rgba(108, 122, 224, 0.2);
        }

        .form-group input.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-group input.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #dc3545;
        }

        .form-group input.is-invalid + .invalid-feedback {
            display: block;
        }
        
        .order-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-summary-item:last-child {
            border-bottom: none;
        }
        
        .order-total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .checkout-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        
        .checkout-btn:hover {
            background-color: #45a049;
        }
        
        .cart-items {
            margin-bottom: 20px;
        }
        
        .cart-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .cart-item-details {
            flex-grow: 1;
        }
        
        .cart-item-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            color: #666;
        }
        
        .cart-item-quantity {
            margin-left: auto;
            color: #666;
        }

        /* Footer fix */
        footer {
            margin-top: 60px;
            clear: both;
        }

        /* Order confirmation styles */
        .order-confirmation {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .order-confirmation i {
            color: #4CAF50;
            margin-bottom: 20px;
            display: block;
        }

        .order-confirmation h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .order-confirmation p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .order-number {
            font-size: 20px;
            font-weight: 600;
            color: #2a3990;
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin: 15px 0 25px;
            border: 1px dashed #d1d5db;
        }

        .confirmation-buttons {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .confirmation-buttons a:after {
            display: none !important;
        }

        /* Error Alert */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        /* Stripe button styling */
        .stripe-btn {
            background-color: #6772e5;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        
        .stripe-btn:hover {
            background-color: #5469d4;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main>
        <section>
            <div class="container">
                <?php if ($orderComplete): ?>
                <!-- Order Confirmation -->
                <div class="order-confirmation">
                    <i class="fas fa-check-circle fa-5x"></i>
                    <h2>Thank You for Your Order!</h2>
                    <p>Your order has been placed successfully. We've sent a confirmation email to your email address.</p>
                    <div class="order-number">
                        Order #: <?php echo $orderNumber; ?>
                    </div>
                    <p>We will process your order as soon as possible. You can check the status of your order in your account.</p>
                    <div class="confirmation-buttons">
                        <a href="shop.php" class="btn primary-btn">Continue Shopping</a>
                        <a href="orders.php" class="btn secondary-btn">View My Orders</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="checkout-container">
                    <h1>Checkout</h1>
                    
                    <?php if (!empty($paymentError)): ?>
                    <div class="alert alert-danger"><?php echo $paymentError; ?></div>
                    <?php endif; ?>
                    
                    <div class="checkout-grid">
                        <div>
                            <form id="checkout-form" action="stripe-payment.php" method="POST">
                                <div class="checkout-form form-section">
                                    <h3>Contact Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="email">Email Address*</label>
                                            <input type="email" id="email" name="email" required value="<?php echo $isLoggedIn && isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone Number*</label>
                                            <input type="tel" id="phone" name="phone" required>
                                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="checkout-form form-section">
                                    <h3>Shipping Address</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="first-name">First Name*</label>
                                            <input type="text" id="first-name" name="first_name" required value="<?php echo $isLoggedIn && isset($_SESSION['firstname']) ? $_SESSION['firstname'] : ''; ?>">
                                            <div class="invalid-feedback">Please enter your first name.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="last-name">Last Name*</label>
                                            <input type="text" id="last-name" name="last_name" required value="<?php echo $isLoggedIn && isset($_SESSION['lastname']) ? $_SESSION['lastname'] : ''; ?>">
                                            <div class="invalid-feedback">Please enter your last name.</div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="address">Address*</label>
                                            <input type="text" id="address" name="address" required>
                                            <div class="invalid-feedback">Please enter your address.</div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="city">City*</label>
                                            <input type="text" id="city" name="city" required>
                                            <div class="invalid-feedback">Please enter your city.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="postal-code">Postal Code*</label>
                                            <input type="text" id="postal-code" name="postal_code" required>
                                            <div class="invalid-feedback">Please enter your postal code.</div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="county">County*</label>
                                            <select id="county" name="county" required>
                                                <option value="" disabled selected>Select County</option>
                                                <option value="Antrim">Antrim</option>
                                                <option value="Armagh">Armagh</option>
                                                <option value="Carlow">Carlow</option>
                                                <option value="Cavan">Cavan</option>
                                                <option value="Clare">Clare</option>
                                                <option value="Cork">Cork</option>
                                                <option value="Derry">Derry</option>
                                                <option value="Donegal">Donegal</option>
                                                <option value="Down">Down</option>
                                                <option value="Dublin">Dublin</option>
                                                <option value="Fermanagh">Fermanagh</option>
                                                <option value="Galway">Galway</option>
                                                <option value="Kerry">Kerry</option>
                                                <option value="Kildare">Kildare</option>
                                                <option value="Kilkenny">Kilkenny</option>
                                                <option value="Laois">Laois</option>
                                                <option value="Leitrim">Leitrim</option>
                                                <option value="Limerick">Limerick</option>
                                                <option value="Longford">Longford</option>
                                                <option value="Louth">Louth</option>
                                                <option value="Mayo">Mayo</option>
                                                <option value="Meath">Meath</option>
                                                <option value="Monaghan">Monaghan</option>
                                                <option value="Offaly">Offaly</option>
                                                <option value="Roscommon">Roscommon</option>
                                                <option value="Sligo">Sligo</option>
                                                <option value="Tipperary">Tipperary</option>
                                                <option value="Tyrone">Tyrone</option>
                                                <option value="Waterford">Waterford</option>
                                                <option value="Westmeath">Westmeath</option>
                                                <option value="Wexford">Wexford</option>
                                                <option value="Wicklow">Wicklow</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="country" value="IE">
                                    </div>
                                </div>

                                <!-- Removed card details collection since Stripe will handle this -->
                            </form>
                        </div>

                        <div class="checkout-summary">
                            <h3>Order Summary</h3>
                            <div class="cart-items" id="checkout-items">
                                <!-- Cart items will be loaded here -->
                            </div>
                            <div class="order-summary-item">
                                <span>Subtotal</span>
                                <span id="checkout-subtotal">€<?php echo number_format($cartTotal, 2); ?></span>
                            </div>
                            <div class="order-summary-item">
                                <span>Shipping</span>
                                <span id="checkout-shipping">Free</span>
                            </div>
                            <div class="order-summary-item">
                                <span>Tax</span>
                                <span id="checkout-tax">€0.00</span>
                            </div>
                            <div class="order-summary-item order-total">
                                <span>Total</span>
                                <span id="checkout-total">€<?php echo number_format($orderTotal, 2); ?></span>
                            </div>
                            <button type="submit" form="checkout-form" class="stripe-btn">
                                <i class="fas fa-credit-card"></i> Pay with Stripe
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!$orderComplete): ?>
            // Cart and checkout functionality
            const cartCount = document.querySelector('.cart-count');
            const summaryItems = document.getElementById('checkout-items');
            const summarySubtotal = document.getElementById('checkout-subtotal');
            const summaryShipping = document.getElementById('checkout-shipping');
            const summaryTotal = document.getElementById('checkout-total');
            
            // Load cart from localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // If cart is empty, redirect to cart page
            if (cart.length === 0) {
                window.location.href = 'cart.php';
            }
            
            // Create a hidden input field to store cart data
            const cartInput = document.createElement('input');
            cartInput.type = 'hidden';
            cartInput.name = 'cart_data';
            cartInput.value = JSON.stringify(cart);
            
            // Get the form element
            const checkoutForm = document.getElementById('checkout-form');
            checkoutForm.appendChild(cartInput);
            
            // Update cart count in header
            cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
            
            // Calculate totals
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            // Shipping is free for all items
            const shipping = 0;
            const total = subtotal + shipping;
            
            // Update summary
            summarySubtotal.textContent = `€${subtotal.toFixed(2)}`;
            summaryShipping.textContent = 'Free';
            summaryTotal.textContent = `€${total.toFixed(2)}`;
            
            // Generate summary items HTML
            let summaryItemsHTML = '';
            cart.forEach(item => {
                summaryItemsHTML += `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="cart-item-price">€${item.price.toFixed(2)}</div>
                        </div>
                        <div class="cart-item-quantity">
                            <span>Qty: ${item.quantity}</span>
                        </div>
                    </div>
                `;
            });
            
            summaryItems.innerHTML = summaryItemsHTML;
            
            // Basic form validation
            checkoutForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Get all required inputs
                const requiredInputs = checkoutForm.querySelectorAll('[required]');
                
                // Check each required input
                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                // Validate email format
                const emailInput = document.getElementById('email');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailInput.value && !emailPattern.test(emailInput.value)) {
                    emailInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                // If form is not valid, prevent submission
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to the first invalid input
                    const firstInvalid = checkoutForm.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
            
            // Real-time validation on input change
            const inputs = checkoutForm.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                    
                    // Specific validations
                    if (this.id === 'email') {
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (this.value && !emailPattern.test(this.value)) {
                            this.classList.add('is-invalid');
                        }
                    }
                });
            });
            <?php endif; ?>
        });
    </script>
</body>
</html> 