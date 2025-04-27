<?php
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: shop.php');
    exit();
}

// Check if cart exists
if (!isset($_COOKIE['cart']) || empty(json_decode($_COOKIE['cart'], true))) {
    header('Location: shop.php');
    exit();
}

// Collect form data
$firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
$lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
$postalCode = filter_input(INPUT_POST, 'postalCode', FILTER_SANITIZE_STRING);
$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
$paymentMethod = filter_input(INPUT_POST, 'paymentMethod', FILTER_SANITIZE_STRING);
$notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

// Validate required fields
if (!$firstName || !$lastName || !$email || !$phone || !$address || !$city || !$postalCode || !$country) {
    $_SESSION['error'] = 'Please fill in all required fields';
    header('Location: checkout.php');
    exit();
}

// Process cart items
$cartItems = json_decode($_COOKIE['cart'], true);
$subtotal = 0;
$shipping = 5.99; // Fixed shipping cost

// Calculate subtotal
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$total = $subtotal + $shipping;

// Generate order ID
$orderId = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));

// Get current date/time
$orderDate = date('Y-m-d H:i:s');

// Create order data structure
$order = [
    'orderId' => $orderId,
    'orderDate' => $orderDate,
    'customerInfo' => [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'phone' => $phone
    ],
    'shippingInfo' => [
        'address' => $address,
        'city' => $city,
        'postalCode' => $postalCode,
        'country' => $country
    ],
    'paymentMethod' => $paymentMethod,
    'notes' => $notes,
    'items' => $cartItems,
    'subtotal' => $subtotal,
    'shipping' => $shipping,
    'orderTotal' => $total
];

// In a real application, you would save this order to a database
// For demonstration purposes, we'll just store it in the session
$_SESSION['order'] = $order;

// Clear the cart after successful order
setcookie('cart', '', time() - 3600, '/');

// Simulate payment processing
// In a real application, this would involve communicating with a payment gateway

// Redirect to confirmation page
header('Location: confirmation.php');
exit();
?> 