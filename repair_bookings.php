<?php
// Start session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Include database configuration
require_once '../config.php';

// Update booking status if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id = $_POST['booking_id'];
    $status = $_POST['status'];
    $notes = $_POST['technician_notes'];
    
    try {
        $stmt = $conn->prepare("UPDATE repair_bookings SET status = ?, technician_notes = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $notes, $id);
        
        if ($stmt->execute()) {
            $_SESSION['admin_message'] = "Booking #$id updated successfully";
        } else {
            $_SESSION['admin_error'] = "Error updating booking";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['admin_error'] = "Database error";
    }
    
    // Redirect to refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Delete booking if requested
if (isset($_GET['delete']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM repair_bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['admin_message'] = "Booking #$id deleted successfully";
        } else {
            $_SESSION['admin_error'] = "Error deleting booking";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['admin_error'] = "Database error";
    }
    
    // Redirect to refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all repair bookings
try {
    $stmt = $conn->prepare("SELECT * FROM repair_bookings ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $bookings = [];
    $_SESSION['admin_error'] = "Error fetching bookings";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Repair Bookings</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            color: white;
        }
        .status-pending {
            background-color: #f0ad4e;
        }
        .status-confirmed {
            background-color: #5bc0de;
        }
        .status-in_progress {
            background-color: #0275d8;
        }
        .status-completed {
            background-color: #5cb85c;
        }
        .status-cancelled {
            background-color: #d9534f;
        }
        .booking-details {
            background-color: #f9f9f9;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .booking-actions {
            display: flex;
            gap: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            max-width: 500px;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-btn:hover {
            color: black;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-content">
            <header class="admin-header">
                <h1>Repair Bookings</h1>
                <div class="admin-header-actions">
                    <a href="dashboard.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </header>
            
            <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['admin_message']; unset($_SESSION['admin_message']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
            </div>
            <?php endif; ?>
            
            <div class="admin-table-container">
                <div class="admin-table-header">
                    <div class="admin-table-filters">
                        <button class="btn btn-outline filter-btn active" data-filter="all">All</button>
                        <button class="btn btn-outline filter-btn" data-filter="pending">Pending</button>
                        <button class="btn btn-outline filter-btn" data-filter="confirmed">Confirmed</button>
                        <button class="btn btn-outline filter-btn" data-filter="in_progress">In Progress</button>
                        <button class="btn btn-outline filter-btn" data-filter="completed">Completed</button>
                        <button class="btn btn-outline filter-btn" data-filter="cancelled">Cancelled</button>
                    </div>
                </div>
                
                <?php if (empty($bookings)): ?>
                <div class="empty-state">
                    <i class="fas fa-tools empty-icon"></i>
                    <h2>No repair bookings found</h2>
                    <p>Once customers make repair requests, they will appear here.</p>
                </div>
                <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Service Type</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr class="booking-row" data-status="<?php echo $booking['status']; ?>">
                            <td>#<?php echo $booking['id']; ?></td>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($booking['name']); ?></strong>
                                </div>
                                <div class="table-subtitle">
                                    <?php echo htmlspecialchars($booking['email']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($booking['service_type']); ?></td>
                            <td>
                                <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                <?php if ($booking['preferred_date']): ?>
                                <div class="table-subtitle">
                                    Preferred: <?php echo date('M d, Y', strtotime($booking['preferred_date'])); ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $booking['status']; ?>">
                                    <?php echo str_replace('_', ' ', $booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="booking-actions">
                                    <button class="btn btn-info btn-sm view-btn" data-id="<?php echo $booking['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $booking['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=1&id=<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this booking?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- View Booking Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Booking Details</h2>
            <div id="bookingDetails"></div>
        </div>
    </div>
    
    <!-- Edit Booking Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Update Booking</h2>
            <form id="updateForm" method="post" action="">
                <input type="hidden" id="booking_id" name="booking_id">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="technician_notes">Technician Notes</label>
                    <textarea id="technician_notes" name="technician_notes" class="form-control" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="update_status" class="btn btn-primary">Update Booking</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const bookingRows = document.querySelectorAll('.booking-row');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    bookingRows.forEach(row => {
                        if (filter === 'all' || row.getAttribute('data-status') === filter) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
            
            // View Booking Modal
            const viewButtons = document.querySelectorAll('.view-btn');
            const viewModal = document.getElementById('viewModal');
            const bookingDetails = document.getElementById('bookingDetails');
            
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-id');
                    const booking = <?php echo json_encode($bookings); ?>.find(b => b.id == bookingId);
                    
                    if (booking) {
                        let statusClass = `status-${booking.status}`;
                        let statusText = booking.status.replace('_', ' ');
                        let preferredDateText = booking.preferred_date 
                            ? `<p><strong>Preferred Date:</strong> ${new Date(booking.preferred_date).toLocaleDateString()}</p>` 
                            : '';
                        let technicianNotes = booking.technician_notes 
                            ? `<div class="booking-notes"><h4>Technician Notes</h4><p>${booking.technician_notes}</p></div>` 
                            : '';
                        
                        bookingDetails.innerHTML = `
                            <div class="booking-details">
                                <p><strong>Booking ID:</strong> #${booking.id}</p>
                                <p><strong>Customer:</strong> ${booking.name}</p>
                                <p><strong>Email:</strong> ${booking.email}</p>
                                <p><strong>Phone:</strong> ${booking.phone}</p>
                                <p><strong>Service Type:</strong> ${booking.service_type}</p>
                                <p><strong>Status:</strong> <span class="status-badge ${statusClass}">${statusText}</span></p>
                                <p><strong>Date Requested:</strong> ${new Date(booking.created_at).toLocaleDateString()}</p>
                                ${preferredDateText}
                                <h4>Issue Description</h4>
                                <p>${booking.issue_description}</p>
                                ${technicianNotes}
                            </div>
                        `;
                        
                        viewModal.style.display = 'block';
                    }
                });
            });
            
            // Edit Booking Modal
            const editButtons = document.querySelectorAll('.edit-btn');
            const editModal = document.getElementById('editModal');
            const bookingIdField = document.getElementById('booking_id');
            const statusField = document.getElementById('status');
            const technicianNotesField = document.getElementById('technician_notes');
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-id');
                    const booking = <?php echo json_encode($bookings); ?>.find(b => b.id == bookingId);
                    
                    if (booking) {
                        bookingIdField.value = booking.id;
                        statusField.value = booking.status;
                        technicianNotesField.value = booking.technician_notes || '';
                        
                        editModal.style.display = 'block';
                    }
                });
            });
            
            // Close Modals
            const closeButtons = document.querySelectorAll('.close-btn');
            
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });
            
            // Close modal when clicking outside the modal content
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 