<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Your Cart - PCFY</title>
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
        
        /* Cart Page Styles */
        .cart-section {
            padding: 40px 0;
        }
        
        .cart-header {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr auto;
            padding: 15px 0;
            border-bottom: 2px solid #e1e5eb;
            font-weight: 600;
            color: #333;
        }
        
        .cart-items-container {
            margin-bottom: 30px;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr auto;
            padding: 20px 0;
            align-items: center;
            border-bottom: 1px solid #e1e5eb;
        }
        
        .cart-product {
            display: flex;
            align-items: center;
        }
        
        .cart-product-image {
            width: 80px;
            height: 80px;
            margin-right: 15px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .cart-product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-product-details h3 {
            margin: 0 0 5px;
            font-size: 16px;
        }
        
        .cart-product-details p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        
        .cart-quantity {
            display: flex;
            align-items: center;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            user-select: none;
            font-size: 16px;
        }
        
        .quantity-input {
            width: 50px;
            height: 30px;
            text-align: center;
            border: 1px solid #ddd;
            margin: 0 5px;
            font-size: 14px;
        }
        
        .cart-price, .cart-total {
            font-weight: 600;
            color: #333;
        }
        
        .cart-remove {
            color: #e74c3c;
            font-size: 18px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 5px;
        }
        
        .cart-empty {
            text-align: center;
            padding: 50px 0;
            color: #6c757d;
        }
        
        .cart-empty i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #e1e5eb;
        }
        
        .cart-empty p {
            margin-bottom: 20px;
        }
        
        .cart-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e1e5eb;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .summary-total {
            font-weight: 700;
            font-size: 18px;
            color: #333;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e1e5eb;
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .continue-shopping {
            display: flex;
            align-items: center;
            color: #6c7ae0;
            text-decoration: none;
            font-weight: 600;
        }
        
        .continue-shopping i {
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            .cart-header {
                display: none;
            }
            
            .cart-item {
                grid-template-columns: 1fr;
                gap: 15px;
                padding: 20px;
                background-color: #f8f9fa;
                border-radius: 8px;
                margin-bottom: 15px;
                border: none;
            }
            
            .cart-product {
                flex-direction: column;
                align-items: flex-start;
                text-align: center;
            }
            
            .cart-product-image {
                margin: 0 auto 15px;
            }
            
            .cart-quantity, .cart-price, .cart-total {
                display: flex;
                justify-content: space-between;
                width: 100%;
                padding-top: 10px;
                border-top: 1px solid #e1e5eb;
            }
            
            .cart-quantity::before {
                content: 'Quantity:';
                font-weight: 600;
            }
            
            .cart-price::before {
                content: 'Price:';
                font-weight: 600;
            }
            
            .cart-total::before {
                content: 'Total:';
                font-weight: 600;
            }
            
            .cart-remove {
                align-self: flex-end;
            }
            
            .cart-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .continue-shopping {
                order: 2;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <!-- Logo -->
            <a href="index.php">
                <img src="Comp 1.gif" alt="Animated Logo" class="logo">
            </a>
            
            <!-- Navigation -->
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="custom-pcs.php">Custom PCs</a></li>
                    <li><a href="repair-services.php">Services</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li class="user-menu">
                        <div class="user-icon">
                            <span><?php echo $firstname; ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="user-dropdown">
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="login.php" class="login-icon">
                            <i class="fas fa-user"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php include 'cart-icon.php'; ?>
                </ul>
            </nav>

            <!-- Hamburger Menu Button -->
            <button class="hamburger">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>
    </header>

    <main>
        <section class="cart-section">
            <div class="container">
                <h1>Your Shopping Cart</h1>
                
                <div id="cart-container">
                    <!-- This will be populated by JavaScript -->
                </div>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?>

    <script src="js/cart-page.js"></script>
</body>
</html> 