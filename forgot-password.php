<?php
// Start session
session_start();

// Enable full error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config.php';
require_once 'email_helper.php';

// Set page title
$pageTitle = 'Forgot Password';

// Define variables and initialize with empty values
$email = "";
$email_err = "";
$success_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate email
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email address.";
        } else {
            $email = trim($_POST["email"]);
            
            // Check if email exists
            $sql = "SELECT id FROM users WHERE email = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $param_email);
                $param_email = $email;
                
                if ($stmt->execute()) {
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 1) {
                        // Email exists, generate reset code
                        $reset_code = mt_rand(100000, 999999); // 6-digit code
                        $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Code expires in 1 hour
                        
                        // Update user record with reset code
                        $update_sql = "UPDATE users SET reset_code = ?, reset_expiry = ? WHERE email = ?";
                        
                        if ($update_stmt = $conn->prepare($update_sql)) {
                            $update_stmt->bind_param("sss", $reset_code, $reset_expiry, $email);
                            
                            if ($update_stmt->execute()) {
                                // Send reset email
                                $subject = "Password Reset Code - PCFY";
                                $message = "
                                <html>
                                <head>
                                    <title>Password Reset Code</title>
                                </head>
                                <body>
                                    <div style='max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;'>
                                        <div style='text-align: center; margin-bottom: 20px;'>
                                            <img src='https://pcfy.com/logo.png' alt='PCFY Logo' style='max-width: 150px;'>
                                        </div>
                                        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px;'>
                                            <h2 style='color: #2a3990; margin-top: 0;'>Password Reset Code</h2>
                                            <p>You recently requested to reset your password for your PCFY account. Use the following code to complete the process:</p>
                                            <div style='background-color: #e9ecef; padding: 15px; font-size: 24px; font-weight: bold; text-align: center; margin: 20px 0; letter-spacing: 5px; border-radius: 5px;'>
                                                $reset_code
                                            </div>
                                            <p>This code is valid for 1 hour. If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
                                        </div>
                                        <div style='text-align: center; margin-top: 20px; color: #6c757d; font-size: 12px;'>
                                            <p>© " . date('Y') . " PCFY. All rights reserved.</p>
                                        </div>
                                    </div>
                                </body>
                                </html>
                                ";
                                
                                try {
                                    // Generate plain text version
                                    $text_message = "You recently requested to reset your password for your PCFY account. Your verification code is: $reset_code. This code is valid for 1 hour.";
                                    
                                    // Send email with all required parameters
                                    $result = send_email(
                                        $email, 
                                        "PCFY User", 
                                        $subject, 
                                        $message, 
                                        $text_message, 
                                        SITE_EMAIL, 
                                        "PCFY Password Reset"
                                    );
                                    
                                    if ($result['success']) {
                                        $success_message = "A password reset code has been sent to your email address.";
                                        
                                        // Store email in session for the reset page
                                        $_SESSION['reset_email'] = $email;
                                        
                                        // Don't redirect automatically - instead show success message
                                        // and provide a button to continue
                                        $show_continue_button = true;
                                    } else {
                                        $email_err = "Error sending reset email. Please try again later.";
                                        error_log("Error sending reset email: " . $result['message']);
                                    }
                                } catch (Exception $e) {
                                    $email_err = "Error sending reset email. Please try again later.";
                                    error_log("Error sending reset email: " . $e->getMessage());
                                }
                            } else {
                                $email_err = "Oops! Something went wrong. Please try again later.";
                                error_log("Error updating reset code: " . $update_stmt->error);
                            }
                            $update_stmt->close();
                        }
                    } else {
                        // Email doesn't exist, but don't reveal this for security reasons
                        $success_message = "If your email is registered, a password reset code has been sent.";
                        $show_continue_button = true;
                    }
                } else {
                    $email_err = "Oops! Something went wrong. Please try again later.";
                    error_log("Error checking email: " . $stmt->error);
                }
                $stmt->close();
            }
        }
    } catch (Exception $e) {
        $email_err = "An unexpected error occurred. Please try again later.";
        error_log("Unexpected error in forgot-password.php: " . $e->getMessage());
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
            <h2>Forgot Password</h2>
            <p>Enter your email address to receive a password reset code.</p>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
                
                <?php if (isset($show_continue_button) && $show_continue_button): ?>
                    <div style="text-align: center; margin: 20px 0;">
                        <p>Check your email for the reset code.</p>
                        <a href="reset-password.php<?php echo isset($_SESSION['reset_email']) ? '?email=' . urlencode($_SESSION['reset_email']) : ''; ?>" class="btn btn-primary">Continue to Reset Password</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php if (!empty($email_err)): ?>
                    <div class="alert alert-danger">
                        <?php echo $email_err; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Send Reset Code</button>
                    </div>
                    
                    <div class="form-footer">
                        <a href="login.php">Back to Login</a>
                    </div>
                </form>
            <?php endif; ?>
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