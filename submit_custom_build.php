<?php
// Disable all error reporting for production
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Start output buffering
ob_start();

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An unknown error occurred'
];

try {
    // Start session
    session_start();
    
    // Include database connection
    try {
        require_once 'config.php';
        // Also include email helper for sending confirmation
        require_once 'email_helper.php';
    } catch (Exception $configException) {
        // Capture and convert config errors to JSON
        throw new Exception('Database configuration error: ' . $configException->getMessage());
    }
    
    // Process the form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data with proper sanitization
        $cpu = isset($_POST['cpu']) ? trim(htmlspecialchars($_POST['cpu'])) : '';
        $motherboard = isset($_POST['motherboard']) ? trim(htmlspecialchars($_POST['motherboard'])) : '';
        $gpu = isset($_POST['gpu']) ? trim(htmlspecialchars($_POST['gpu'])) : '';
        $ram = isset($_POST['ram']) ? trim(htmlspecialchars($_POST['ram'])) : '';
        $storage = isset($_POST['storage']) ? trim(htmlspecialchars($_POST['storage'])) : '';
        $additional_storage = isset($_POST['additional_storage']) ? trim(htmlspecialchars($_POST['additional_storage'])) : 'None';
        $cooling = isset($_POST['cooling']) ? trim(htmlspecialchars($_POST['cooling'])) : '';
        $case = isset($_POST['case']) ? trim(htmlspecialchars($_POST['case'])) : '';
        $power_supply = isset($_POST['power_supply']) ? trim(htmlspecialchars($_POST['power_supply'])) : '';
        $operating_system = isset($_POST['operating_system']) ? trim(htmlspecialchars($_POST['operating_system'])) : '';
        $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
        $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
        $phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';
        $additional_notes = isset($_POST['additional_notes']) ? trim(htmlspecialchars($_POST['additional_notes'])) : '';
        $build_summary = isset($_POST['build_summary']) ? $_POST['build_summary'] : '';
        
        // Log sanitized data
        error_log("Sanitized data: CPU=$cpu, Motherboard=$motherboard, Name=$name, Email=$email");
        
        // Validate required fields
        if (empty($cpu) || empty($motherboard) || empty($gpu) || empty($ram) || empty($storage) || 
            empty($cooling) || empty($case) || empty($power_supply) || empty($name) || empty($email) || empty($phone)) {
            throw new Exception("Please fill in all required fields.");
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        
        // Create database table if it doesn't exist
        $sql_create_table = "CREATE TABLE IF NOT EXISTS custom_builds (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            cpu VARCHAR(255) NOT NULL,
            motherboard VARCHAR(255) NOT NULL,
            gpu VARCHAR(255) NOT NULL,
            ram VARCHAR(255) NOT NULL,
            storage VARCHAR(255) NOT NULL,
            additional_storage VARCHAR(255) NOT NULL,
            cooling VARCHAR(255) NOT NULL,
            pc_case VARCHAR(255) NOT NULL,
            power_supply VARCHAR(255) NOT NULL,
            operating_system VARCHAR(255) NOT NULL,
            additional_notes TEXT,
            status VARCHAR(50) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql_create_table) !== TRUE) {
            error_log("Error creating table: " . $conn->error);
            throw new Exception("Error creating database table");
        }
        
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO custom_builds (name, email, phone, cpu, motherboard, gpu, ram, storage, additional_storage, cooling, pc_case, power_supply, operating_system, additional_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            throw new Exception("Database prepare failed");
        }
        
        $stmt->bind_param("ssssssssssssss", $name, $email, $phone, $cpu, $motherboard, $gpu, $ram, $storage, $additional_storage, $cooling, $case, $power_supply, $operating_system, $additional_notes);
        
        if ($stmt->execute()) {
            $build_id = $conn->insert_id;
            $response = [
                'success' => true,
                'message' => "Your custom PC build request has been submitted successfully.",
                'build_id' => $build_id
            ];
            error_log("Build request submitted successfully. ID: $build_id");
            
            // SEND CONFIRMATION EMAIL TO CUSTOMER
            // Create email content
            $emailContent = '
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            
            <p>Thank you for submitting your custom PC build request with ' . SITE_NAME . '. We have received your details and are excited to help you create your perfect PC.</p>
            
            <p>Our team will review your specifications and get back to you within 24-48 hours with a detailed quote and any recommendations we might have.</p>
            
            <h3>Your Build Specifications:</h3>
            <ul>
                <li><strong>CPU:</strong> ' . htmlspecialchars($cpu) . '</li>
                <li><strong>Motherboard:</strong> ' . htmlspecialchars($motherboard) . '</li>
                <li><strong>Graphics Card:</strong> ' . htmlspecialchars($gpu) . '</li>
                <li><strong>RAM:</strong> ' . htmlspecialchars($ram) . '</li>
                <li><strong>Primary Storage:</strong> ' . htmlspecialchars($storage) . '</li>
                <li><strong>Additional Storage:</strong> ' . htmlspecialchars($additional_storage) . '</li>
                <li><strong>CPU Cooling:</strong> ' . htmlspecialchars($cooling) . '</li>
                <li><strong>Case:</strong> ' . htmlspecialchars($case) . '</li>
                <li><strong>Power Supply:</strong> ' . htmlspecialchars($power_supply) . '</li>
                <li><strong>Operating System:</strong> ' . htmlspecialchars($operating_system) . '</li>
            </ul>
            
            <p>If you have any questions in the meantime, please feel free to contact us directly at ' . SITE_EMAIL . ' or by phone at 085 842 4769.</p>
            
            <p>We appreciate your interest in ' . SITE_NAME . ' and look forward to building your custom PC.</p>';
            
            // Plain text version for email clients that don't support HTML
            $textContent = "Dear $name,

Thank you for submitting your custom PC build request with " . SITE_NAME . ". We have received your details and are excited to help you create your perfect PC.

Our team will review your specifications and get back to you within 24-48 hours with a detailed quote and any recommendations we might have.

Your Build Specifications:
- CPU: $cpu
- Motherboard: $motherboard
- Graphics Card: $gpu
- RAM: $ram
- Primary Storage: $storage
- Additional Storage: $additional_storage
- CPU Cooling: $cooling
- Case: $case
- Power Supply: $power_supply
- Operating System: $operating_system

If you have any questions in the meantime, please feel free to contact us directly at " . SITE_EMAIL . " or by phone at 085 842 4769.

We appreciate your interest in " . SITE_NAME . " and look forward to building your custom PC.

© " . date('Y') . " " . SITE_NAME . ". All rights reserved.
Galway, Ireland";

            // Generate HTML email using template
            $htmlEmail = create_email_template(
                'Custom PC Build Request Confirmation',
                $emailContent,
                'Visit Our Website',
                'https://pcfy.ie'
            );
            
            // Send confirmation email
            try {
                $emailResult = send_email(
                    $email,
                    $name,
                    'Your ' . SITE_NAME . ' Custom PC Build Request Confirmation',
                    $htmlEmail,
                    $textContent,
                    SITE_EMAIL,
                    SITE_NAME . ' Customer Support'
                );
                
                if ($emailResult['success']) {
                    error_log("Confirmation email sent successfully to $email");
                } else {
                    error_log("Failed to send confirmation email: " . $emailResult['message']);
                }
            } catch (Exception $emailException) {
                error_log("Email exception: " . $emailException->getMessage());
                // Don't let email errors prevent successful database submission
            }
        } else {
            error_log("Execute failed: " . $stmt->error);
            throw new Exception("Database insert failed");
        }
        
        $stmt->close();
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    error_log("Error in submit_custom_build.php: " . $e->getMessage());
}

// Clear any output that might have been generated
if (ob_get_length()) ob_end_clean();

// Set JSON header - after clearing buffer
header('Content-Type: application/json');

// Return clean JSON response
echo json_encode($response);
exit; 