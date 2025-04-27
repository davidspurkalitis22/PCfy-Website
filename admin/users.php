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

// Process user deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Don't allow admins to delete themselves
    if ($user_id == $_SESSION['userid']) {
        $error_message = "You cannot delete your own account!";
    } else {
        // Delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User deleted successfully.";
        } else {
            $error_message = "Error deleting user: " . $conn->error;
        }
        $stmt->close();
    }
}

// Process form submissions for adding or editing users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        // Get form data
        $firstname = $conn->real_escape_string($_POST['firstname']);
        $lastname = $conn->real_escape_string($_POST['lastname']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $address = $conn->real_escape_string($_POST['address']);
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;
        
        // Add new user
        if ($_POST['action'] == 'add') {
            // Generate a random password
            $random_password = substr(md5(rand()), 0, 8);
            $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = "Email address already exists!";
            } else {
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, phone, address, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $hashed_password, $phone, $address, $is_admin);
                
                if ($stmt->execute()) {
                    $success_message = "User added successfully. Temporary password: " . $random_password;
                } else {
                    $error_message = "Error adding user: " . $conn->error;
                }
            }
            $stmt->close();
        }
        
        // Update existing user
        else if ($_POST['action'] == 'edit' && isset($_POST['user_id'])) {
            $user_id = (int)$_POST['user_id'];
            
            // Check if changing email and if it already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = "Email address already exists!";
            } else {
                // Update user
                $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, phone = ?, address = ?, is_admin = ? WHERE id = ?");
                $stmt->bind_param("sssssii", $firstname, $lastname, $email, $phone, $address, $is_admin, $user_id);
                
                if ($stmt->execute()) {
                    $success_message = "User updated successfully.";
                } else {
                    $error_message = "Error updating user: " . $conn->error;
                }
            }
            $stmt->close();
        }
        
        // Reset user password
        else if ($_POST['action'] == 'reset_password' && isset($_POST['user_id'])) {
            $user_id = (int)$_POST['user_id'];
            
            // Generate a new random password
            $random_password = substr(md5(rand()), 0, 8);
            $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
            
            // Update password
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                $success_message = "Password reset successfully. New temporary password: " . $random_password;
            } else {
                $error_message = "Error resetting password: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Get all users
$users = array();
$sql = "SELECT id, firstname, lastname, email, phone, address, is_admin, created_at FROM users ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Page title
$pageTitle = "User Management";
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
        
        .checkbox-container {
            display: flex;
            align-items: center;
        }
        
        .checkbox-container input[type="checkbox"] {
            margin-right: 10px;
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
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
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
                    <button onclick="openAddModal()" class="btn btn-primary"><i class="fas fa-plus"></i> Add New User</button>
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
            
            <!-- Users Table -->
            <div class="card">
                <h2>All Users</h2>
                <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Admin</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['phone']; ?></td>
                                <td>
                                    <?php if ($user['is_admin'] == 1): ?>
                                    <span class="badge badge-primary">Yes</span>
                                    <?php else: ?>
                                    <span class="badge">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <button 
                                        onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo $user['firstname']; ?>', '<?php echo $user['lastname']; ?>', '<?php echo $user['email']; ?>', '<?php echo $user['phone']; ?>', '<?php echo addslashes($user['address']); ?>', <?php echo $user['is_admin']; ?>)" 
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to reset this user\'s password?');">
                                        <input type="hidden" name="action" value="reset_password">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fas fa-key"></i> Reset Password
                                        </button>
                                    </form>
                                    <?php if ($user['id'] != $_SESSION['userid']): ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No users found</p>
                <?php endif; ?>
            </div>
            
            <!-- Add User Modal -->
            <div id="addUserModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeAddModal()">&times;</span>
                    <h2>Add New User</h2>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" name="firstname" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" name="lastname" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group checkbox-container">
                            <input type="checkbox" id="is_admin" name="is_admin" value="1">
                            <label for="is_admin">Is Admin</label>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Add User</button>
                            <button type="button" class="btn" onclick="closeAddModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Edit User Modal -->
            <div id="editUserModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal()">&times;</span>
                    <h2>Edit User</h2>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" id="edit_user_id" name="user_id" value="">
                        
                        <div class="form-group">
                            <label for="edit_firstname">First Name</label>
                            <input type="text" id="edit_firstname" name="firstname" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_lastname">Last Name</label>
                            <input type="text" id="edit_lastname" name="lastname" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_phone">Phone</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_address">Address</label>
                            <textarea id="edit_address" name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group checkbox-container">
                            <input type="checkbox" id="edit_is_admin" name="is_admin" value="1">
                            <label for="edit_is_admin">Is Admin</label>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <button type="button" class="btn" onclick="closeEditModal()">Cancel</button>
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
        // Add User Modal
        var addUserModal = document.getElementById("addUserModal");
        
        function openAddModal() {
            addUserModal.style.display = "block";
        }
        
        function closeAddModal() {
            addUserModal.style.display = "none";
        }
        
        // Edit User Modal
        var editUserModal = document.getElementById("editUserModal");
        
        function openEditModal(id, firstname, lastname, email, phone, address, isAdmin) {
            document.getElementById("edit_user_id").value = id;
            document.getElementById("edit_firstname").value = firstname;
            document.getElementById("edit_lastname").value = lastname;
            document.getElementById("edit_email").value = email;
            document.getElementById("edit_phone").value = phone;
            document.getElementById("edit_address").value = address;
            document.getElementById("edit_is_admin").checked = isAdmin == 1;
            
            editUserModal.style.display = "block";
        }
        
        function closeEditModal() {
            editUserModal.style.display = "none";
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == addUserModal) {
                addUserModal.style.display = "none";
            }
            if (event.target == editUserModal) {
                editUserModal.style.display = "none";
            }
        }
    </script>
</body>
</html> 