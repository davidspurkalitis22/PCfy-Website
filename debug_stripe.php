<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include configuration files
require_once 'config.php';
require_once 'stripe-config.php';
require_once 'stripe-init.php';

echo "<h1>Stripe Payment Debug</h1>";

// Check if cURL is installed and enabled
echo "<h2>PHP and cURL Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

if (function_exists('curl_version')) {
    $curl_info = curl_version();
    echo "<p style='color:green'>✓ cURL is installed</p>";
    echo "<p>cURL Version: " . $curl_info['version'] . "</p>";
    echo "<p>SSL Version: " . $curl_info['ssl_version'] . "</p>";
} else {
    echo "<p style='color:red'>✗ cURL is NOT installed or enabled</p>";
    echo "<p>Stripe payments require cURL to be installed and enabled.</p>";
}

// Check if stripe config is available
echo "<h2>Stripe Configuration</h2>";
if (defined('STRIPE_API_KEY') && !empty(STRIPE_API_KEY)) {
    echo "<p style='color:green'>✓ Stripe API Key is defined</p>";
    // Mask the key for security
    $masked_key = substr(STRIPE_API_KEY, 0, 4) . '...' . substr(STRIPE_API_KEY, -4);
    echo "<p>API Key: " . $masked_key . "</p>";
} else {
    echo "<p style='color:red'>✗ Stripe API Key is missing or empty</p>";
}

if (defined('STRIPE_PUBLISHABLE_KEY') && !empty(STRIPE_PUBLISHABLE_KEY)) {
    echo "<p style='color:green'>✓ Stripe Publishable Key is defined</p>";
} else {
    echo "<p style='color:red'>✗ Stripe Publishable Key is missing or empty</p>";
}

// Test Stripe API connection
echo "<h2>Stripe API Connection Test</h2>";
try {
    // Set Stripe API key
    stripe_set_api_key(STRIPE_API_KEY);
    
    // Make a simple API call to check connection
    $response = stripe_request('get', 'customers?limit=1');
    
    echo "<p style='color:green'>✓ Stripe API connection successful!</p>";
    echo "<p>API responded with data</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Stripe API connection failed</p>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Additional debug information for SSL issues
    echo "<p>Common causes:</p>";
    echo "<ul>";
    echo "<li>SSL certificate verification issues</li>";
    echo "<li>Incorrect API key</li>";
    echo "<li>Network/firewall blocking outbound connections</li>";
    echo "</ul>";
}

// Check for redirect URLs
echo "<h2>Redirect URLs</h2>";
if (defined('PAYMENT_SUCCESS_URL') && !empty(PAYMENT_SUCCESS_URL)) {
    echo "<p style='color:green'>✓ Success URL is defined: " . PAYMENT_SUCCESS_URL . "</p>";
} else {
    echo "<p style='color:red'>✗ Success URL is missing or empty</p>";
}

if (defined('PAYMENT_CANCEL_URL') && !empty(PAYMENT_CANCEL_URL)) {
    echo "<p style='color:green'>✓ Cancel URL is defined: " . PAYMENT_CANCEL_URL . "</p>";
} else {
    echo "<p style='color:red'>✗ Cancel URL is missing or empty</p>";
}

// Test creating a simple checkout session
echo "<h2>Test Checkout Session Creation</h2>";
try {
    // Create a simple test item
    $lineItems = [
        [
            'price_data' => [
                'currency' => STRIPE_CURRENCY,
                'product_data' => [
                    'name' => 'Test Product',
                    'description' => 'Test product for debugging',
                ],
                'unit_amount' => 50, // 50 cents
            ],
            'quantity' => 1,
        ]
    ];
    
    // Create a checkout session
    $checkout_session = stripe_checkout_session_create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => PAYMENT_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => PAYMENT_CANCEL_URL,
    ]);
    
    if ($checkout_session && isset($checkout_session->id)) {
        echo "<p style='color:green'>✓ Test checkout session created successfully</p>";
        echo "<p>Session ID: " . $checkout_session->id . "</p>";
        echo "<p>Checkout URL: " . $checkout_session->url . "</p>";
        echo "<p><a href='" . $checkout_session->url . "' target='_blank'>Click here to test the checkout flow</a></p>";
    } else {
        echo "<p style='color:red'>✗ Failed to create checkout session</p>";
        echo "<pre>" . print_r($checkout_session, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error creating test checkout session</p>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Recommendations</h2>";
echo "<p>If you're experiencing the blank page issue when trying to pay with Stripe:</p>";
echo "<ol>";
echo "<li>Check that the Stripe API keys are correct and match between local and server</li>";
echo "<li>Verify cURL is installed and working properly with SSL</li>";
echo "<li>Ensure the success and cancel URLs are pointing to the correct locations</li>";
echo "<li>Look in your server's PHP error logs for any additional information</li>";
echo "<li>Try the test checkout link above to see if it works</li>";
echo "</ol>";

// If there's a specific server error log file, check that too
if (file_exists('error_log') && is_readable('error_log')) {
    echo "<h2>Recent Error Log Entries (last 10 lines)</h2>";
    $error_log = shell_exec("tail -n 10 error_log");
    echo "<pre>" . htmlspecialchars($error_log) . "</pre>";
}

?> 