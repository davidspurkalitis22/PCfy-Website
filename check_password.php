<?php
// Include database connection
require_once 'config.php';

// Get a user's password
$sql = "SELECT id, email, password FROM users LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    echo "<h2>Password Check</h2>";
    echo "User ID: " . $user['id'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Password hash: " . substr($user['password'], 0, 20) . "...<br>";
    echo "Password length: " . strlen($user['password']) . " characters<br>";
    
    // Check hash algorithm
    $info = password_get_info($user['password']);
    echo "Hash algorithm: " . ($info['algoName'] ?: 'Unknown or not a standard PHP hash') . "<br>";
    
    if ($info['algoName'] === 0 || $info['algoName'] === '') {
        // Try to determine if MD5, SHA1, or plain text
        if (strlen($user['password']) === 32 && ctype_xdigit($user['password'])) {
            echo "Password appears to be an MD5 hash (not secure)<br>";
        } elseif (strlen($user['password']) === 40 && ctype_xdigit($user['password'])) {
            echo "Password appears to be a SHA1 hash (not secure)<br>";
        } elseif (strlen($user['password']) < 20) {
            echo "WARNING: Password might be stored in plain text!<br>";
        } else {
            echo "Unknown password format<br>";
        }
    }
    
    // Update password hash if needed
    if (isset($_GET['fix']) && $_GET['fix'] === 'yes' && $info['algoName'] === '') {
        // Get the plain text password from the form
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['old_password'])) {
            $old_password = $_POST['old_password'];
            
            // Check if we can identify the current password format
            $password_matches = false;
            
            // Check if MD5
            if (strlen($user['password']) === 32 && ctype_xdigit($user['password'])) {
                $password_matches = (md5($old_password) === $user['password']);
            } elseif (strlen($user['password']) === 40 && ctype_xdigit($user['password'])) {
                $password_matches = (sha1($old_password) === $user['password']);
            } elseif (strlen($user['password']) < 20) {
                $password_matches = ($old_password === $user['password']);
            }
            
            if ($password_matches) {
                // Create proper hash using password_hash
                $new_hash = password_hash($old_password, PASSWORD_DEFAULT);
                
                // Update the user's password
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $new_hash, $user['id']);
                
                if ($stmt->execute()) {
                    echo "<div style='color: green; font-weight: bold;'>Password updated successfully with proper hashing!</div>";
                } else {
                    echo "<div style='color: red; font-weight: bold;'>Failed to update password: " . $stmt->error . "</div>";
                }
                
                $stmt->close();
            } else {
                echo "<div style='color: red; font-weight: bold;'>Old password does not match!</div>";
            }
        }
        
        // Show form to enter password
        echo "<h3>Fix Password Hashing</h3>";
        echo "<form method='post'>";
        echo "<label>Current password: <input type='password' name='old_password' required></label><br><br>";
        echo "<button type='submit'>Update Password Hash</button>";
        echo "</form>";
    } else {
        echo "<br><a href='check_password.php?fix=yes'>Fix password hashing</a>";
    }
} else {
    echo "No users found in database.";
}

$conn->close();
?> 