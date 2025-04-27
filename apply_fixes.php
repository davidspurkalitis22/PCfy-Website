<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Buffer output to prevent "headers already sent" issues
ob_start();

echo "<h1>Applying Stripe Fixes</h1>";

// Function to fix a PHP file by removing whitespace and closing tags
function fix_php_file($file_path) {
    if (!file_exists($file_path)) {
        return [false, "File not found: $file_path"];
    }
    
    // Read the file
    $content = file_get_contents($file_path);
    if ($content === false) {
        return [false, "Could not read file: $file_path"];
    }
    
    // Create backup
    $backup_path = $file_path . '.bak';
    if (!file_put_contents($backup_path, $content)) {
        return [false, "Could not create backup: $backup_path"];
    }
    
    // Remove BOM if present
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
    
    // Remove closing PHP tag and any whitespace after it
    $content = preg_replace('/\?>[\s\r\n]*$/', '', $content);
    
    // Ensure no whitespace at the end of the file
    $content = rtrim($content);
    
    // Write the fixed content
    if (!file_put_contents($file_path, $content)) {
        return [false, "Could not write fixed file: $file_path"];
    }
    
    return [true, "Successfully fixed $file_path"];
}

// Function to update success and cancel URLs
function update_urls($file_path, $domain) {
    if (!file_exists($file_path)) {
        return [false, "File not found: $file_path"];
    }
    
    // Read the file
    $content = file_get_contents($file_path);
    if ($content === false) {
        return [false, "Could not read file: $file_path"];
    }
    
    // Create backup if not already created
    $backup_path = $file_path . '.bak';
    if (!file_exists($backup_path) && !file_put_contents($backup_path, $content)) {
        return [false, "Could not create backup: $backup_path"];
    }
    
    // Update localhost URLs to domain URLs
    $content = preg_replace(
        "/define\('PAYMENT_SUCCESS_URL', '.*?'\);/",
        "define('PAYMENT_SUCCESS_URL', 'https://$domain/payment-success.php');",
        $content
    );
    
    $content = preg_replace(
        "/define\('PAYMENT_CANCEL_URL', '.*?'\);/",
        "define('PAYMENT_CANCEL_URL', 'https://$domain/payment-cancel.php');",
        $content
    );
    
    // Write the fixed content
    if (!file_put_contents($file_path, $content)) {
        return [false, "Could not write fixed file: $file_path"];
    }
    
    return [true, "Successfully updated URLs in $file_path"];
}

// Function to fix stripe-payment.php to use output buffering
function fix_stripe_payment($file_path) {
    if (!file_exists($file_path)) {
        return [false, "File not found: $file_path"];
    }
    
    // Read the file
    $content = file_get_contents($file_path);
    if ($content === false) {
        return [false, "Could not read file: $file_path"];
    }
    
    // Create backup
    $backup_path = $file_path . '.bak';
    if (!file_put_contents($backup_path, $content)) {
        return [false, "Could not create backup: $backup_path"];
    }
    
    // Add output buffering at the beginning of the file
    // Find the position after the first <?php
    $pos = strpos($content, '<?php');
    if ($pos !== false) {
        $pos += 5; // Length of '<?php'
        $buffer_code = "\n// Buffer output to prevent headers already sent\nob_start();\n";
        $content = substr($content, 0, $pos) . $buffer_code . substr($content, $pos);
    }
    
    // Ensure all header/Location calls are followed by exit
    $content = preg_replace('/header\("Location: (.*?)"\);(\s*)([^\s]|$)/i', 'header("Location: $1");\nexit();$3', $content);
    
    // Write the fixed content
    if (!file_put_contents($file_path, $content)) {
        return [false, "Could not write fixed file: $file_path"];
    }
    
    return [true, "Successfully fixed $file_path"];
}

// Detect server domain
$server_domain = $_SERVER['HTTP_HOST'];
echo "<p>Detected domain: $server_domain</p>";

echo "<h2>Applying fixes...</h2>";

// 1. Fix stripe-config.php (whitespace and closing tag)
echo "<h3>Fixing stripe-config.php</h3>";
$result = fix_php_file('stripe-config.php');
echo "<p>" . ($result[0] ? '✅ ' : '❌ ') . $result[1] . "</p>";

// 2. Update URLs in stripe-config.php
echo "<h3>Updating URLs in stripe-config.php</h3>";
$result = update_urls('stripe-config.php', $server_domain);
echo "<p>" . ($result[0] ? '✅ ' : '❌ ') . $result[1] . "</p>";

// 3. Fix stripe-init.php (whitespace and closing tag)
echo "<h3>Fixing stripe-init.php</h3>";
$result = fix_php_file('stripe-init.php');
echo "<p>" . ($result[0] ? '✅ ' : '❌ ') . $result[1] . "</p>";

// 4. Fix stripe-payment.php (add output buffering, fix redirects)
echo "<h3>Fixing stripe-payment.php</h3>";
$result = fix_stripe_payment('stripe-payment.php');
echo "<p>" . ($result[0] ? '✅ ' : '❌ ') . $result[1] . "</p>";

// 5. Fix config.php (whitespace and closing tag)
echo "<h3>Fixing config.php</h3>";
$result = fix_php_file('config.php');
echo "<p>" . ($result[0] ? '✅ ' : '❌ ') . $result[1] . "</p>";

echo "<h2>Testing Fixed Files</h2>";

echo "<p>Let's test if the Stripe integration works now:</p>";

// Create a test button
echo '<form action="stripe-payment.php" method="POST">';
echo '<input type="hidden" name="email" value="test@example.com">';
echo '<input type="hidden" name="phone" value="1234567890">';
echo '<input type="hidden" name="first_name" value="Test">';
echo '<input type="hidden" name="last_name" value="User">';
echo '<input type="hidden" name="address" value="123 Test St">';
echo '<input type="hidden" name="city" value="Test City">';
echo '<input type="hidden" name="county" value="Test County">';
echo '<input type="hidden" name="postal_code" value="12345">';
echo '<input type="hidden" name="country" value="IE">';

// Create a sample cart and encode it
$cart = [
    [
        'id' => 'TEST001',
        'name' => 'Fix Test Product',
        'price' => 10.00,
        'quantity' => 1
    ]
];
echo '<input type="hidden" name="cart_data" value="' . htmlspecialchars(json_encode($cart)) . '">';

echo '<button type="submit" style="padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Test Stripe Payment</button>';
echo '</form>';

echo "<h2>View Debug Log</h2>";
echo "<p>If you still experience issues, check the stripe_error.log file for errors.</p>";

// Check if log file exists and show last 10 lines
if (file_exists('stripe_error.log')) {
    echo "<h3>Recent Log Entries</h3>";
    $log_content = file_get_contents('stripe_error.log');
    $lines = explode("\n", $log_content);
    $last_lines = array_slice($lines, -10);
    
    echo "<pre>";
    foreach ($last_lines as $line) {
        echo htmlspecialchars($line) . "\n";
    }
    echo "</pre>";
} else {
    echo "<p>No error log file found.</p>";
}

echo "<p><a href='debug_stripe.php'>Go back to debug page</a></p>";

// Flush output buffer
ob_end_flush();
?> 