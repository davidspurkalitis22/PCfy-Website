<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config.php';

echo "<h1>Setting Up Database Tables</h1>";

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reset_code VARCHAR(10) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Users table created successfully</p>";
} else {
    echo "<p style='color:red'>✗ Error creating users table: " . $conn->error . "</p>";
}

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL,
    user_id INT(11) DEFAULT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    county VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    country VARCHAR(2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    order_date DATETIME NOT NULL,
    order_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    order_total DECIMAL(10, 2) NOT NULL,
    UNIQUE KEY (order_number)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Orders table created successfully</p>";
} else {
    echo "<p style='color:red'>✗ Error creating orders table: " . $conn->error . "</p>";
}

// Create order_items table
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    order_number VARCHAR(20) NOT NULL,
    product_id VARCHAR(20) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Order items table created successfully</p>";
} else {
    echo "<p style='color:red'>✗ Error creating order items table: " . $conn->error . "</p>";
}

// Create repair_bookings table
$sql = "CREATE TABLE IF NOT EXISTS repair_bookings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) DEFAULT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  service_type VARCHAR(50) NOT NULL,
  issue_description TEXT NOT NULL,
  preferred_date DATE DEFAULT NULL,
  status ENUM('pending','confirmed','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  technician_notes TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT repair_bookings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Repair bookings table created successfully</p>";
} else {
    echo "<p style='color:red'>✗ Error creating repair bookings table: " . $conn->error . "</p>";
}

// Create an admin user
$admin_firstname = "Admin";
$admin_lastname = "User";
$admin_email = "admin@pcfy.com";
$admin_password = password_hash("Admin123!", PASSWORD_DEFAULT); // Hashed password
$admin_phone = "123456789";
$admin_address = "Admin Address";
$is_admin = 1;

// Check if admin user already exists
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $admin_email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    // Create new admin account
    $sql = "INSERT INTO users (firstname, lastname, email, password, phone, address, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $admin_firstname, $admin_lastname, $admin_email, $admin_password, $admin_phone, $admin_address, $is_admin);
    
    if ($stmt->execute()) {
        echo "<p style='color:green'>✓ Admin account created successfully!</p>";
        echo "<p><strong>Admin Login Credentials:</strong><br>";
        echo "Email: admin@pcfy.com<br>";
        echo "Password: Admin123!</p>";
    } else {
        echo "<p style='color:red'>✗ Error creating admin account: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
} else {
    echo "<p style='color:blue'>ℹ️ Admin account already exists</p>";
}

$check_stmt->close();

echo "<p>Database setup complete. You can now <a href='login.php'>login</a> or <a href='register.php'>register</a>.</p>";

// Clean up
$conn->close();
?> 