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

// Process booking status update
if (isset($_POST['update_status']) && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $technician_notes = isset($_POST['technician_notes']) ? $conn->real_escape_string($_POST['technician_notes']) : '';
    
    // Update booking status and notes
    $stmt = $conn->prepare("UPDATE repair_bookings SET status = ?, technician_notes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $technician_notes, $booking_id);
    
    if ($stmt->execute()) {
        $success_message = "Booking status updated successfully.";
    } else {
        $error_message = "Error updating booking status: " . $conn->error;
    }
    $stmt->close();
}

// Delete booking
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $booking_id = (int)$_GET['delete'];
    
    // Delete the booking
    $stmt = $conn->prepare("DELETE FROM repair_bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $success_message = "Booking deleted successfully.";
    } else {
        $error_message = "Error deleting booking: " . $conn->error;
    }
    $stmt->close();
}

// Get all repair bookings
$bookings = array();

// Filter by status if provided
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$where_clause = '';

if (!empty($status_filter) && $status_filter != 'all') {
    $status_filter = $conn->real_escape_string($status_filter);
    $where_clause = "WHERE status = '$status_filter'";
}

$sql = "SELECT id, user_id, name, email, phone, service_type, issue_description, preferred_date, status, technician_notes, created_at, updated_at FROM repair_bookings $where_clause ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Page title
$pageTitle = "Repair Bookings Management";
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
        
        .btn-success {
            background-color: #28a745;
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
        
        .badge-primary {
            background-color: #6c7ae0;
            color: white;
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
        
        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
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
            width: 60%;
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
        
        .status-filter {
            margin-bottom: 20px;
        }
        
        .status-filter a {
            display: inline-block;
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .status-filter a.active {
            background-color: #6c7ae0;
            color: white;
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
        
        .issue-description, .technician-notes {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .modal-body .issue-description, .modal-body .technician-notes {
            max-width: 100%;
            white-space: pre-wrap;
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
                <li><a href="repair_bookings.php" class="active"><i class="fas fa-tools"></i> Repair Bookings</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1><?php echo $pageTitle; ?></h1>
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
            
            <!-- Status Filter -->
            <div class="status-filter">
                <strong>Filter by status:</strong>
                <a href="?status=all" class="<?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'active' : ''; ?>">All</a>
                <a href="?status=pending" class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'active' : ''; ?>">Pending</a>
                <a href="?status=confirmed" class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'active' : ''; ?>">Confirmed</a>
                <a href="?status=in_progress" class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'in_progress') ? 'active' : ''; ?>">In Progress</a>
                <a href="?status=completed" class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : ''; ?>">Completed</a>
                <a href="?status=cancelled" class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'active' : ''; ?>">Cancelled</a>
            </div>
            
            <!-- Repair Bookings Table -->
            <div class="card">
                <h2>Repair Bookings</h2>
                <?php if (count($bookings) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Service Type</th>
                                <th>Issue</th>
                                <th>Preferred Date</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td>
                                    <strong><?php echo $booking['name']; ?></strong><br>
                                    <small><?php echo $booking['email']; ?></small><br>
                                    <small><?php echo $booking['phone']; ?></small>
                                </td>
                                <td><?php echo $booking['service_type']; ?></td>
                                <td class="issue-description" title="<?php echo htmlspecialchars($booking['issue_description']); ?>">
                                    <?php echo htmlspecialchars($booking['issue_description']); ?>
                                </td>
                                <td>
                                    <?php 
                                    echo !empty($booking['preferred_date']) 
                                        ? date('M j, Y', strtotime($booking['preferred_date'])) 
                                        : 'Not specified'; 
                                    ?>
                                </td>
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
                                            $statusClass = 'badge-primary';
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
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <button onclick="openDetailsModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <a href="?delete=<?php echo $booking['id']; ?>" onclick="return confirm('Are you sure you want to delete this booking?');" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
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
            
            <!-- Booking Details Modal -->
            <div id="bookingDetailsModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeDetailsModal()">&times;</span>
                    <h2>Booking Details</h2>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <h3>Customer Information</h3>
                                <p><strong>Name:</strong> <span id="modal-name"></span></p>
                                <p><strong>Email:</strong> <span id="modal-email"></span></p>
                                <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
                            </div>
                            <div class="col">
                                <h3>Service Information</h3>
                                <p><strong>Service Type:</strong> <span id="modal-service-type"></span></p>
                                <p><strong>Preferred Date:</strong> <span id="modal-preferred-date"></span></p>
                                <p><strong>Created:</strong> <span id="modal-created-at"></span></p>
                                <p><strong>Updated:</strong> <span id="modal-updated-at"></span></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col">
                                <h3>Issue Description</h3>
                                <div class="issue-description" id="modal-issue-description"></div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <form method="post" action="">
                            <input type="hidden" id="modal-booking-id" name="booking_id" value="">
                            
                            <div class="form-group">
                                <label for="modal-status">Status</label>
                                <select id="modal-status" name="status" class="form-control">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="modal-technician-notes">Technician Notes</label>
                                <textarea id="modal-technician-notes" name="technician_notes" class="form-control" rows="5"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                <button type="button" class="btn" onclick="closeDetailsModal()">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="admin-footer">
                <p>&copy; <?php echo date('Y'); ?> PCFY Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <script>
        // Booking Details Modal
        var bookingDetailsModal = document.getElementById("bookingDetailsModal");
        
        function openDetailsModal(booking) {
            // Set values in the modal
            document.getElementById("modal-booking-id").value = booking.id;
            document.getElementById("modal-name").textContent = booking.name;
            document.getElementById("modal-email").textContent = booking.email;
            document.getElementById("modal-phone").textContent = booking.phone;
            document.getElementById("modal-service-type").textContent = booking.service_type;
            
            // Format preferred date
            var preferredDate = booking.preferred_date 
                ? new Date(booking.preferred_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
                : 'Not specified';
            document.getElementById("modal-preferred-date").textContent = preferredDate;
            
            document.getElementById("modal-created-at").textContent = new Date(booking.created_at).toLocaleString();
            document.getElementById("modal-updated-at").textContent = new Date(booking.updated_at).toLocaleString();
            document.getElementById("modal-issue-description").textContent = booking.issue_description;
            document.getElementById("modal-technician-notes").value = booking.technician_notes || '';
            
            // Set the current status
            var statusSelect = document.getElementById("modal-status");
            for (var i = 0; i < statusSelect.options.length; i++) {
                if (statusSelect.options[i].value === booking.status) {
                    statusSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Show the modal
            bookingDetailsModal.style.display = "block";
        }
        
        function closeDetailsModal() {
            bookingDetailsModal.style.display = "none";
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == bookingDetailsModal) {
                bookingDetailsModal.style.display = "none";
            }
        }
    </script>
</body>
</html> 