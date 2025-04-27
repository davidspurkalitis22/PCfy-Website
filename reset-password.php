<?php
// Start session
session_start();

// Enable full error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config.php';

// Set page title
$pageTitle = 'Reset Password';

// Define variables and initialize with empty values
$code = $password = $confirm_password = "";
$code_err = $password_err = $confirm_password_err = "";
$success_message = $error_message = "";
$email = "";

// Check if email exists in session or was passed as GET parameter
if (isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
} elseif (isset($_GET['email'])) {
    // Allow email to be passed via GET parameter as a fallback
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
} elseif (isset($_POST['email_hidden'])) {
    // Also check for email in POST data from our hidden field
    $email = filter_var($_POST['email_hidden'], FILTER_SANITIZE_EMAIL);
} else {
    // Display form to enter email manually
    $entered_email = false;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $entered_email = true;
        } else {
            $error_message = "Please enter a valid email address.";
        }
    }
    
    if (!$entered_email) {
        // Include header
        include 'header.php';
        ?>
        <main>
            <div class="container">
                <div class="auth-form-container">
                    <h2>Reset Password</h2>
                    <p>Please enter your email address first.</p>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Continue</button>
                        </div>
                        
                        <div class="form-footer">
                            <a href="login.php">Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <?php
        include 'footer.php';
        exit();
    }
}

// Now process the reset form if we have an email
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['code'])) {
    
    // Validate verification code
    if (empty(trim($_POST["code"]))) {
        $code_err = "Please enter the verification code.";
    } else {
        $code = trim($_POST["code"]);
        
        // Check if code is valid and not expired
        $sql = "SELECT id, reset_code, reset_expiry FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($user_id, $reset_code, $reset_expiry);
                    $stmt->fetch();
                    
                    // For debugging
                    error_log("Code Comparison - DB: '$reset_code' (type: " . gettype($reset_code) . "), User: '$code' (type: " . gettype($code) . ")");
                    
                    // Check if code matches and is not expired (try both loose and strict comparison)
                    $loose_match = ($reset_code == $code);
                    $strict_match = ($reset_code === $code);
                    $not_expired = (strtotime($reset_expiry) > time());
                    
                    error_log("Match results - Loose: " . ($loose_match ? 'true' : 'false') . 
                              ", Strict: " . ($strict_match ? 'true' : 'false') . 
                              ", Not expired: " . ($not_expired ? 'true' : 'false'));
                    
                    // Use loose comparison as the reset_code might be stored as a different type in DB
                    if ($loose_match && $not_expired) {
                        // Code is valid, validate password
                        if (empty(trim($_POST["password"]))) {
                            $password_err = "Please enter a password.";
                        } elseif (strlen(trim($_POST["password"])) < 8) {
                            $password_err = "Password must have at least 8 characters.";
                        } else {
                            $password = trim($_POST["password"]);
                        }
                        
                        // Validate confirm password
                        if (empty(trim($_POST["confirm_password"]))) {
                            $confirm_password_err = "Please confirm the password.";
                        } else {
                            $confirm_password = trim($_POST["confirm_password"]);
                            if (empty($password_err) && ($password != $confirm_password)) {
                                $confirm_password_err = "Passwords did not match.";
                            }
                        }
                        
                        // Check input errors before updating the database
                        if (empty($password_err) && empty($confirm_password_err)) {
                            // Update password
                            $update_sql = "UPDATE users SET password = ?, reset_code = NULL, reset_expiry = NULL WHERE id = ?";
                            
                            if ($update_stmt = $conn->prepare($update_sql)) {
                                $update_stmt->bind_param("si", $param_password, $user_id);
                                
                                // Set parameters
                                $param_password = password_hash($password, PASSWORD_DEFAULT);
                                
                                if ($update_stmt->execute()) {
                                    // Password updated successfully
                                    $success_message = "Password has been reset successfully. You can now login with your new password.";
                                    
                                    // Clear session variables
                                    unset($_SESSION['reset_email']);
                                    
                                    // Redirect to login page after 3 seconds
                                    header("refresh:3;url=login.php");
                                } else {
                                    $error_message = "Oops! Something went wrong. Please try again later.";
                                    error_log("Error updating password: " . $update_stmt->error);
                                }
                                $update_stmt->close();
                            }
                        }
                    } else {
                        // Invalid or expired code
                        if (strtotime($reset_expiry) <= time()) {
                            $code_err = "The verification code has expired. Please request a new one.";
                        } else {
                            $code_err = "The verification code is invalid.";
                        }
                    }
                } else {
                    // Email not found
                    $error_message = "Invalid request. Please start the password reset process again.";
                    header("refresh:2;url=forgot-password.php");
                }
            } else {
                $error_message = "Oops! Something went wrong. Please try again later.";
                error_log("Error checking reset code: " . $stmt->error);
            }
            $stmt->close();
        }
    }
    
    // Close connection
    $conn->close();
}

// Include header
include 'header.php';
?>

<main>
    <div class="container">
        <div class="auth-form-container">
            <h2>Reset Password</h2>
            <p>Enter the verification code sent to your email and your new password.</p>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?><?php echo isset($email) ? '?email=' . urlencode($email) : ''; ?>" method="post">
                <input type="hidden" name="email_hidden" value="<?php echo htmlspecialchars($email); ?>">
                <div class="form-group">
                    <label for="code">Verification Code</label>
                    <input type="text" id="code" name="code" class="form-control <?php echo (!empty($code_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $code; ?>" required>
                    <?php if (!empty($code_err)): ?>
                        <div class="invalid-feedback">
                            <?php echo $code_err; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" required>
                    <?php if (!empty($password_err)): ?>
                        <div class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" required>
                    <?php if (!empty($confirm_password_err)): ?>
                        <div class="invalid-feedback">
                            <?php echo $confirm_password_err; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </div>
                
                <div class="form-footer">
                    <a href="login.php">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
    .auth-form-container {
        max-width: 500px;
        margin: 50px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }
    
    .auth-form-container h2 {
        text-align: center;
        margin-bottom: 10px;
        color: #2a3990;
    }
    
    .auth-form-container p {
        text-align: center;
        margin-bottom: 30px;
        color: #6c757d;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #495057;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 15px;
        font-size: 16px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .is-invalid {
        border-color: #dc3545;
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 5px;
        font-size: 14px;
        color: #dc3545;
    }
    
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 10px 15px;
        font-size: 16px;
        line-height: 1.5;
        border-radius: 4px;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        cursor: pointer;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #2a3990;
        border-color: #2a3990;
    }
    
    .btn-primary:hover {
        background-color: #1e2a6b;
        border-color: #1e2a6b;
    }
    
    .btn-block {
        display: block;
        width: 100%;
    }
    
    .form-footer {
        text-align: center;
        margin-top: 15px;
    }
    
    .form-footer a {
        color: #6c7ae0;
        text-decoration: none;
    }
    
    .form-footer a:hover {
        text-decoration: underline;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
</style>

<?php
// Include footer
include 'footer.php';
?> 