<?php
// Include your database connection
require_once __DIR__ . '/../config/db.php'; // Assuming db.php is in Ecom/config/

// Start session for messages or user info
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$khalti_secret_key = 'c0086a41a71c499b91ffa67336cd9045'; // <--- REPLACE WITH YOUR ACTUAL LIVE SECRET KEY (LIVE KEY for production, sandbox key for testing)

// Get necessary parameters from Khalti's callback
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$pidx = isset($_GET['pidx']) ? $_GET['pidx'] : null;
$khalti_callback_status = isset($_GET['status']) ? $_GET['status'] : null;

// Sanitize order_id early for safer use
$safe_order_id = $order_id ? mysqli_real_escape_string($conn, $order_id) : null;

// Function to reverse stock (add quantity back) - Only called if stock was previously deducted
function addStockBack($conn, $orderId) {
    $safe_order_id_for_stock = mysqli_real_escape_string($conn, $orderId);
    $get_order_items_sql = "SELECT product_id, quantity FROM order_items WHERE order_id = '$safe_order_id_for_stock'";
    $order_items_result = mysqli_query($conn, $get_order_items_sql);

    if ($order_items_result && mysqli_num_rows($order_items_result) > 0) {
        while ($item = mysqli_fetch_assoc($order_items_result)) {
            $product_id = mysqli_real_escape_string($conn, $item['product_id']);
            $quantity = mysqli_real_escape_string($conn, $item['quantity']);
            
            $update_stock_sql = "UPDATE `products` SET `stock` = `stock` + $quantity WHERE `product_id` = '$product_id'";
            $update_success = mysqli_query($conn, $update_stock_sql);

            if (!$update_success) {
                error_log("Stock refund failed for product_id: $product_id, order_id: $orderId. Error: " . mysqli_error($conn));
            }
        }
    } else {
        error_log("No order items found for stock refund for order_id: " . $safe_order_id_for_stock . " or query failed. Error: " . mysqli_error($conn));
    }
}


// --- SCENARIO 1: Handle User Cancellation or Direct Failure from Khalti Callback ---
// This block handles cases where the user cancels payment on Khalti, or Khalti redirects with a 'Cancelled'/'Failed' status,
// or if pidx is missing (implying an incomplete payment attempt).
if (($khalti_callback_status === 'Cancelled' || $khalti_callback_status === 'Failed') || !$pidx) {
    if ($safe_order_id) {
        // DO NOT call addStockBack here, as stock is only deducted on SUCCESSFUL Khalti verification.
        // If stock was to be deducted earlier (e.g., on order creation), then addStockBack would be needed here.

        $admin_note_reason = "Payment initiated but cancelled or failed on Khalti interface.";
        if ($khalti_callback_status) {
            $admin_note_reason .= " Khalti Callback Status: " . $khalti_callback_status . ".";
        } else {
            $admin_note_reason .= " No pidx received, implying incomplete payment attempt.";
        }

        $update_order_sql = "UPDATE `orders` SET 
                             `status` = 'cancelled',               
                             `payment_status` = 'failed',          
                             `admin_notes` = CONCAT(COALESCE(`admin_notes`, ''), ' | " . mysqli_real_escape_string($conn, $admin_note_reason) . "')
                             WHERE `order_id` = '$safe_order_id'";
        mysqli_query($conn, $update_order_sql);

        $_SESSION['message-status'] = 'error';
        $_SESSION['message'] = "Your payment was cancelled or failed. Please try again or choose COD.";
        
        header("Location: order-tracking.php?order_id=$order_id");
        exit();
    } else {
        $_SESSION['message-status'] = 'error';
        $_SESSION['message'] = "Payment cancelled, but order details could not be retrieved.";
        header("Location: homepage.php");
        exit();
    }
}

