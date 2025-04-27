<?php
// Start session
session_start();

// Initialize variables
$error_message = "";
$success_message = "";
$token = "";
$valid_token = false;
$user_id = 0;

// Check if token is provided in URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pcfy";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        $error_message = "Connection failed: " . $conn->connect_error;
    } else {
        // Make sure the password_reset_tokens table exists
        $sql = "CREATE TABLE IF NOT EXISTS password_reset_tokens (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            token VARCHAR(64) NOT NULL,
            expiry DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";
        $conn->query($sql);
        
        // Verify token and check if it's not expired
        $sql = "SELECT user_id FROM password_reset_tokens WHERE token = ? AND expiry > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            $valid_token = true;
        } else {
            $error_message = "Invalid or expired token. Please request a new password reset link.";
        }
        
        $stmt->close();
    }
} else {
    $error_message = "No reset token provided. Please request a password reset link.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if (empty($password) || empty($confirm_password)) {
        $error_message = "Please enter both password fields.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update the user's password
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            // Delete the used token
            $sql = "DELETE FROM password_reset_tokens WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $success_message = "Your password has been successfully reset. You can now <a href='login.php'>login</a> with your new password.";
            $valid_token = false; // Prevent the form from being shown
        } else {
            $error_message = "Failed to update password. Please try again.";
        }
        
        $stmt->close();
    }
}

// Close the database connection if it was opened
if (isset($conn) && $conn) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reset Password - PCFY</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .reset-password-section {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .password-header {
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
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
        .login-icon {
            color: #fff !important;
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
        <section class="reset-password-section">
            <div class="password-header">
                <h2>Reset Your Password</h2>
                <p>Create a new password for your account</p>
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
            <?php else: ?>
                <?php if($valid_token): ?>
                    <form action="reset_password.php?token=<?php echo $token; ?>" method="post">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" required>
                            </div>
                            <p class="password-requirements">Password must be at least 8 characters long</p>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p><a href="login.php" style="color: #6c7ae0; text-decoration: none;">Back to Login</a></p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-phone"></i> 085 863 9422</p>
                    <p><i class="fas fa-envelope"></i> pcfy.galway@gmail.com</p>
                    <p><i class="fas fa-location-dot"></i> Galway City</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="custom-pcs.php">Custom Builds</a></li>
                        <li><a href="repair-services.php">Repair Services</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="about.php">About Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 PCFY. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="js/main.js"></script>
</body>
</html> 