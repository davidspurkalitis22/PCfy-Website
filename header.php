<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-40KKZDD1CN"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-40KKZDD1CN');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - PCFY' : 'PCFY'; ?></title>
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
        /* Cart icon styles */
        .cart-icon {
            display: inline-block !important;
            position: relative;
            margin-left: 20px;
            cursor: pointer;
        }
        .cart-icon i {
            font-size: 24px;
            color: #fff;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .cart-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            width: 320px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            border-radius: 5px;
            padding: 15px;
            z-index: 1000;
            margin-top: 5px;
            display: none;
        }
        .cart-dropdown.show {
            display: block;
        }
        .cart-items {
            max-height: 320px;
            overflow-y: auto;
        }
        .empty-cart {
            text-align: center;
            padding: 15px 0;
            color: #6c757d;
        }
        .cart-subtotal {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-weight: 600;
            border-top: 1px solid #eee;
        }
        .cart-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .cart-actions a {
            width: 100%;
            text-align: center;
            display: block;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
        }
        .cart-actions a.btn-primary {
            margin-bottom: 5px;
            background: #00CCF5;
            color: #fff;
        }
        .cart-actions a.btn-secondary {
            background: transparent;
            color: #333;
            border: 1px solid #ddd;
        }
        
        /* Mobile improvements */
        @media (max-width: 768px) {
            .user-menu, .cart-icon {
                margin-left: 10px;
            }
            nav ul {
                display: flex;
                align-items: center;
            }
        }
    </style>
    <?php if (isset($additionalStyles)) echo $additionalStyles; ?>
</head>
<body>
    <header>
        <div class="header-container">
            <!-- Logo -->
            <a href="index.php">
                <img src="Comp 1.gif" alt="Animated Logo" class="logo">
            </a>
            
            <!-- Navigation with login icon or user menu -->
            <nav>
                <ul>
                    <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                    <li><a href="custom-pcs.php" <?php echo basename($_SERVER['PHP_SELF']) == 'custom-pcs.php' ? 'class="active"' : ''; ?>>Custom PCs</a></li>
                    <li><a href="repair-services.php" <?php echo basename($_SERVER['PHP_SELF']) == 'repair-services.php' ? 'class="active"' : ''; ?>>Services</a></li>
                    <li><a href="shop.php" <?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'class="active"' : ''; ?>>Shop</a></li>
                    <li><a href="about.php" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>About</a></li>
                    <li><a href="contact.php" <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'class="active"' : ''; ?>>Contact</a></li>
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
                    <!-- Shopping Cart Icon -->
                    <li class="cart-icon" id="header-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                        <div class="cart-dropdown">
                            <div class="cart-items">
                                <div class="empty-cart">Your cart is empty</div>
                            </div>
                            <div class="cart-subtotal">
                                <span>Subtotal:</span>
                                <span class="subtotal-amount">€0.00</span>
                            </div>
                            <div class="cart-actions">
                                <a href="cart.php" class="btn btn-primary">View Cart</a>
                                <a href="checkout.php" class="btn btn-secondary">Checkout</a>
                            </div>
                        </div>
                    </li>
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
</body>
</html> 