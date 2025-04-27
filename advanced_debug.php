<?php
// Enable extensive error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'stripe_error.log');

// Buffer all output
ob_start();

// Start session
session_start();

echo "<h1>Advanced Stripe Debugging</h1>";

// Check for any pending errors
if (function_exists('error_get_last')) {
    $error = error_get_last();
    if ($error) {
        echo "<h2>Last Error</h2>";
        echo "<pre>" . print_r($error, true) . "</pre>";
    }
}

// Check if we have URL parameters
echo "<h2>Current Request</h2>";
echo "<p>URL: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
echo "<p>Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";

// Check session data
echo "<h2>Session Data</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Check cookie data
echo "<h2>Cookie Data</h2>";
echo "<pre>" . print_r($_COOKIE, true) . "</pre>";

// Load a minimal set of config just for testing connection
echo "<h2>Testing Stripe Connection</h2>";
try {
    // Define Stripe API key manually for this test
    $stripe_api_key = 'sk_test_51RGO9EIh1kNprMB5RPOzC0Lq95lL3NnNNwUoRjicbiqCE87ZDJZaDLoP1R4eYIJ04ZQgJrVbGLV78D11EGeA3CFd00gblabO45';
    
    // Manual curl test
    $curl = curl_init();
    $url = 'https://api.stripe.com/v1/customers?limit=1';
    
    $headers = [
        'Authorization: Bearer ' . $stripe_api_key,
        'Stripe-Version: 2022-11-15',
        'Content-Type: application/x-www-form-urlencoded'
    ];
    
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 80,
        CURLOPT_SSL_VERIFYPEER => false,  // Try disabling SSL verification
        CURLOPT_VERBOSE => true           // Enable verbose output
    ];
    
    // Create a file handle for curl to write verbose info
    $verbose_output = fopen('php://temp', 'w+');
    curl_setopt($curl, CURLOPT_STDERR, $verbose_output);
    
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $error = curl_error($curl);
    $info = curl_getinfo($curl);
    
    // Get verbose debug info
    rewind($verbose_output);
    $verbose_log = stream_get_contents($verbose_output);
    
    echo "<h3>cURL Test Results</h3>";
    
    if ($error) {
        echo "<p style='color:red'>✗ cURL Error: " . htmlspecialchars($error) . "</p>";
        echo "<h4>cURL Info</h4>";
        echo "<pre>" . print_r($info, true) . "</pre>";
        echo "<h4>Verbose Output</h4>";
        echo "<pre>" . htmlspecialchars($verbose_log) . "</pre>";
    } else {
        echo "<p style='color:green'>✓ cURL request successful</p>";
        echo "<p>HTTP Status: " . $info['http_code'] . "</p>";
        echo "<h4>Response</h4>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "...</pre>";
    }
    
    curl_close($curl);
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Now try with actual configuration files
echo "<h2>Testing With Configuration Files</h2>";
try {
    echo "<p>Including config.php... ";
    require_once 'config.php';
    echo "✓</p>";
    
    echo "<p>Including stripe-config.php... ";
    require_once 'stripe-config.php';
    echo "✓</p>";
    
    echo "<p>Including stripe-init.php... ";
    require_once 'stripe-init.php';
    echo "✓</p>";
    
    // Check for required constants
    echo "<h3>Configuration Constants</h3>";
    echo "<ul>";
    $constants = [
        'STRIPE_API_KEY',
        'STRIPE_PUBLISHABLE_KEY',
        'STRIPE_CURRENCY',
        'PAYMENT_SUCCESS_URL',
        'PAYMENT_CANCEL_URL'
    ];
    
    foreach ($constants as $constant) {
        if (defined($constant)) {
            echo "<li style='color:green'>✓ " . $constant . " is defined";
            if ($constant === 'STRIPE_API_KEY' || $constant === 'STRIPE_PUBLISHABLE_KEY') {
                // Show masked version
                $value = constant($constant);
                $masked = substr($value, 0, 4) . '...' . substr($value, -4);
                echo ": " . $masked;
            } else {
                echo ": " . constant($constant);
            }
            echo "</li>";
        } else {
            echo "<li style='color:red'>✗ " . $constant . " is NOT defined</li>";
        }
    }
    echo "</ul>";
    
    // Try a simple API call
    echo "<h3>API Call Test</h3>";
    stripe_set_api_key(STRIPE_API_KEY);
    
    try {
        $checkout_session = stripe_checkout_session_create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => STRIPE_CURRENCY,
                        'product_data' => [
                            'name' => 'Debug Test',
                        ],
                        'unit_amount' => 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => PAYMENT_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => PAYMENT_CANCEL_URL,
        ]);
        
        echo "<p style='color:green'>✓ Checkout session created successfully</p>";
        echo "<p>Session ID: " . $checkout_session->id . "</p>";
        echo "<p>URL: <a href='" . $checkout_session->url . "' target='_blank'>" . $checkout_session->url . "</a></p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Session creation error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Configuration error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Troubleshooting Redirect Issues</h2>";

