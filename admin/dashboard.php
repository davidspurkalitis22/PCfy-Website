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

// Get basic stats
$stats = array(
    'users' => 0,
    'orders' => 0,
    'repair_bookings' => 0,
    'revenue' => 0
);

// Get user count
$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stats['users'] = $row['count'];
}

// Get orders count
$sql = "SELECT COUNT(*) as count, SUM(order_total) as total FROM orders";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stats['orders'] = $row['count'] ?: 0;
    $stats['revenue'] = $row['total'] ?: 0;
}

// Get repair bookings count
$sql = "SELECT COUNT(*) as count FROM repair_bookings";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stats['repair_bookings'] = $row['count'];
}

// Get recent orders
$recentOrders = array();
$sql = "SELECT order_number, first_name, last_name, order_date, order_total, order_status 
        FROM orders ORDER BY order_date DESC LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}

// Get recent repair bookings
$recentBookings = array();
$sql = "SELECT id, name, service_type, created_at, status 
        FROM repair_bookings ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentBookings[] = $row;
    }
}

// Page title
$pageTitle = "Admin Dashboard";
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #6c7ae0;
            font-size: 16px;
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
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
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
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
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="repair_bookings.php"><i class="fas fa-tools"></i> Repair Bookings</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1><?php echo $pageTitle; ?></h1>
                <div>
                    <span>Welcome, <?php echo $_SESSION['firstname']; ?></span>
                </div>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value"><?php echo $stats['users']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <div class="value"><?php echo $stats['orders']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value">€<?php echo number_format($stats['revenue'], 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Repair Bookings</h3>
                    <div class="value"><?php echo $stats['repair_bookings']; ?></div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card">
                <h2>Recent Orders</h2>
                <?php if (count($recentOrders) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><?php echo $order['order_number']; ?></td>
                                <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                <td>€<?php echo number_format($order['order_total'], 2); ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($order['order_status']) {
                                        case 'pending':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'processing':
                                            $statusClass = 'badge-info';
                                            break;
                                        case 'completed':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'badge-danger';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($order['order_status']); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No orders found</p>
                <?php endif; ?>
            </div>
            
            <!-- Recent Repair Bookings -->
            <div class="card">
                <h2>Recent Repair Bookings</h2>
                <?php if (count($recentBookings) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo $booking['name']; ?></td>
                                <td><?php echo $booking['service_type']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($booking['status']) {
                                        case 'pending':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'confirmed':
                                            $statusClass = 'badge-info';
                                            break;
                                        case 'in_progress':
                                            $statusClass = 'badge-info';
                                            break;
                                        case 'completed':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'badge-danger';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No repair bookings found</p>
                <?php endif; ?>
            </div>
            
            <div class="admin-footer">
                <p>&copy; <?php echo date('Y'); ?> PCFY Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html> 