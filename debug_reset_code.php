<?php
// Enable full error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Include database connection
require_once 'config.php';

echo "<h1>Reset Code Debugging</h1>";

// Check if email is provided
if (isset($_GET['email'])) {
    $email = $_GET['email'];
} elseif (isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
} else {
    die("No email provided. Please go back and try again.");
}

echo "<p>Testing for email: " . htmlspecialchars($email) . "</p>";

// Check if code is provided
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    echo "<p>Testing code: " . htmlspecialchars($code) . "</p>";
} else {
    echo "<p>No code provided in URL. Add ?code=YOUR_CODE to the URL to test.</p>";
    
    // Show current code in database
    $sql = "SELECT id, reset_code, reset_expiry FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        echo "<p>Current code in database: " . htmlspecialchars($row['reset_code']) . "</p>";
        echo "<p>Expiry time: " . htmlspecialchars($row['reset_expiry']) . "</p>";
        echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
        
        if (strtotime($row['reset_expiry']) > time()) {
            echo "<p style='color: green;'>Code is NOT expired</p>";
        } else {
            echo "<p style='color: red;'>Code IS expired</p>";
        }
    } else {
        echo "<p style='color: red;'>No reset code found for this email!</p>";
    }
    
    $stmt->close();
    
    echo "<form method='POST'>";
    echo "<p>Generate a new code for testing:</p>";
    echo "<input type='hidden' name='generate_code' value='1'>";
    echo "<button type='submit'>Generate New Code</button>";
    echo "</form>";
    
    die();
}

// Process code validation
$sql = "SELECT id, reset_code, reset_expiry FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Code Validation Results:</h2>";

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    echo "<p>Code in database: " . htmlspecialchars($row['reset_code']) . "</p>";
    echo "<p>Code provided: " . htmlspecialchars($code) . "</p>";
    echo "<p>Type of database code: " . gettype($row['reset_code']) . "</p>";
    echo "<p>Type of provided code: " . gettype($code) . "</p>";
    
    if ($row['reset_code'] === $code) {
        echo "<p style='color: green;'>✓ Codes match exactly!</p>";
    } else {
        echo "<p style='color: red;'>✗ Codes do NOT match</p>";
        
        // Show detailed character comparison
        echo "<p>Character by character comparison:</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Position</th><th>DB Code</th><th>Provided Code</th><th>Match</th></tr>";
        
        $max_length = max(strlen($row['reset_code']), strlen($code));
        for ($i = 0; $i < $max_length; $i++) {
            $db_char = isset($row['reset_code'][$i]) ? $row['reset_code'][$i] : '[none]';
            $provided_char = isset($code[$i]) ? $code[$i] : '[none]';
            $match = $db_char === $provided_char ? "✓" : "✗";
            $color = $match === "✓" ? "green" : "red";
            
            echo "<tr>";
            echo "<td>" . ($i + 1) . "</td>";
            echo "<td>" . htmlspecialchars($db_char) . "</td>";
            echo "<td>" . htmlspecialchars($provided_char) . "</td>";
            echo "<td style='color: $color;'>" . $match . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    echo "<p>Expiry time: " . htmlspecialchars($row['reset_expiry']) . "</p>";
    echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
    
    if (strtotime($row['reset_expiry']) > time()) {
        echo "<p style='color: green;'>✓ Code is NOT expired</p>";
    } else {
        echo "<p style='color: red;'>✗ Code IS expired</p>";
    }
} else {
    echo "<p style='color: red;'>No reset code found for this email!</p>";
}

$stmt->close();

// Process code generation if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_code'])) {
    // Generate new code
    $reset_code = mt_rand(100000, 999999); // 6-digit code
    $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Code expires in 1 hour
    
    // Update user record with reset code
    $update_sql = "UPDATE users SET reset_code = ?, reset_expiry = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sss", $reset_code, $reset_expiry, $email);
    
    if ($update_stmt->execute()) {
        echo "<p style='color: green;'>New code generated: " . htmlspecialchars($reset_code) . "</p>";
        echo "<p>Expiry time: " . htmlspecialchars($reset_expiry) . "</p>";
    } else {
        echo "<p style='color: red;'>Error generating new code: " . $update_stmt->error . "</p>";
    }
    
    $update_stmt->close();
}

$conn->close();
?> 