// Simulate the redirect process
echo "<h3>Testing Redirect Headers</h3>";

try {
    echo "<p>This will test if headers can be sent properly:</p>";
    
    // Buffer and discard output to test header sending
    ob_start();
    $result = @header('Content-Type: text/plain', false);
    ob_end_clean();
    
    if ($result) {
        echo "<p style='color:green'>✓ Headers can be sent</p>";
    } else {
        echo "<p style='color:red'>✗ Headers cannot be sent, possibly because output has already been sent</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error testing headers: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check output buffering status
echo "<h3>Output Buffering Status</h3>";
echo "<p>Current nesting level: " . ob_get_level() . "</p>";

echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Check if stripe-config.php and stripe-init.php contain any whitespace or output before or after the PHP tags</li>";
echo "<li>Ensure all included files use &lt;?php (not &lt;?) and don't have closing ?&gt; tags</li>";
echo "<li>Add 'ob_start();' at the beginning of stripe-payment.php to buffer any accidental output</li>";
echo "<li>Make sure the success and cancel URLs are properly defined and accessible</li>";
echo "<li>Update redirect code to use 'exit();' immediately after header() calls</li>";
echo "</ol>";

// Create a test button that simulates the checkout process
echo "<h2>Test Checkout Process</h2>";
echo "<form action='stripe-payment.php' method='POST'>";
echo "<input type='hidden' name='email' value='test@example.com'>";
echo "<input type='hidden' name='phone' value='1234567890'>";
echo "<input type='hidden' name='first_name' value='Test'>";
echo "<input type='hidden' name='last_name' value='User'>";
echo "<input type='hidden' name='address' value='123 Test St'>";
echo "<input type='hidden' name='city' value='Test City'>";
echo "<input type='hidden' name='county' value='Test County'>";
echo "<input type='hidden' name='postal_code' value='12345'>";
echo "<input type='hidden' name='country' value='IE'>";

// Create a sample cart and encode it
$cart = [
    [
        'id' => 'TEST001',
        'name' => 'Debug Test Product',
        'price' => 10.00,
        'quantity' => 1
    ]
];
echo "<input type='hidden' name='cart_data' value='" . htmlspecialchars(json_encode($cart)) . "'>";

echo "<button type='submit' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Test Checkout Process</button>";
echo "</form>";

// Create a fix script for common issues
echo "<h2>Fix Common Issues</h2>";

echo "<button onclick='applyFixes()' style='padding: 10px; background-color: #008CBA; color: white; border: none; cursor: pointer;'>Apply Common Fixes</button>";

echo "<script>
function applyFixes() {
    if (confirm('This will attempt to fix common issues by creating fixed versions of your files. Continue?')) {
        window.location.href = 'apply_fixes.php';
    }
}
</script>";

// End buffer and output
$output = ob_get_clean();
echo $output;

?> 