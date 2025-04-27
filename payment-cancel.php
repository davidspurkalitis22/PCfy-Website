<?php
// Start session
session_start();

// Set error message
$_SESSION['payment_error'] = "Payment was cancelled. Your order has not been processed.";

// Redirect back to checkout
header('Location: /PCFYwebsite/checkout.php');
exit();
?> 