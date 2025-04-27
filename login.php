<?php
// Start session
session_start();

// Check if there are any error messages from failed login attempts
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : "";
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : "";

// Clear any session messages after they've been displayed
unset($_SESSION['error']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - PCFY</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-section {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .input-with-icon {
            position: relative;
        }
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c7ae0;
        }
        .input-with-icon input {
            padding: 12px 12px 12px 45px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .input-with-icon input:focus {
            border-color: #6c7ae0;
            box-shadow: 0 0 0 2px rgba(108, 122, 224, 0.2);
        }
        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert-error {
            background-color: rgba(255, 0, 0, 0.1);
            color: #a94442;
            border: 1px solid rgba(255, 0, 0, 0.2);
        }
        .alert-success {
            background-color: rgba(0, 128, 0, 0.1);
            color: #3c763d;
            border: 1px solid rgba(0, 128, 0, 0.2);
        }
        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
        }
        .forgot-password a {
            color: #6c7ae0;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .forgot-password a:hover {
            text-decoration: underline;
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
            
            <!-- Navigation with login icon -->
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="custom-pcs.php">Custom PCs</a></li>
                    <li><a href="repair-services.php">Services</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li>
                        <a href="login.php" class="login-icon">
                            <i class="fas fa-user"></i>
                        </a>
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

    <main>
        <section class="login-section">
            <div class="login-header">
                <h2>Login to Your Account</h2>
                <p>Enter your credentials to access your account</p>
            </div>
            
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form action="login_handler.php" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="forgot-password">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <p>Don't have an account? <a href="register.php" style="color: #6c7ae0; text-decoration: none;">Register here</a></p>
                </div>
            </form>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html> 