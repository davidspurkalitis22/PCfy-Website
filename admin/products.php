<?php
// Start session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['is_admin'] !== 1) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once '../config.php';

// Initialize message variables
$success_message = '';
$error_message = '';

// Process product deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $product_id = $conn->real_escape_string($_GET['delete']);
    
    // Delete the product
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("s", $product_id);
    
    if ($stmt->execute()) {
        $success_message = "Product deleted successfully.";
    } else {
        $error_message = "Error deleting product: " . $conn->error;
    }
    $stmt->close();
}

// Process form submissions for adding or editing products
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        // Get form data
        $product_id = isset($_POST['product_id']) ? $conn->real_escape_string($_POST['product_id']) : '';
        $name = $conn->real_escape_string($_POST['name']);
        $category = $conn->real_escape_string($_POST['category']);
        $subcategory = $conn->real_escape_string($_POST['subcategory']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $image_url = $conn->real_escape_string($_POST['image_url']);
        $features = $conn->real_escape_string($_POST['features']);
        $specifications = $conn->real_escape_string($_POST['specifications']);
        $featured = isset($_POST['featured']) ? 1 : 0;
        $on_sale = isset($_POST['on_sale']) ? 1 : 0;
        $sale_price = isset($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        
        // Add new product
        if ($_POST['action'] == 'add') {
            // Generate a unique product ID if not provided
            if (empty($product_id)) {
                $product_id = 'PROD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            }
            
            // Check if product ID already exists
            $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
            $stmt->bind_param("s", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = "Product ID already exists!";
            } else {
                // Insert new product
                $stmt = $conn->prepare("INSERT INTO products (id, name, category, subcategory, description, price, stock, image_url, features, specifications, featured, on_sale, sale_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssissiiid", $product_id, $name, $category, $subcategory, $description, $price, $stock, $image_url, $features, $specifications, $featured, $on_sale, $sale_price);
                
                if ($stmt->execute()) {
                    $success_message = "Product added successfully.";
                } else {
                    $error_message = "Error adding product: " . $conn->error;
                }
            }
            $stmt->close();
        }
        
        // Update existing product
        else if ($_POST['action'] == 'edit') {
            // Update product
            $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, subcategory = ?, description = ?, price = ?, stock = ?, image_url = ?, features = ?, specifications = ?, featured = ?, on_sale = ?, sale_price = ? WHERE id = ?");
            $stmt->bind_param("ssssdissiiids", $name, $category, $subcategory, $description, $price, $stock, $image_url, $features, $specifications, $featured, $on_sale, $sale_price, $product_id);
            
            if ($stmt->execute()) {
                $success_message = "Product updated successfully.";
            } else {
                $error_message = "Error updating product: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Get all products
$products = array();
$sql = "SELECT id, name, category, subcategory, price, stock, featured, on_sale, sale_price FROM products ORDER BY name ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get categories for dropdown
$categories = array();
$sql = "SELECT DISTINCT category FROM products ORDER BY category ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Page title
$pageTitle = "Product Management";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - PCFY</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 250px;
            background-color: #212529;
            color: white;
            padding: 20px 0;
        }
        
        .admin-sidebar .brand {
            font-size: 24px;
            font-weight: bold;
            padding: 0 20px;
            margin-bottom: 30px;
        }
        
        .admin-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .admin-sidebar li {
            padding: 0;
        }
        
        .admin-sidebar a {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .admin-sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .admin-content {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card h2 {
            margin-top: 0;
            color: #333;
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: #f9f9f9;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #ddd;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .table tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            background-color: #6c7ae0;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-primary {
            background-color: #6c7ae0;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .form-row {
            display: flex;
            margin: 0 -10px;
            flex-wrap: wrap;
        }
        
        .form-col {
            flex: 1;
            padding: 0 10px;
            min-width: 200px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
        }
        
        .modal-content {
            position: relative;
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }
        
        .close:hover {
            color: #333;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
        }
        
        .checkbox-container input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .action-buttons {
            white-space: nowrap;
        }
        
        .admin-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="brand">PCFY Admin</div>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="repair_bookings.php"><i class="fas fa-tools"></i> Repair Bookings</a></li>
                <li><a href="products.php" class="active"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1><?php echo $pageTitle; ?></h1>
                <div>
                    <button onclick="openAddModal()" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</button>
                </div>
            </div>
            
            <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <!-- Products Table -->
            <div class="card">
                <h2>All Products</h2>
                <?php if (count($products) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Subcategory</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category']; ?></td>
                                <td><?php echo $product['subcategory']; ?></td>
                                <td>
                                    <?php if ($product['on_sale'] == 1 && !is_null($product['sale_price'])): ?>
                                    <del>€<?php echo number_format($product['price'], 2); ?></del>
                                    <span class="text-danger">€<?php echo number_format($product['sale_price'], 2); ?></span>
                                    <?php else: ?>
                                    €<?php echo number_format($product['price'], 2); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['stock'] > 0): ?>
                                    <span class="badge badge-success"><?php echo $product['stock']; ?> in stock</span>
                                    <?php else: ?>
                                    <span class="badge badge-danger">Out of stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['featured'] == 1): ?>
                                    <span class="badge badge-primary">Featured</span>
                                    <?php endif; ?>
                                    <?php if ($product['on_sale'] == 1): ?>
                                    <span class="badge badge-warning">On Sale</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <button onclick="loadProductDetails('<?php echo $product['id']; ?>')" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No products found</p>
                <?php endif; ?>
            </div>
            
            <!-- Product Modal (for both Add and Edit) -->
            <div id="productModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeProductModal()">&times;</span>
                    <h2 id="modalTitle">Add New Product</h2>
                    <form method="post" action="" id="productForm">
                        <input type="hidden" name="action" id="form_action" value="add">
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="product_id">Product ID</label>
                                    <input type="text" id="product_id" name="product_id" class="form-control" placeholder="Leave blank to auto-generate">
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="name">Product Name</label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <input type="text" id="category" name="category" class="form-control" list="category-list" required>
                                    <datalist id="category-list">
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category; ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="subcategory">Subcategory</label>
                                    <input type="text" id="subcategory" name="subcategory" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="price">Price (€)</label>
                                    <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="stock">Stock</label>
                                    <input type="number" id="stock" name="stock" class="form-control" min="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col checkbox-container">
                                <div class="form-group">
                                    <input type="checkbox" id="featured" name="featured" value="1">
                                    <label for="featured">Featured Product</label>
                                </div>
                            </div>
                            <div class="form-col checkbox-container">
                                <div class="form-group">
                                    <input type="checkbox" id="on_sale" name="on_sale" value="1" onchange="toggleSalePrice()">
                                    <label for="on_sale">On Sale</label>
                                </div>
                            </div>
                            <div class="form-col" id="sale_price_container" style="display: none;">
                                <div class="form-group">
                                    <label for="sale_price">Sale Price (€)</label>
                                    <input type="number" id="sale_price" name="sale_price" class="form-control" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="image_url">Image URL</label>
                            <input type="text" id="image_url" name="image_url" class="form-control" placeholder="Path to product image">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="features">Features (comma separated)</label>
                                    <textarea id="features" name="features" class="form-control" rows="3" placeholder="Feature 1, Feature 2, Feature 3"></textarea>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="specifications">Specifications (JSON format)</label>
                                    <textarea id="specifications" name="specifications" class="form-control" rows="3" placeholder='{"Processor": "Intel i7", "RAM": "16GB"}'></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="saveButton">Save Product</button>
                            <button type="button" class="btn" onclick="closeProductModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="admin-footer">
                <p>&copy; <?php echo date('Y'); ?> PCFY Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <script>
        // Product Modal
        var productModal = document.getElementById("productModal");
        
        function openAddModal() {
            document.getElementById("modalTitle").textContent = "Add New Product";
            document.getElementById("form_action").value = "add";
            document.getElementById("productForm").reset();
            document.getElementById("saveButton").textContent = "Add Product";
            toggleSalePrice(); // Reset sale price visibility
            
            productModal.style.display = "block";
        }
        
        function loadProductDetails(productId) {
            // AJAX call to get product details
            fetch(`get_product.php?id=${productId}`)
                .then(response => response.json())
                .then(product => {
                    // Fill form with product details
                    document.getElementById("modalTitle").textContent = "Edit Product";
                    document.getElementById("form_action").value = "edit";
                    document.getElementById("product_id").value = product.id;
                    document.getElementById("product_id").readOnly = true;
                    document.getElementById("name").value = product.name;
                    document.getElementById("category").value = product.category;
                    document.getElementById("subcategory").value = product.subcategory;
                    document.getElementById("price").value = product.price;
                    document.getElementById("stock").value = product.stock;
                    document.getElementById("image_url").value = product.image_url;
                    document.getElementById("description").value = product.description;
                    document.getElementById("features").value = product.features;
                    document.getElementById("specifications").value = product.specifications;
                    document.getElementById("featured").checked = product.featured == 1;
                    document.getElementById("on_sale").checked = product.on_sale == 1;
                    document.getElementById("sale_price").value = product.sale_price;
                    document.getElementById("saveButton").textContent = "Update Product";
                    
                    toggleSalePrice(); // Update sale price visibility
                    
                    productModal.style.display = "block";
                })
                .catch(error => {
                    console.error('Error loading product details:', error);
                    alert('Error loading product details. Please try again.');
                });
        }
        
        function closeProductModal() {
            productModal.style.display = "none";
            document.getElementById("product_id").readOnly = false;
            document.getElementById("productForm").reset();
        }
        
        function toggleSalePrice() {
            var onSaleCheckbox = document.getElementById("on_sale");
            var salePriceContainer = document.getElementById("sale_price_container");
            
            if (onSaleCheckbox.checked) {
                salePriceContainer.style.display = "block";
            } else {
                salePriceContainer.style.display = "none";
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == productModal) {
                closeProductModal();
            }
        }
        
        // For demonstration, since we don't have a backend endpoint for get_product.php
        // This is a fallback if the fetch call fails
        function loadProductDetailsFallback(productId) {
            // Find the product in the table and extract fields
            var rows = document.querySelectorAll('.table tbody tr');
            var product = null;
            
            rows.forEach(row => {
                var cells = row.querySelectorAll('td');
                if (cells[0].textContent === productId) {
                    // Basic details from the table
                    var priceText = cells[4].textContent.replace('€', '').trim();
                    if (priceText.includes('€')) {
                        // Handle sale price
                        var prices = cells[4].textContent.match(/€([0-9.]+)/g);
                        var regularPrice = prices[0].replace('€', '');
                        var salePrice = prices[1].replace('€', '');
                    } else {
                        var regularPrice = priceText;
                        var salePrice = '';
                    }
                    
                    var featured = cells[6].textContent.includes('Featured');
                    var onSale = cells[6].textContent.includes('On Sale');
                    
                    document.getElementById("modalTitle").textContent = "Edit Product";
                    document.getElementById("form_action").value = "edit";
                    document.getElementById("product_id").value = productId;
                    document.getElementById("product_id").readOnly = true;
                    document.getElementById("name").value = cells[1].textContent;
                    document.getElementById("category").value = cells[2].textContent;
                    document.getElementById("subcategory").value = cells[3].textContent;
                    document.getElementById("price").value = regularPrice;
                    
                    // Extract stock from the badge
                    var stockText = cells[5].querySelector('.badge').textContent;
                    var stockMatch = stockText.match(/(\d+)/);
                    document.getElementById("stock").value = stockMatch ? stockMatch[1] : 0;
                    
                    // Set featured and on_sale checkboxes
                    document.getElementById("featured").checked = featured;
                    document.getElementById("on_sale").checked = onSale;
                    if (onSale) {
                        document.getElementById("sale_price").value = salePrice;
                    }
                    
                    document.getElementById("saveButton").textContent = "Update Product";
                    
                    toggleSalePrice(); // Update sale price visibility
                    
                    productModal.style.display = "block";
                }
            });
            
            // If no product found by traversing the DOM, show a message
            if (!product) {
                alert('Error: Product details not available. Please try again.');
            }
        }
    </script>
</body>
</html> 