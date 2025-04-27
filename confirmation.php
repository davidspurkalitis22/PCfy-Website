<?php
// Start session
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If order information is not in session, redirect to home
if (!isset($_SESSION['order']) || empty($_SESSION['order'])) {
    header('Location: index.php');
    exit();
}

// Get order details from session
$order = $_SESSION['order'];
$orderNumber = $order['orderNumber'];
$orderDate = $order['orderDate'];
$orderTotal = $order['orderTotal'];
$items = $order['items'];

// Set page title
$pageTitle = "Order Confirmation";

// Add any additional styles
$additionalStyles = <<<EOT
<style>
    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 30px;
        text-align: center;
    }
    .order-container {
        max-width: 800px;
        margin: 40px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .order-details {
        margin-bottom: 30px;
    }
    .order-info {
        margin-bottom: 20px;
    }
    .order-info p {
        margin: 5px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    .total-row {
        font-weight: bold;
    }
    .actions {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .action-btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #6c7ae0;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
        text-align: center;
    }
    .action-btn:hover {
        background-color: #5867db;
    }
</style>
EOT;

// Include header
include 'header.php';
?>

<div class="order-container">
    <h1>Order Confirmation</h1>
    
    <div class="success-message">
        <p>Thank you for your order! Your payment has been processed successfully.</p>
    </div>
    
    <div class="order-details">
        <div class="order-info">
            <p><strong>Order Number:</strong> <?php echo htmlspecialchars($orderNumber); ?></p>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($orderDate)); ?></p>
        </div>
        
        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>€<?php echo number_format($item['price'], 2); ?></td>
                        <td>€<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Order Total:</td>
                    <td>€<?php echo number_format($orderTotal, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="actions">
        <a href="shop.php" class="action-btn">Continue Shopping</a>
        <a href="orders.php" class="action-btn">View All Orders</a>
    </div>
</div>

<script>
    // Clear cart in localStorage if it exists
    if (localStorage.getItem('cart')) {
        localStorage.removeItem('cart');
    }
</script>

<?php
// Include footer
include 'footer.php';
?>