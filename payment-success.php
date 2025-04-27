<?php
// Start session
session_start();

// Start output buffering to prevent partial outputs
ob_start();

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    // Include database connection and config files
    require_once 'config.php';
    require_once 'stripe-config.php';
    require_once 'stripe-init.php';
    require_once 'email_helper.php';
    require_once 'send_order_confirmation.php';

    // Check if session ID exists
    if (!isset($_GET['session_id']) || empty($_GET['session_id'])) {
        // Clean any output buffer
        if (ob_get_length()) ob_end_clean();
        header('Location: shop.php');
        exit();
    }

    // Check if order details exist in session
    if (!isset($_SESSION['order_details']) || empty($_SESSION['order_details'])) {
        // Clean any output buffer
        if (ob_get_length()) ob_end_clean();
        header('Location: shop.php');
        exit();
    }

    // Get session ID from URL
    $session_id = $_GET['session_id'];

    // Retrieve stripe session
    try {
        // Set Stripe API key
        stripe_set_api_key(STRIPE_API_KEY);
        
        // Retrieve the session
        $session = stripe_checkout_session_retrieve($session_id);
        
        // Verify payment was successful
        if ($session->payment_status === 'paid') {
            // Get order details from session
            $orderDetails = $_SESSION['order_details'];
            $cart = $orderDetails['cart'];
            
            // Generate a unique order number
            $orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            
            try {
                // Begin transaction
                $pdo->beginTransaction();
                
                // Insert order into database
                // NOTE: We're still storing the original order total, not the 1 cent test amount
                $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, first_name, last_name, email, phone, address, city, county, postal_code, country, payment_method, order_date, order_status, order_total) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'paid', ?)");
                
                // If user is logged in, associate order with user
                $userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;
                
                $stmt->execute([
                    $orderNumber,
                    $userId,
                    $orderDetails['first_name'],
                    $orderDetails['last_name'],
                    $orderDetails['email'],
                    $orderDetails['phone'],
                    $orderDetails['address'],
                    $orderDetails['city'],
                    $orderDetails['county'],
                    $orderDetails['postal_code'],
                    $orderDetails['country'],
                    'stripe', // Payment method
                    $orderDetails['order_total']
                ]);
                
                $orderId = $pdo->lastInsertId();
                
                // Insert order items
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, order_number, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
                
                foreach ($cart as $item) {
                    $stmt->execute([
                        $orderId,
                        $orderNumber,
                        $item['id'],
                        $item['name'],
                        $item['quantity'],
                        $item['price']
                    ]);
                }
                
                // Commit transaction
                $pdo->commit();
                
                // Send order confirmation email
                try {
                    // Get order items for email
                    $orderItemsForEmail = [];
                    foreach ($cart as $item) {
                        $orderItemsForEmail[] = [
                            'product_name' => $item['name'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price']
                        ];
                    }
                    
                    // Send confirmation email to customer
                    $emailResult = send_order_confirmation(
                        $orderNumber,
                        $orderDetails['first_name'],
                        $orderDetails['last_name'],
                        $orderDetails['email'],
                        $orderDetails['order_total'],
                        $orderItemsForEmail,
                        $orderDetails['address'],
                        $orderDetails['city'],
                        $orderDetails['county'],
                        $orderDetails['postal_code'],
                        'stripe'
                    );
                    
                    // Send notification to admin
                    $adminEmailResult = send_order_notification_to_admin(
                        $orderNumber,
                        $orderDetails['first_name'],
                        $orderDetails['last_name'],
                        $orderDetails['email'],
                        $orderDetails['order_total'],
                        $orderItemsForEmail
                    );
                    
                } catch (Exception $e) {
                    error_log("Error sending order emails: " . $e->getMessage());
                    // Continue with order completion even if email fails
                }
                
                // Clear the cart and session variables
                setcookie('cart', '', time() - 3600, '/');
                
                // Only unset specific session variables related to the order
                // DO NOT unset the entire session or session_destroy() as this will log the user out
                unset($_SESSION['order_details']);
                unset($_SESSION['stripe_session_id']);
                unset($_SESSION['payment_in_progress']);
                // We keep $_SESSION['loggedin'] and $_SESSION['userid'] intact
                
                // Set order information in session for confirmation page
                $_SESSION['order'] = [
                    'orderNumber' => $orderNumber,
                    'orderDate' => date('Y-m-d H:i:s'), // Current date and time
                    'orderTotal' => $orderDetails['order_total'],
                    'items' => $cart
                ];
                
                // Set success message
                $_SESSION['payment_success'] = true;
                $_SESSION['order_number'] = $orderNumber;
                
                // Clean any output buffer
                if (ob_get_length()) ob_end_clean();
                
                // Redirect to confirmation page with proper path
                header('Location: confirmation.php');
                exit();
                
            } catch (PDOException $e) {
                // Rollback transaction on error
                $pdo->rollBack();
                error_log("Database error during payment processing: " . $e->getMessage());
                $_SESSION['payment_error'] = "An error occurred while processing your order. Please contact support.";
                
                // Clean any output buffer
                if (ob_get_length()) ob_end_clean();
                
                header('Location: checkout.php');
                exit();
            }
            
        } else {
            // Payment was not successful
            $_SESSION['payment_error'] = "Payment was not successful. Please try again.";
            
            // Clean any output buffer
            if (ob_get_length()) ob_end_clean();
            
            header('Location: checkout.php');
            exit();
        }
        
    } catch (Exception $e) {
        error_log("Stripe error: " . $e->getMessage());
        $_SESSION['payment_error'] = "An error occurred while processing your payment. Please try again.";
        
        // Clean any output buffer
        if (ob_get_length()) ob_end_clean();
        
        header('Location: checkout.php');
        exit();
    }
} catch (Exception $e) {
    // Log any unexpected exceptions
    error_log("Critical error in payment-success.php: " . $e->getMessage());
    
    // Set error message
    $_SESSION['payment_error'] = "An unexpected error occurred. Please try again or contact support.";
    
    // Clean any output buffer
    if (ob_get_length()) ob_end_clean();
    
    // Redirect back to checkout
    header('Location: checkout.php');
    exit();
}
?> 