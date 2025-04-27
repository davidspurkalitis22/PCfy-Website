<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config.php';

echo "<h1>Setting Up PCFY Shop Products</h1>";

// First, create the products table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS products (
  id VARCHAR(20) NOT NULL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category VARCHAR(50) NOT NULL,
  subcategory VARCHAR(50) DEFAULT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image_url VARCHAR(255) DEFAULT NULL,
  features TEXT DEFAULT NULL,
  specifications TEXT DEFAULT NULL,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  on_sale TINYINT(1) NOT NULL DEFAULT 0,
  sale_price DECIMAL(10, 2) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Products table created successfully</p>";
} else {
    echo "<p style='color:red'>✗ Error creating products table: " . $conn->error . "</p>";
    exit;
}

// Check if the products table is empty, and if so, add your actual products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    echo "<p>Adding your actual PCFY shop products...</p>";
    
    // Your ACTUAL shop products from add_shop_products.php
    $shop_products = [
        [
            'id' => 'PROD-STORAGE001',
            'name' => 'Intel DC S3710 800GB 2.5" SATA 6Gb/s 256MB Cache',
            'category' => 'Storage',
            'subcategory' => 'SSD',
            'description' => 'Internal SSD with high performance and reliability for demanding workloads.',
            'price' => 130.00,
            'stock' => 15,
            'image_url' => 'images/ssd.jpg',
            'features' => 'High performance, 800GB capacity, SATA interface, 2.5" form factor',
            'specifications' => '{"Brand": "Intel", "Series": "DC S3710", "Capacity": "800GB", "Interface": "SATA 6Gb/s", "Form Factor": "2.5 inch", "Cache": "256MB"}',
            'featured' => 1,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PROD-STORAGE002',
            'name' => 'Hitachi 250GB 7200RPM SATA 3Gb/s 256MB Cache',
            'category' => 'Storage',
            'subcategory' => 'Hard Drive',
            'description' => 'Internal HDD with reliable performance for everyday computing needs.',
            'price' => 40.00,
            'stock' => 30,
            'image_url' => 'images/hdd.jpg',
            'features' => '250GB capacity, 7200RPM speed, SATA interface, reliable performance',
            'specifications' => '{"Brand": "Hitachi", "Capacity": "250GB", "Speed": "7200RPM", "Interface": "SATA 3Gb/s", "Cache": "256MB"}',
            'featured' => 0,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PROD-CABLE001',
            'name' => 'Premium HDMI 2.1 Cable',
            'category' => 'Cables',
            'subcategory' => 'HDMI Cable',
            'description' => '8K@60Hz, 4K@120Hz, 2m Length - High-speed HDMI cable for gaming and high-definition video.',
            'price' => 24.99,
            'stock' => 50,
            'image_url' => 'images/HDMI.jpg',
            'features' => 'HDMI 2.1 specification, 8K@60Hz, 4K@120Hz support, 2m length, gold-plated connectors',
            'specifications' => '{"Brand": "Premium", "Version": "HDMI 2.1", "Length": "2m", "Max Resolution": "8K@60Hz", "Connector": "Gold-plated"}',
            'featured' => 0,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PROD-CABLE002',
            'name' => 'USB-C to USB-C Cable',
            'category' => 'Cables',
            'subcategory' => 'USB Cable',
            'description' => '65W Fast Charging, 1 Meter - Durable USB-C cable for charging and data transfer.',
            'price' => 19.99,
            'stock' => 45,
            'image_url' => 'images/wcable.jpg',
            'features' => '65W fast charging, 1m length, durable nylon braided design, compatible with USB PD devices',
            'specifications' => '{"Brand": "Generic", "Type": "USB-C to USB-C", "Length": "1m", "Power": "65W", "Data Transfer": "Up to 10Gbps"}',
            'featured' => 1,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PROD-ACC001',
            'name' => 'ATX Ultimate Gaming Mouse Pad',
            'category' => 'Accessories',
            'subcategory' => 'Other Accessories',
            'description' => 'Small Mouse Pad - Precision-focused mousepad with non-slip base and smooth surface.',
            'price' => 19.99,
            'stock' => 60,
            'image_url' => 'images/mousepad.jpg',
            'features' => 'Smooth surface, non-slip rubber base, stitched edges for durability, small size for precision gaming',
            'specifications' => '{"Brand": "ATX", "Size": "Small", "Surface": "Cloth", "Base": "Rubber", "Thickness": "3mm"}',
            'featured' => 0,
            'on_sale' => 1,
            'sale_price' => 14.99
        ],
        [
            'id' => 'PROD-ACC002',
            'name' => 'Piranha Gaming Keyboard',
            'category' => 'Accessories',
            'subcategory' => 'Gaming Keyboard',
            'description' => 'RGB Mechanical Gaming Keyboard - Tactile mechanical switches with customizable RGB lighting.',
            'price' => 120.00,
            'stock' => 25,
            'image_url' => 'images/keyboard.jpg',
            'features' => 'Mechanical switches, RGB backlighting, programmable keys, multimedia controls, anti-ghosting',
            'specifications' => '{"Brand": "Piranha", "Type": "Mechanical", "Switches": "Blue", "Backlighting": "RGB", "Connection": "USB", "Layout": "Full-size"}',
            'featured' => 1,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PROD-ACC003',
            'name' => 'Razer DeathAdder Essential',
            'category' => 'Accessories',
            'subcategory' => 'Gaming Mouse',
            'description' => 'Gaming Mouse - High-precision gaming mouse with ergonomic design for extended comfort.',
            'price' => 40.00,
            'stock' => 35,
            'image_url' => 'images/mouse.jpg',
            'features' => '6400 DPI optical sensor, ergonomic design, mechanical switches, 5 programmable buttons',
            'specifications' => '{"Brand": "Razer", "Model": "DeathAdder Essential", "DPI": "6400", "Buttons": "5", "Connection": "Wired USB", "Weight": "96g"}',
            'featured' => 0,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PROD-ACC004',
            'name' => 'HyperX Cloud Stinger 1',
            'category' => 'Accessories',
            'subcategory' => 'Gaming Headset',
            'description' => 'Gaming Headset - Lightweight headset with excellent audio quality and rotating ear cups.',
            'price' => 70.00,
            'stock' => 20,
            'image_url' => 'images/headphones.jpg',
            'features' => '50mm directional drivers, lightweight design, adjustable steel sliders, rotating ear cups, noise-cancelling microphone',
            'specifications' => '{"Brand": "HyperX", "Model": "Cloud Stinger 1", "Driver": "50mm", "Frequency Response": "18Hz-23kHz", "Connection": "3.5mm", "Weight": "275g"}',
            'featured' => 0,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PROD-ACC005',
            'name' => 'Trust Gaming Microphone',
            'category' => 'Accessories',
            'subcategory' => 'Microphone',
            'description' => 'USB Condenser Microphone - High-quality microphone for streaming, gaming and recording.',
            'price' => 50.00,
            'stock' => 15,
            'image_url' => 'images/mic.jpg',
            'features' => 'USB connectivity, condenser capsule, cardioid pattern, zero-latency monitoring, tripod stand included',
            'specifications' => '{"Brand": "Trust", "Type": "Condenser", "Pattern": "Cardioid", "Connection": "USB", "Sample Rate": "96kHz/24-bit", "Frequency Response": "30Hz-18kHz"}',
            'featured' => 0,
            'on_sale' => 0,
            'sale_price' => NULL
        ]
    ];
    
    // Prepare statement for inserting products
    $stmt = $conn->prepare("INSERT INTO products (id, name, category, subcategory, description, price, stock, image_url, features, specifications, featured, on_sale, sale_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Check if prepare was successful
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    // Counter for successful inserts
    $success_count = 0;
    
    // Insert each product
    foreach ($shop_products as $product) {
        $stmt->bind_param("ssssssissiiid", 
            $product['id'], 
            $product['name'], 
            $product['category'], 
            $product['subcategory'], 
            $product['description'], 
            $product['price'], 
            $product['stock'], 
            $product['image_url'], 
            $product['features'], 
            $product['specifications'], 
            $product['featured'], 
            $product['on_sale'], 
            $product['sale_price']
        );
        
        if ($stmt->execute()) {
            $success_count++;
            echo "<p style='color:green'>✓ Added product: {$product['name']}</p>";
        } else {
            echo "<p style='color:red'>✗ Error adding product '{$product['name']}': " . $stmt->error . "</p>";
        }
    }
    
    echo "<p style='color:green'>✓ Successfully added $success_count of " . count($shop_products) . " products</p>";
    
    $stmt->close();
} else {
    echo "<p style='color:blue'>ℹ️ Products table already has data.</p>";
    
    // Show existing products
    echo "<h2>Existing Products</h2>";
    echo "<ul>";
    
    $result = $conn->query("SELECT id, name FROM products");
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['name']) . " (ID: " . htmlspecialchars($row['id']) . ")</li>";
    }
    
    echo "</ul>";
    
    echo "<p>To reset products, you can run: <code>TRUNCATE TABLE products;</code> in phpMyAdmin</p>";
}

// Make sure images directory exists
echo "<h2>Checking for Images Directory</h2>";
$images_path = $_SERVER['DOCUMENT_ROOT'] . '/images';
if (!file_exists($images_path)) {
    if (mkdir($images_path, 0755, true)) {
        echo "<p style='color:green'>✓ Created images directory</p>";
    } else {
        echo "<p style='color:red'>✗ Failed to create images directory</p>";
    }
} else {
    echo "<p style='color:blue'>ℹ️ Images directory already exists</p>";
}

echo "<p>Shop products setup complete. You can now <a href='shop.php'>visit the shop</a>.</p>";

// Clean up
$conn->close();
?> 