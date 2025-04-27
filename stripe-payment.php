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

// Include database connection and configuration
try {
    require_once 'config.php';
    require_once 'stripe-config.php';
    require_once 'stripe-init.php';

    // Check if user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        $_SESSION['redirect_after_login'] = 'checkout.php';
        // Clean any output buffer
        if (ob_get_length()) ob_end_clean();
        header("Location: login.php");
        exit();
    }

    // Check if cart is empty
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
    if (empty($cart)) {
        // Clean any output buffer
        if (ob_get_length()) ob_end_clean();
        header('Location: shop.php');
        exit();
    }

    // Initialize variables
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $county = $_POST['county'] ?? '';
        $postal_code = $_POST['postal_code'] ?? '';
        $country = $_POST['country'] ?? '';
        
        // Store checkout data for error recovery
        $_SESSION['checkout_data'] = [
            'email' => $email,
            'phone' => $phone,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'address' => $address,
            'city' => $city,
            'county' => $county,
            'postal_code' => $postal_code,
            'country' => $country
        ];
        
        // Get cart data
        if (isset($_POST['cart_data']) && !empty($_POST['cart_data'])) {
            $cart = json_decode($_POST['cart_data'], true);
        } else {
            $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        }
        
        // Verify cart is not empty
        if (empty($cart)) {
            $error = "Your cart appears to be empty. Please add items to your cart and try again.";
        } else {
            try {
                // Calculate order total
                $orderTotal = 0;
                $lineItems = [];
                foreach ($cart as $item) {
                    // For testing: Change to 1 cent but keep original price for database
                    $originalPrice = $item['price'] * $item['quantity'];
                    $orderTotal += $originalPrice;
                    
                    // Create line items for Stripe - Set all prices to 50 cents for testing (Stripe minimum)
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => STRIPE_CURRENCY,
                            'product_data' => [
                                'name' => $item['name'] . ' (TEST: 50 cents)',
                                // Only include description if it exists and is not empty
                                'description' => isset($item['description']) && !empty($item['description']) ? $item['description'] : 'Product from PCFY (TEST)',
                            ],
                            'unit_amount' => 50, // 50 cents for testing (Stripe minimum for EUR)
                        ],
                        'quantity' => 1, // Single quantity for testing
                    ];
                }
                
                // Set Stripe API key
                stripe_set_api_key(STRIPE_API_KEY);
                
                // Create a checkout session
                $checkout_session = stripe_checkout_session_create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'customer_email' => $email,
                    'success_url' => PAYMENT_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => PAYMENT_CANCEL_URL,
                    'metadata' => [
                        'customer_name' => $first_name . ' ' . $last_name,
                        'customer_email' => $email,
                        'customer_phone' => $phone,
                        'shipping_address' => $address,
                        'shipping_city' => $city,
                        'shipping_county' => $county,
                        'shipping_postal_code' => $postal_code,
                        'shipping_country' => $country,
                    ],
                ]);
                
                // Store session ID in session for later use
                $_SESSION['stripe_session_id'] = $checkout_session->id;
                
                // Store order details in session
                $_SESSION['order_details'] = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'county' => $county,
                    'postal_code' => $postal_code,
                    'country' => $country,
                    'order_total' => $orderTotal,
                    'cart' => $cart
                ];
                
                // Make sure to store the user's login status
                $_SESSION['payment_in_progress'] = true;
                
                // Clean any output buffer
                if (ob_get_length()) ob_end_clean();
                
                // Redirect to Stripe Checkout
                header("HTTP/1.1 303 See Other");
                header("Location: " . $checkout_session->url);
                exit();
                
            } catch (Exception $e) {
                $error = "Error creating payment: " . $e->getMessage();
                error_log("Stripe error: " . $e->getMessage());
            }
        }
    } else {
        $error = "Invalid request method. Please use the checkout form.";
    }

    // If we reached here, there was an error
    $_SESSION['payment_error'] = $error;
    
    // Clean any output buffer
    if (ob_get_length()) ob_end_clean();
    
    header("Location: checkout.php");
    exit();
    
} catch (Exception $e) {
    // Log any unexpected exceptions
    error_log("Critical error in stripe-payment.php: " . $e->getMessage());
    
    // Set error message
    $_SESSION['payment_error'] = "An unexpected error occurred. Please try again or contact support.";
    
    // Clean any output buffer
    if (ob_get_length()) ob_end_clean();
    
    // Redirect back to checkout
    header("Location: checkout.php");
    exit();
}
?> 