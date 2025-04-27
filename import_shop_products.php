<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config.php';

echo "<h1>Setting Up Shop Products</h1>";

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
    echo "<p>Adding your PCFY shop products...</p>";
    
    // Your actual PCFY shop products
    $pcfy_products = [
        [
            'id' => 'PC-GAMING-001',
            'name' => 'PCFY Gaming PC',
            'category' => 'Gaming PCs',
            'subcategory' => 'Custom Builds',
            'description' => 'A high-performance gaming PC with RGB lighting, latest generation CPU and GPU for an immersive gaming experience.',
            'price' => 1499.99,
            'stock' => 5,
            'image_url' => 'pcbackground.jpg',
            'features' => 'RGB Lighting, Liquid Cooling, Tempered Glass Case, 3-Year Warranty',
            'specifications' => '{"CPU": "Intel Core i7", "GPU": "NVIDIA RTX 3070", "RAM": "16GB", "Storage": "1TB SSD", "Motherboard": "ASUS ROG", "Power Supply": "750W Gold"}',
            'featured' => 1,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PC-OFFICE-001',
            'name' => 'PCFY Office PC',
            'category' => 'Office PCs',
            'subcategory' => 'Business',
            'description' => 'Reliable and efficient office PC for productivity and business applications. Perfect for work-from-home setups.',
            'price' => 899.99,
            'stock' => 10,
            'image_url' => 'pc1.png',
            'features' => 'Compact Design, Silent Operation, Business Software, 2-Year Warranty',
            'specifications' => '{"CPU": "Intel Core i5", "GPU": "Integrated Graphics", "RAM": "8GB", "Storage": "512GB SSD", "Motherboard": "ASUS Prime", "Power Supply": "500W Bronze"}',
            'featured' => 0,
            'on_sale' => 1,
            'sale_price' => 799.99
        ],
        [
            'id' => 'PC-STUDENT-001',
            'name' => 'PCFY Student PC',
            'category' => 'Student PCs',
            'subcategory' => 'Budget',
            'description' => 'Affordable PC for students with all the essentials for schoolwork, research and light entertainment.',
            'price' => 699.99,
            'stock' => 15,
            'image_url' => 'pc2.png',
            'features' => 'Budget-Friendly, Compact, Educational Software Bundle, 1-Year Warranty',
            'specifications' => '{"CPU": "AMD Ryzen 5", "GPU": "Integrated Graphics", "RAM": "8GB", "Storage": "256GB SSD", "Motherboard": "ASRock", "Power Supply": "450W"}',
            'featured' => 1,
            'on_sale' => 1,
            'sale_price' => 649.99
        ],
        [
            'id' => 'PC-WORKSTATION-001',
            'name' => 'PCFY Workstation PC',
            'category' => 'Workstation PCs',
            'subcategory' => 'Professional',
            'description' => 'High-end workstation for professional creative work, 3D modeling, video editing and other demanding applications.',
            'price' => 2499.99,
            'stock' => 3,
            'image_url' => 'pc3.png',
            'features' => 'Professional Grade, ECC Memory, Quadro Graphics, 3-Year Warranty with Priority Support',
            'specifications' => '{"CPU": "AMD Threadripper", "GPU": "NVIDIA Quadro", "RAM": "64GB ECC", "Storage": "2TB NVMe SSD", "Motherboard": "ASUS Pro", "Power Supply": "1000W Platinum"}',
            'featured' => 0,
            'on_sale' => 0,
            'sale_price' => NULL
        ],
        [
            'id' => 'PC-STREAMING-001',
            'name' => 'PCFY Streaming PC',
            'category' => 'Streaming PCs',
            'subcategory' => 'Content Creator',
            'description' => 'Optimized for content creators and streamers with multi-tasking capabilities and dedicated encoding.',
            'price' => 1799.99,
            'stock' => 7,
            'image_url' => 'pc1.png',
            'features' => 'Dedicated Stream Encoding, RGB Lighting, Streaming Software Bundle, 3-Year Warranty',
            'specifications' => '{"CPU": "AMD Ryzen 9", "GPU": "NVIDIA RTX 3080", "RAM": "32GB", "Storage": "1TB NVMe SSD", "Motherboard": "MSI Gaming", "Power Supply": "850W Gold"}',
            'featured' => 1,
            'on_sale' => 0,
            'sale_price' => NULL
        ]
    ];
    
    $stmt = $conn->prepare("INSERT INTO products (id, name, category, subcategory, description, price, stock, image_url, features, specifications, featured, on_sale, sale_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $success_count = 0;
    foreach ($pcfy_products as $product) {
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
    
    echo "<p style='color:green'>✓ Successfully added $success_count PCFY shop products</p>";
    
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

// Make sure image files are accessible
echo "<h2>Checking for Product Images</h2>";
$images_to_check = ['pc1.png', 'pc2.png', 'pc3.png', 'pcbackground.jpg'];

echo "<ul>";
foreach ($images_to_check as $image) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image)) {
        echo "<li style='color:green'>✓ Image file exists: $image</li>";
    } else {
        echo "<li style='color:red'>✗ Image file missing: $image - please upload it to your server</li>";
    }
}
echo "</ul>";

echo "<p>Shop products setup complete. You can now <a href='shop.php'>visit the shop</a>.</p>";

// Clean up
$conn->close();
?> 