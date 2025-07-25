<?php

// This line includes Composer's autoloader.
// It's crucial because it makes the Khalti SDK classes available to your script.
// The path is relative: '..' goes up one directory from 'user' to 'Ecom',
// and then 'vendor/autoload.php' is found there.
require_once __DIR__ . '/../vendor/autoload.php';

// Include your database connection file
// Adjust path if your db.php is elsewhere relative to khalti-payment.php
require_once __DIR__ . '/../config/db.php';


use Xentixar\KhaltiSdk\Khalti;

// Checks if order_id is present in the URL
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])){
    // Handle error: No valid order ID provided
    echo "Error: No valid order ID for payment.";
    // You might want to redirect the user back to an error page or checkout ???
    exit();
}

$order_id = intval($_GET['order_id']); // Get and sanitize the order ID form URL

// --- Fetch Order Detials form Database ---
$getOrderSQL = "SELECT order_id, o.user_id, o.total, o.shipping_address, o.phone, u.username, u.email 
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                WHERE o.order_id = $order_id";
$resultOrder = mysqli_query($conn, $getOrderSQL);

if (!$resultOrder || mysqli_num_rows($resultOrder) === 0){
    // Handel error:  Order not found or database error
    echo "Error: Order not found or database error.";
    // You might want to redirect to  an error page or user's orders list
    exit();
}

$order_details = mysqli_fetch_assoc($resultOrder);

// --- These variables will hold the dynamic data for the payment. ---
$amountInPaisa = $order_details['total']; // The total amount of the order, *in paisa* (e.g., 1000 for NPR 10.00).
$purchaseOrderId = $order_details['order_id']; // A unique ID for this specific order from your system.
$purchaseOrderName = "order #".$order_id; // A short description or name for the order (e.g., "Online Store Purchase").
$customerInfo = [ // Optional: Customer details for Khalti's record.
    "name" => $order_details['username'],
    "email" => $order_details['email'],
    "phone" => $order_details['phone']
];

// Initialize Khalti SDK
$khalti = new Khalti();

// --- IMPORTANT: Replace 'your_live_secret_key_here' with your actual Live Secret Key from test-admin.khalti.com ---
// This key is vital for authenticating your payment requests with Khalti.
$khalti->setSecretKey('c0086a41a71c499b91ffa67336cd9045');

// --- These variables will hold the dynamic data for the payment. ---
$websiteUrl = 'http://localhost/Ecom'; // Your website's base URL (e.g., 'http://localhost/Ecom').
$returnUrl = $websiteUrl . '/user/khalti-callback.php?order_id=' . $order_id; // Khalti will redirect the user to this URL after payment.

// Configure the payment details for Khalti
$khalti->config(
    $returnUrl,
    $websiteUrl,
    $amountInPaisa,
    $purchaseOrderId,
    $purchaseOrderName,
    $customerInfo
);

// Initiate the payment process. This line will typically redirect the user's browser to the Khalti payment page.
// For a live production environment, the first parameter of init() might need to be `true` (e.g., $khalti->init(true);)
// but for sandbox testing, the default is usually fine.
$khalti->init();

?>