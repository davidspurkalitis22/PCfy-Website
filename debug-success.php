<?php
// Enable extensive error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'success_page_error.log');

// Start session
session_start();

echo "<h1>Payment Success Page Debugging</h1>";

// Check query parameters
echo "<h2>URL Parameters</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Check session data
echo "<h2>Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if we have a session ID
if (isset($_GET['session_id'])) {
    echo "<h2>Stripe Session Found</h2>";
    echo "<p>Session ID: " . htmlspecialchars($_GET['session_id']) . "</p>";
    
    // Check if order details exist
    if (isset($_SESSION['order_details'])) {
        echo "<p style='color:green'>✓ Order details found in session</p>";
    } else {
        echo "<p style='color:red'>✗ Order details not found in session</p>";
        
        // This is likely the issue - session data was lost
        echo "<h3>Possible Fix</h3>";
        echo "<p>The session data appears to be lost between checkout and success page.</p>";
        echo "<p>This could be due to:</p>";
        echo "<ol>";
        echo "<li>Different domain or subdomain redirects</li>";
        echo "<li>Session configuration issues</li>";
        echo "<li>PHP session garbage collection timing</li>";
        echo "</ol>";
    }
}

// Let's add some simple tests for payment-success.php
echo "<h2>Testing payment-success.php File</h2>";

// Check if file exists
if (file_exists('payment-success.php')) {
    echo "<p style='color:green'>✓ payment-success.php exists</p>";
    
    // Display the first 20 lines of the file to see the code
    $file_content = file_get_contents('payment-success.php');
    $lines = explode("\n", $file_content);
    $preview = array_slice($lines, 0, 50);
    
    echo "<h3>First 50 Lines of payment-success.php</h3>";
    echo "<pre>";
    echo htmlspecialchars(implode("\n", $preview));
    echo "</pre>";
    
    // Check if there are any exit() or die() calls early in the file
    if (preg_match('/\bexit\s*\(.*?\);|\bdie\s*\(.*?\);/i', implode("\n", array_slice($lines, 0, 50)))) {
        echo "<p style='color:red'>⚠️ Found early exit() or die() calls that might be terminating execution</p>";
    }
    
} else {
    echo "<p style='color:red'>✗ payment-success.php does not exist</p>";
}

// Create a fake success page with debugging
echo "<h2>Create Test Order</h2>";
echo "<p>This section creates a test order record without needing to go through Stripe.</p>";

// Generate a test order number
$test_order_number = 'TEST-' . date('YmdHis');

echo "<form method='post' action='".htmlspecialchars($_SERVER['PHP_SELF'])."'>";
echo "<input type='hidden' name='test_action' value='create_order'>";
echo "<input type='hidden' name='order_number' value='$test_order_number'>";
echo "<button type='submit' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Create Test Order</button>";
echo "</form>";

