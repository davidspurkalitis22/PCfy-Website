<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Include database configuration
require_once 'config.php';

// Check if the connection exists
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed. Please check your database configuration.");
}

// Get orders from database
$sql = "SHOW COLUMNS FROM orders";
$result = $conn->query($sql);
$columns = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
}

// Determine which date column to use for ordering
$dateColumn = 'id'; // Default to id
if (in_array('created_at', $columns)) {
    $dateColumn = 'created_at';
} else if (in_array('order_date', $columns)) {
    $dateColumn = 'order_date';
} else if (in_array('date', $columns)) {
    $dateColumn = 'date';
}

// Get user email to fetch only their orders
$userEmail = $_SESSION['email'];

// Determine which email column to use
$emailColumn = '';
if (in_array('customer_email', $columns)) {
    $emailColumn = 'customer_email';
} else if (in_array('email', $columns)) {
    $emailColumn = 'email';
} else if (in_array('user_email', $columns)) {
    $emailColumn = 'user_email';
}

// Get all orders - if we have an email column, filter by user email
if (!empty($emailColumn)) {
    $sql = "SELECT * FROM orders WHERE $emailColumn = ? ORDER BY $dateColumn DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // If no email column, just get all orders (for now)
    $sql = "SELECT * FROM orders ORDER BY $dateColumn DESC";
    $result = $conn->query($sql);
}

// Function to get order items
function getOrderItems($conn, $orderId) {
    $sql = "SELECT * FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
    return $items;
}

// Set page title
$pageTitle = "My Orders";

// Add any additional styles
$additionalStyles = <<<EOT
<style>
    .orders-container {
        max-width: 1200px;
        margin: 40px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    tr:hover {
        background-color: #f9f9f9;
    }
    .status-completed {
        color: green;
        font-weight: bold;
    }
    .status-pending {
        color: orange;
        font-weight: bold;
    }
    .order-details {
        display: none;
        background-color: #f9f9f9;
        padding: 15px;
        margin: 10px 0;
        border-radius: 5px;
    }
    .detail-btn {
        background-color: #6c7ae0;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    .detail-btn:hover {
        background-color: #5867db;
    }
    .empty-message {
        text-align: center;
        margin: 50px 0;
        color: #777;
    }
    h1 {
        color: #6c7ae0;
        text-align: center;
        margin-bottom: 30px;
    }
    .inner-table {
        width: 100%;
        margin-top: 10px;
    }
    .inner-table th {
        background-color: #e9ecef;
    }
    .order-total {
        font-size: 16px;
        margin-bottom: 15px;
        color: #6c7ae0;
        font-weight: 500;
    }
    .debug-info {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        padding: 10px;
        margin-top: 20px;
        border-radius: 5px;
        font-family: monospace;
        font-size: 12px;
        white-space: pre-wrap;
    }
</style>
EOT;

// Include header
include 'header.php';
?>

<div class="orders-container">
    <h1>My Orders</h1>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo isset($row['order_number']) ? htmlspecialchars($row['order_number']) : 'N/A'; ?></td>
                        <td><?php echo isset($row['payment_method']) ? htmlspecialchars($row['payment_method']) : 'N/A'; ?></td>
                        <td class="<?php echo isset($row['status']) ? 'status-'.strtolower($row['status']) : ''; ?>">
                            <?php echo isset($row['status']) ? htmlspecialchars($row['status']) : 'N/A'; ?>
                        </td>
                        <td>
                            <?php 
                            // Find the date field and format it
                            $dateValue = '';
                            if (isset($row['created_at']) && !empty($row['created_at'])) {
                                $dateValue = $row['created_at'];
                            } else if (isset($row['order_date']) && !empty($row['order_date'])) {
                                $dateValue = $row['order_date'];
                            } else if (isset($row['date']) && !empty($row['date'])) {
                                $dateValue = $row['date'];
                            }
                            
                            if (!empty($dateValue)) {
                                echo date('M d, Y H:i', strtotime($dateValue));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td>
                            <button class="detail-btn" onclick="toggleDetails(<?php echo $row['id']; ?>)">View Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div id="details-<?php echo $row['id']; ?>" class="order-details">
                                <h3>Order Items</h3>
                                <?php 
                                // Display order total
                                if (isset($row['total_amount']) && !empty($row['total_amount'])) {
                                    echo '<p class="order-total"><strong>Order Total:</strong> €' . number_format($row['total_amount'], 2) . '</p>';
                                }
                                
                                $items = getOrderItems($conn, $row['id']);
                                if (count($items) > 0):
                                ?>
                                <table class="inner-table">
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
                                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>€<?php echo number_format($item['price'], 2); ?></td>
                                                <td>€<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                    <p>No items found for this order.</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-message">
            <h2>No orders found</h2>
            <p>You haven't placed any orders yet. <a href="shop.php">Browse our shop</a> to make your first purchase!</p>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleDetails(orderId) {
        const detailsElement = document.getElementById('details-' + orderId);
        if (detailsElement.style.display === 'block') {
            detailsElement.style.display = 'none';
        } else {
            detailsElement.style.display = 'block';
        }
    }
</script>

<?php
// Include footer
include 'footer.php';
?> 