// --- SCENARIO 2: Proceed with Full Payment Verification (if pidx is available and not an early cancellation) ---
// This block performs the server-to-server verification with Khalti API.
if ($pidx && $safe_order_id) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/lookup/', // Khalti Lookup API URL (for sandbox)
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
        CURLOPT_HTTPHEADER => array(
            'Authorization: key ' . $khalti_secret_key,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $verification_data = json_decode($response, true);

    // Check if the API call was successful and payment status is 'Completed'
    if ($http_status === 200 && isset($verification_data['status']) && $verification_data['status'] === 'Completed') {
        // Payment is successfully verified!
        // IMPORTANT FIX: Use 'total_amount' from Khalti Lookup API response
        $khalti_amount = $verification_data['total_amount']; 
        $khalti_transaction_id = $verification_data['transaction_id'];

        // --- IMPORTANT: Verify the amount from your local database with Khalti's amount ---
        $get_order_total_sql = "SELECT total FROM orders WHERE order_id = '$safe_order_id'";
        $result_order_total = mysqli_query($conn, $get_order_total_sql);

        if (!$result_order_total || mysqli_num_rows($result_order_total) === 0) {
            $_SESSION['message-status'] = 'error';
            $_SESSION['message'] = "Order not found in database for verification after Khalti completion. Please contact support.";
            error_log("Khalti Callback: Order ID $safe_order_id not found in DB after Khalti success.");
            header("Location: order-tracking.php");
            exit();
        }

        $local_order_total_row = mysqli_fetch_assoc($result_order_total);
        $local_order_total_in_paisa = floatval($local_order_total_row['total']) * 100;

        if ($khalti_amount == $local_order_total_in_paisa) {
            // Amount matches, proceed to update order status AND deduct stock
            $safe_pidx = mysqli_real_escape_string($conn, $pidx);
            $safe_transaction_id = mysqli_real_escape_string($conn, $khalti_transaction_id);

            // --- START Stock Deduction Logic (Happens ONLY on SUCCESS) ---
            $get_order_items_sql = "SELECT product_id, quantity FROM order_items WHERE order_id = '$safe_order_id'";
            $order_items_result = mysqli_query($conn, $get_order_items_sql);

            if ($order_items_result && mysqli_num_rows($order_items_result) > 0) {
                while ($item = mysqli_fetch_assoc($order_items_result)) {
                    $product_id = mysqli_real_escape_string($conn, $item['product_id']);
                    $quantity = mysqli_real_escape_string($conn, $item['quantity']);
                    
                    $update_stock_sql = "UPDATE `products` SET `stock` = `stock` - $quantity WHERE `product_id` = '$product_id'";
                    $update_success = mysqli_query($conn, $update_stock_sql);

                    if (!$update_success) {
                        error_log("Stock deduction failed for product_id: $product_id, order_id: $order_id. Error: " . mysqli_error($conn));
                        // Consider what to do here: Rollback entire transaction, or mark order as 'stock issue'?
                        // For now, it will proceed but log the error.
                    }
                }
            } else {
                error_log("No order items found for order ID: " . $safe_order_id . ". Stock not deducted.");
            }
            // --- END Stock Reduction Logic ---

            $update_order_sql = "UPDATE `orders` SET 
                                 `payment_status` = 'paid', 
                                 `payment_method` = 'khalti', 
                                 `khalti_pidx` = '$safe_pidx',               
                                 `khalti_transaction_id` = '$safe_transaction_id',
                                 `status` = 'pending' 
                                 WHERE `order_id` = '$safe_order_id'";
            $update_result = mysqli_query($conn, $update_order_sql);

            if ($update_result) {
                $_SESSION['message-status'] = 'success';
                $_SESSION['message'] = "Payment successful and order updated! Your order is now processing.";
                header("Location: order-confirmation.php?order_id=$order_id");
                exit();
            } else {
                $_SESSION['message-status'] = 'error';
                $_SESSION['message'] = "Payment verified but failed to update order in database. Please contact support.";
                error_log("Khalti Callback: Failed to update order status for Order ID $order_id after successful payment and stock reduction. Error: " . mysqli_error($conn));
                header("Location: order-tracking.php?order_id=$order_id"); 
                exit();
            }
        } else {
            // Amount mismatch - CRITICAL ERROR
            $_SESSION['message-status'] = 'error';
            $_SESSION['message'] = "Payment amount mismatch! Order not confirmed. Please contact support.";
            error_log("Khalti Callback: Amount mismatch for Order ID $order_id. Khalti: $khalti_amount, Local: $local_order_total_in_paisa");
            
            // DO NOT call addStockBack here, as stock was not deducted initially.

            $update_order_sql_mismatch = "UPDATE `orders` SET 
                                          `payment_status` = 'failed', 
                                          `status` = 'cancelled', 
                                          `admin_notes` = CONCAT(COALESCE(`admin_notes`, ''), ' | Khalti Amount Mismatch: Expected " . $local_order_total_in_paisa . ", Received " . $khalti_amount . " (PIDX: $pidx)') 
                                          WHERE `order_id` = '$safe_order_id'";
            mysqli_query($conn, $update_order_sql_mismatch);
            header("Location: order-tracking.php?order_id=$order_id"); 
            exit();
        }

    } else {
        // Payment not 'Completed' according to Khalti's API or API call failed for other reasons
        $error_detail = 'Unknown error or payment not completed.';
        if (isset($verification_data['status'])) {
            $error_detail = "Status: " . $verification_data['status'];
            if (isset($verification_data['message'])) {
                $error_detail .= " - " . $verification_data['message'];
            }
        } else if ($http_status !== 200) {
            $error_detail = "API call failed with HTTP status: " . $http_status . ". Response: " . $response;
        }

        $_SESSION['message-status'] = 'error';
        $_SESSION['message'] = "Payment verification failed: " . htmlspecialchars($error_detail) . ". Please try again or choose COD.";
        error_log("Khalti Callback: Verification failed for Order ID $safe_order_id. Details: $error_detail");
        
        if ($safe_order_id) {
            // DO NOT call addStockBack here, as stock was not deducted initially.

            $update_order_sql_fail = "UPDATE `orders` SET 
                                      `payment_status` = 'failed',
                                      `status` = 'cancelled',
                                      `payment_method` = 'khalti',
                                      `admin_notes` = CONCAT(COALESCE(`admin_notes`, ''), ' | Khalti Payment API Verify Failed: " . mysqli_real_escape_string($conn, $error_detail) . " (PIDX: $pidx)')
                                      WHERE `order_id` = '$safe_order_id'";
            mysqli_query($conn, $update_order_sql_fail);
        }
        header("Location: order-tracking.php?order_id=$order_id"); 
        exit();
    }

} else {
    $_SESSION['message-status'] = 'error';
    $_SESSION['message'] = "Invalid payment callback. Missing transaction details.";
    error_log("Khalti Callback: Invalid callback - Missing pidx or order_id.");
    header("Location: homepage.php");
    exit();
}
?>