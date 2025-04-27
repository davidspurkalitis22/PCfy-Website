<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Include database connection
require_once 'config.php';

// Set page title
$pageTitle = 'Shop';

// Add link to shop.css and script
$additionalStyles = '<link rel="stylesheet" href="css/shop.css">
<script src="js/shop-filter.js"></script>';

// Fetch all products from the database
$products = array();
$sql = "SELECT * FROM products ORDER BY featured DESC, name ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch distinct categories and subcategories
$categories = array();
$sql = "SELECT DISTINCT category, subcategory FROM products ORDER BY category ASC, subcategory ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!isset($categories[$row['category']])) {
            $categories[$row['category']] = array();
        }
        if (!empty($row['subcategory']) && !in_array($row['subcategory'], $categories[$row['category']])) {
            $categories[$row['category']][] = $row['subcategory'];
        }
    }
}

// Include header
include 'header.php';
?>

<!-- Shop content starts here -->
<section id="shop-categories" class="product-categories">
    <div class="container">
        <h2 class="section-title">Shop Our Products</h2>
        
        <div class="shop-container">
            <!-- Sidebar with filters -->
            <div class="shop-sidebar">
                <h3 class="sidebar-title">Filters</h3>
                
                <div class="sidebar-filter-group">
                    <h4>Categories</h4>
                    <div class="category-filters">
                        <button class="filter-btn active" data-filter="all">All Products</button>
                        
                        <?php foreach ($categories as $category => $subcategories): ?>
                        <button class="filter-btn" data-filter="<?php echo strtolower(str_replace(' ', '-', $category)); ?>"><?php echo $category; ?></button>
                        
                        <?php if (!empty($subcategories)): ?>
                        <div class="subcategory-menu" data-parent="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                            <?php foreach ($subcategories as $subcategory): ?>
                            <button class="subcategory-item" data-subfilter="<?php echo strtolower(str_replace(' ', '-', $subcategory)); ?>"><?php echo $subcategory; ?></button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="sidebar-filter-group">
                    <h4>Sort By</h4>
                    <select id="sort-by" class="filter-select">
                        <option value="featured">Featured</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
                
                <div class="sidebar-filter-group">
                    <h4>Brand</h4>
                    <select id="brand" class="filter-select">
                        <option value="all">All Brands</option>
                        <option value="samsung">Samsung</option>
                        <option value="corsair">Corsair</option>
                        <option value="western">Western Digital</option>
                        <option value="seagate">Seagate</option>
                        <option value="logitech">Logitech</option>
                        <option value="anker">Anker</option>
                        <option value="razer">Razer</option>
                    </select>
                </div>
            </div>
            
            <!-- Main content with products -->
            <div class="shop-main">
                <!-- Product Grid -->
                <div class="product-grid">
                    <?php if (empty($products)): ?>
                    <div class="no-products">
                        <p>No products found. Please check back later.</p>
                    </div>
                    <?php else: ?>
                    
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" 
                         data-category="<?php echo strtolower(str_replace(' ', '-', $product['category'])); ?>" 
                         data-subcategory="<?php echo strtolower(str_replace(' ', '-', $product['subcategory'])); ?>">
                        
                        <?php if ($product['featured']): ?>
                        <div class="product-badge">Featured</div>
                        <?php elseif ($product['on_sale']): ?>
                        <div class="product-badge sale">Sale</div>
                        <?php endif; ?>
                        
                        <div class="product-image">
                            <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : 'images/placeholder.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 onerror="this.src='images/placeholder.jpg'">
                        </div>
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?></p>
                            <div class="product-meta">
                                <?php if ($product['on_sale'] && !is_null($product['sale_price'])): ?>
                                <span class="product-price">
                                    <span class="original-price">€<?php echo number_format($product['price'], 2); ?></span>
                                    €<?php echo number_format($product['sale_price'], 2); ?>
                                </span>
                                <?php else: ?>
                                <span class="product-price">€<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                                
                                <!-- Star Rating - Random for now, could be actual ratings in the future -->
                                <span class="product-rating">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php if ($i < 4): ?>
                                        <i class="fas fa-star"></i>
                                        <?php else: ?>
                                        <i class="fas fa-star-half-alt"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </span>
                            </div>
                            <a href="#" class="btn btn-primary add-to-cart" 
                               data-id="<?php echo htmlspecialchars($product['id']); ?>" 
                               data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                               data-price="<?php echo $product['on_sale'] && !is_null($product['sale_price']) ? $product['sale_price'] : $product['price']; ?>" 
                               data-image="<?php echo !empty($product['image_url']) ? $product['image_url'] : 'images/placeholder.jpg'; ?>">
                                <?php echo $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php endif; ?>
                </div>

                <!-- Load More Button -->
                <div class="load-more">
                    <button id="load-more-btn" class="btn btn-primary">Load More Products</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?> 