// Check if form was submitted
if (isset($_POST['test_action']) && $_POST['test_action'] === 'create_order') {
    echo "<h3>Creating Test Order</h3>";
    
    try {
        // Include database connection
        require_once 'config.php';
        
        // Create a test order directly in the database
        $orderNumber = $_POST['order_number'];
        $customerName = 'Test Customer';
        $customerEmail = 'test@example.com';
        $totalAmount = 10.00;
        $paymentMethod = 'Stripe (Test)';
        $status = 'completed';
        
        // Check if orders table exists
        $result = $conn->query("SHOW TABLES LIKE 'orders'");
        if ($result->num_rows == 0) {
            echo "<p style='color:red'>✗ Orders table does not exist</p>";
            echo "<p>Creating orders table...</p>";
            
            // Create orders table
            $sql = "CREATE TABLE orders (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                order_number VARCHAR(20) NOT NULL,
                customer_name VARCHAR(100) NOT NULL,
                customer_email VARCHAR(100) NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color:green'>✓ Orders table created successfully</p>";
            } else {
                echo "<p style='color:red'>✗ Error creating orders table: " . $conn->error . "</p>";
            }
        }
        
        // Check if order_items table exists
        $result = $conn->query("SHOW TABLES LIKE 'order_items'");
        if ($result->num_rows == 0) {
            echo "<p style='color:red'>✗ Order items table does not exist</p>";
            echo "<p>Creating order_items table...</p>";
            
            // Create order_items table
            $sql = "CREATE TABLE order_items (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                order_id INT(11) NOT NULL,
                order_number VARCHAR(20) NOT NULL,
                product_id VARCHAR(20) NOT NULL,
                product_name VARCHAR(255) NOT NULL,
                quantity INT(11) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color:green'>✓ Order items table created successfully</p>";
            } else {
                echo "<p style='color:red'>✗ Error creating order items table: " . $conn->error . "</p>";
            }
        }
        
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (order_number, customer_name, customer_email, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdss", $orderNumber, $customerName, $customerEmail, $totalAmount, $paymentMethod, $status);
        
        if ($stmt->execute()) {
            $orderId = $conn->insert_id;
            echo "<p style='color:green'>✓ Test order created successfully</p>";
            
            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, order_number, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
            $productId = "TEST001";
            $productName = "Test Product";
            $quantity = 1;
            $price = 10.00;
            
            $stmt->bind_param("isssid", $orderId, $orderNumber, $productId, $productName, $quantity, $price);
            
            if ($stmt->execute()) {
                echo "<p style='color:green'>✓ Test order item added successfully</p>";
                
                // Create test session data
                $_SESSION['order_details'] = [
                    'first_name' => 'Test',
                    'last_name' => 'Customer',
                    'email' => 'test@example.com',
                    'phone' => '1234567890',
                    'address' => '123 Test St',
                    'city' => 'Test City',
                    'county' => 'Test County',
                    'postal_code' => '12345',
                    'country' => 'IE',
                    'order_total' => 10.00,
                    'cart' => [
                        [
                            'id' => 'TEST001',
                            'name' => 'Test Product',
                            'price' => 10.00,
                            'quantity' => 1
                        ]
                    ]
                ];
                
                $_SESSION['stripe_session_id'] = 'test_' . uniqid();
                
                echo "<p style='color:green'>✓ Test session data created</p>";
                echo "<p>Now you can go to <a href='payment-success.php?session_id=".$_SESSION['stripe_session_id']."'>payment-success.php</a> with the test data.</p>";
                
            } else {
                echo "<p style='color:red'>✗ Error adding test order item: " . $stmt->error . "</p>";
            }
            
        } else {
            echo "<p style='color:red'>✗ Error creating test order: " . $stmt->error . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Fix Recommendations</h2>";
echo "<ol>";
echo "<li>Add output buffering to payment-success.php: Insert <code>ob_start();</code> right after the opening PHP tag</li>";
echo "<li>Add error reporting: Add <code>error_reporting(E_ALL); ini_set('display_errors', 1);</code> at the beginning of the file</li>";
echo "<li>Check for early exit() calls that might be terminating execution before output</li>";
echo "<li>Make sure you're not losing session data during the redirect</li>";
echo "</ol>";

// Create a simple fix script for payment-success.php
echo "<h3>Fix Payment Success Page</h3>";
echo "<form method='post' action='".htmlspecialchars($_SERVER['PHP_SELF'])."'>";
echo "<input type='hidden' name='test_action' value='fix_success_page'>";
echo "<button type='submit' style='padding: 10px; background-color: #008CBA; color: white; border: none; cursor: pointer;'>Fix Success Page</button>";
echo "</form>";

if (isset($_POST['test_action']) && $_POST['test_action'] === 'fix_success_page') {
    // Check if file exists
    if (file_exists('payment-success.php')) {
        // Create backup
        if (copy('payment-success.php', 'payment-success.php.bak')) {
            echo "<p>Created backup at payment-success.php.bak</p>";
            
            // Read the file
            $content = file_get_contents('payment-success.php');
            
            // Add output buffering and error reporting
            $pos = strpos($content, '<?php');
            if ($pos !== false) {
                $pos += 5; // Length of '<?php'
                $debug_code = "\n// Enable error reporting\nerror_reporting(E_ALL);\nini_set('display_errors', 1);\n\n// Buffer output\nob_start();\n";
                $content = substr($content, 0, $pos) . $debug_code . substr($content, $pos);
            }
            
            // Add output buffer flush at the end
            if (strpos($content, '?>') !== false) {
                $content = str_replace('?>', "\n// Flush the output buffer\nob_end_flush();\n?>", $content);
            } else {
                $content .= "\n// Flush the output buffer\nob_end_flush();\n";
            }
            
            // Write the modified content
            if (file_put_contents('payment-success.php', $content)) {
                echo "<p style='color:green'>✓ Successfully added output buffering and error reporting to payment-success.php</p>";
            } else {
                echo "<p style='color:red'>✗ Failed to write to payment-success.php</p>";
            }
        } else {
            echo "<p style='color:red'>✗ Failed to create backup of payment-success.php</p>";
        }
    } else {
        echo "<p style='color:red'>✗ payment-success.php does not exist</p>";
    }
}
?> 