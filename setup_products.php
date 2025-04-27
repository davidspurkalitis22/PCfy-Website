<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config.php';

echo "<h1>Setting Up Products Table</h1>";

// Create products table
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
    
    // Check if products table is empty, and if so, add some sample products
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "<p>Adding sample products...</p>";
        
        // Add sample products
        $sample_products = [
            [
                'id' => 'PROD-CPU001',
                'name' => 'Intel Core i7-11700K',
                'category' => 'Components',
                'subcategory' => 'Processors',
                'description' => 'Intel Core i7-11700K Desktop Processor 8 Cores up to 5.0 GHz Unlocked LGA1200 (Intel 500 Series & Select 400 Series Chipset) 125W',
                'price' => 359.99,
                'stock' => 25,
                'image_url' => 'images/products/cpu_intel.jpg',
                'features' => 'Intel 11th Generation, 8 Cores, 16 Threads, 3.6GHz Base Clock, 5.0GHz Turbo Clock',
                'specifications' => '{"Brand": "Intel", "Series": "Core i7", "Cores": 8, "Threads": 16, "Socket": "LGA1200", "TDP": "125W"}',
                'featured' => 1,
                'on_sale' => 0,
                'sale_price' => NULL
            ],
            [
                'id' => 'PROD-GPU001',
                'name' => 'NVIDIA GeForce RTX 3070',
                'category' => 'Components',
                'subcategory' => 'Graphics Cards',
                'description' => 'NVIDIA GeForce RTX 3070 8GB GDDR6 Graphics Card with Ampere Architecture and Ray Tracing',
                'price' => 699.99,
                'stock' => 10,
                'image_url' => 'images/products/gpu_nvidia.jpg',
                'features' => 'NVIDIA Ampere Architecture, 8GB GDDR6 Memory, Ray Tracing, DLSS AI Acceleration',
                'specifications' => '{"Brand": "NVIDIA", "Series": "RTX 3070", "Memory": "8GB", "Memory Type": "GDDR6", "Interface": "PCI Express 4.0", "Power": "220W"}',
                'featured' => 1,
                'on_sale' => 1,
                'sale_price' => 649.99
            ],
            [
                'id' => 'PROD-RAM001',
                'name' => 'Corsair Vengeance RGB Pro 32GB',
                'category' => 'Components',
                'subcategory' => 'Memory',
                'description' => 'Corsair Vengeance RGB Pro 32GB (2x16GB) DDR4 3600MHz C18 LED Desktop Memory',
                'price' => 189.99,
                'stock' => 30,
                'image_url' => 'images/products/ram_corsair.jpg',
                'features' => 'Dynamic RGB Lighting, Aluminum Heat Spreader, Intel XMP 2.0 Support',
                'specifications' => '{"Brand": "Corsair", "Series": "Vengeance RGB Pro", "Capacity": "32GB (2x16GB)", "Speed": "3600MHz", "CAS Latency": "18", "Voltage": "1.35V"}',
                'featured' => 0,
                'on_sale' => 0,
                'sale_price' => NULL
            ],
            [
                'id' => 'PROD-MB001',
                'name' => 'ASUS ROG Strix Z590-E Gaming',
                'category' => 'Components',
                'subcategory' => 'Motherboards',
                'description' => 'ASUS ROG Strix Z590-E Gaming WiFi 6E LGA 1200 ATX Motherboard',
                'price' => 379.99,
                'stock' => 15,
                'image_url' => 'images/products/mb_asus.jpg',
                'features' => 'PCIe 4.0, WiFi 6E, 2.5Gb Ethernet, USB 3.2 Gen 2x2 Type-C, Aura Sync RGB',
                'specifications' => '{"Brand": "ASUS", "Chipset": "Intel Z590", "Socket": "LGA1200", "Form Factor": "ATX", "Memory Slots": 4, "Max Memory": "128GB"}',
                'featured' => 0,
                'on_sale' => 1,
                'sale_price' => 349.99
            ],
            [
                'id' => 'PROD-SSD001',
                'name' => 'Samsung 970 EVO Plus 1TB',
                'category' => 'Storage',
                'subcategory' => 'SSD',
                'description' => 'Samsung 970 EVO Plus 1TB PCIe NVMe M.2 Internal Solid State Drive',
                'price' => 159.99,
                'stock' => 40,
                'image_url' => 'images/products/ssd_samsung.jpg',
                'features' => 'Sequential read speeds up to 3,500 MB/s, Samsung V-NAND Technology, Intelligent TurboWrite',
                'specifications' => '{"Brand": "Samsung", "Series": "970 EVO Plus", "Capacity": "1TB", "Interface": "PCIe Gen 3.0 x4, NVMe 1.3", "Form Factor": "M.2 2280", "Read Speed": "3500 MB/s", "Write Speed": "3300 MB/s"}',
                'featured' => 1,
                'on_sale' => 0,
                'sale_price' => NULL
            ]
        ];
        
        $stmt = $conn->prepare("INSERT INTO products (id, name, category, subcategory, description, price, stock, image_url, features, specifications, featured, on_sale, sale_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $success_count = 0;
        foreach ($sample_products as $product) {
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
            } else {
                echo "<p style='color:red'>✗ Error adding product '{$product['name']}': " . $stmt->error . "</p>";
            }
        }
        
        echo "<p style='color:green'>✓ Successfully added $success_count sample products</p>";
        
        $stmt->close();
    } else {
        echo "<p style='color:blue'>ℹ️ Products table already has data. Skipping sample product creation.</p>";
    }
} else {
    echo "<p style='color:red'>✗ Error creating products table: " . $conn->error . "</p>";
}

// Create product-related directories if they don't exist
echo "<h2>Setting Up Product Images Directory</h2>";

$image_path = $_SERVER['DOCUMENT_ROOT'] . '/images/products';
if (!file_exists($image_path)) {
    if (mkdir($image_path, 0755, true)) {
        echo "<p style='color:green'>✓ Created products image directory</p>";
    } else {
        echo "<p style='color:red'>✗ Failed to create products image directory</p>";
    }
} else {
    echo "<p style='color:blue'>ℹ️ Products image directory already exists</p>";
}

echo "<p>Products setup complete. You can now <a href='shop.php'>visit the shop</a>.</p>";

// Clean up
$conn->close();
?> 