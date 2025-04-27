<?php
// Start session
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'config.php';

// Process status update if submitted
if (isset($_POST['update_status']) && isset($_POST['build_id']) && isset($_POST['status'])) {
    $build_id = intval($_POST['build_id']);
    $status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
    if (in_array($status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE custom_builds SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $build_id);
        $stmt->execute();
        $stmt->close();
        
        // Set success message
        $status_message = "Status updated successfully.";
    }
}

// Get all custom builds from newest to oldest
$result = $conn->query("SELECT * FROM custom_builds ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Custom PC Build Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e1e1e1;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .build-details {
            margin-top: 10px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
        }
        .build-details h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .build-details p {
            margin: 5px 0;
        }
        .status-form {
            display: flex;
            gap: 10px;
        }
        .status-form select {
            padding: 5px;
        }
        .status-form button {
            padding: 5px 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .status-form button:hover {
            background-color: #2980b9;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .status-in_progress {
            background-color: #b8daff;
            color: #004085;
        }
        .status-completed {
            background-color: #c3e6cb;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f5c6cb;
            color: #721c24;
        }
        .view-details-btn {
            padding: 5px 10px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .view-details-btn:hover {
            background-color: #5a6268;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Custom PC Build Requests</h1>
        
        <?php if (isset($status_message)): ?>
            <div class="message"><?php echo $status_message; ?></div>
        <?php endif; ?>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php 
                                    switch($row['status']) {
                                        case 'pending': echo 'Pending'; break;
                                        case 'in_progress': echo 'In Progress'; break;
                                        case 'completed': echo 'Completed'; break;
                                        case 'cancelled': echo 'Cancelled'; break;
                                        default: echo ucfirst($row['status']);
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <button class="view-details-btn" onclick="toggleDetails(<?php echo $row['id']; ?>)">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr id="details-<?php echo $row['id']; ?>" class="hidden">
                            <td colspan="6">
                                <div class="build-details">
                                    <h4>PC Build Specifications</h4>
                                    <p><strong>CPU:</strong> <?php echo htmlspecialchars($row['cpu']); ?></p>
                                    <p><strong>Motherboard:</strong> <?php echo htmlspecialchars($row['motherboard']); ?></p>
                                    <p><strong>GPU:</strong> <?php echo htmlspecialchars($row['gpu']); ?></p>
                                    <p><strong>RAM:</strong> <?php echo htmlspecialchars($row['ram']); ?></p>
                                    <p><strong>Storage:</strong> <?php echo htmlspecialchars($row['storage']); ?></p>
                                    
                                    <?php if (!empty($row['additional_storage'])): ?>
                                        <p><strong>Additional Storage:</strong> <?php echo htmlspecialchars($row['additional_storage']); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($row['cooling'])): ?>
                                        <p><strong>Cooling:</strong> <?php echo htmlspecialchars($row['cooling']); ?></p>
                                    <?php endif; ?>
                                    
                                    <p><strong>Case:</strong> <?php echo htmlspecialchars($row['pc_case']); ?></p>
                                    <p><strong>Power Supply:</strong> <?php echo htmlspecialchars($row['power_supply']); ?></p>
                                    
                                    <?php if (!empty($row['operating_system'])): ?>
                                        <p><strong>Operating System:</strong> <?php echo htmlspecialchars($row['operating_system']); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($row['additional_notes'])): ?>
                                        <p><strong>Additional Notes:</strong> <?php echo nl2br(htmlspecialchars($row['additional_notes'])); ?></p>
                                    <?php endif; ?>
                                    
                                    <h4>Contact Information</h4>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                                    
                                    <h4>Update Status</h4>
                                    <form method="post" class="status-form">
                                        <input type="hidden" name="build_id" value="<?php echo $row['id']; ?>">
                                        <select name="status">
                                            <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="in_progress" <?php echo $row['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="completed" <?php echo $row['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $row['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status">Update Status</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No custom build requests found.</p>
        <?php endif; ?>
    </div>
    
    <script>
        function toggleDetails(id) {
            const detailsRow = document.getElementById('details-' + id);
            detailsRow.classList.toggle('hidden');
        }
    </script>
</body>
</html> 