<?php
// Start session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Get user information from session
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
$email = $_SESSION['email'];
$userid = $_SESSION['userid'];

// Initialize variables
$phone = "";
$address = "";
$error_message = "";
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : "";

// Clear any session success message after it's been displayed
unset($_SESSION['success']);

// Include database configuration
require_once 'config.php';

// Get full user details from database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $phone = $user['phone'];
    $address = $user['address'];
} else {
    $error_message = "User information could not be retrieved.";
}

// Close the statement
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Profile - PCFY</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-section {
            max-width: 800px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #6c7ae0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: #fff;
            font-size: 40px;
        }
        .profile-details h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .profile-details p {
            margin: 0;
            color: #666;
        }
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 30px;
        }
        .profile-tab {
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        .profile-tab.active {
            border-bottom: 2px solid #6c7ae0;
            color: #6c7ae0;
        }
        .profile-tab:hover {
            color: #6c7ae0;
        }
        .profile-form-group {
            margin-bottom: 20px;
        }
        .profile-form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .profile-form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
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
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <!-- Logo -->
            <a href="index.php">
                <img src="Comp 1.gif" alt="Animated Logo" class="logo">
            </a>
            
            <!-- Navigation with user menu -->
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="custom-pcs.php">Custom PCs</a></li>
                    <li><a href="repair-services.php">Services</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
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
        <section class="profile-section">
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
            
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($firstname, 0, 1)); ?>
                </div>
                <div class="profile-details">
                    <h1><?php echo $firstname . ' ' . $lastname; ?></h1>
                    <p><?php echo $email; ?></p>
                </div>
            </div>
            
            <div class="profile-tabs">
                <div class="profile-tab active">Account Information</div>
            </div>
            
            <form action="update_profile.php" method="post">
                <div class="profile-form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
                </div>
                
                <div class="profile-form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" required>
                </div>
                
                <div class="profile-form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                
                <div class="profile-form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>" required>
                </div>
                
                <div class="profile-form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo $address; ?>" required>
                </div>
                
                <div class="profile-form-group">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </section>
    </main>

<?php include 'footer.php'; ?>

    <script>
        // Toggle between profile tabs
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.profile-tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html> 