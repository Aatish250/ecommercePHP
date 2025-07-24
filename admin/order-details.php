<?php
    
    session_start();
    require '../config/verify_session.php';
    verify_user("admin", "../");
    require_once '../config/db.php';
    $order_id = $_GET['order_id'];
    
    $order = ($res = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $order_id")) ? mysqli_fetch_assoc($res) : exit();
    
    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_status'])){
        $new_status = $_POST['new_status'];
        $admin_notes = $_POST['admin_notes'] ?? '';

        if($res = mysqli_query($conn, "UPDATE orders SET status = '$new_status', admin_notes = '$admin_notes' WHERE order_id = '$order_id'")){
            $_SESSION['message_status'] = "success";
            $_SESSION['message'] = "Order Updated";
            header ("Location: ".$_SERVER['PHP_SELF']."?order_id=$order_id");
        }
    }

    $customer_id = $order['user_id'];

    $order_details = [];
    
    $res = mysqli_query($conn, "SELECT * FROM order_details WHERE order_id = $order_id");
    while($row = mysqli_fetch_assoc($res))
        $order_details[] = $row;

    $order_specific_SQL = "SELECT count(quantity) as total_quantity, sum(sub_total) as sum_sub_total FROM order_details WHERE order_id = $order_id";
    if($res = mysqli_query($conn,$order_specific_SQL)) 
        $order_spec = mysqli_fetch_assoc($res);
    
    if($res = mysqli_query($conn,"SELECT * FROM user_details WHERE user_id = $customer_id")) 
        $customer = mysqli_fetch_assoc($res);
    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo $order_id; ?> - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="">
    <?php
        $active_page = 3;
        include '../components/admin_nav.php';
        include '../components/flashMessage.php';

        
    ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 no-print">
            <div>
                <a href="orders.php" class="text-indigo-600 hover:text-indigo-800 mb-2 inline-block">&larr; Back to Orders</a>
                <h1 class="text-3xl font-bold text-gray-800">Order Details #<?php echo $order_id; ?></h1>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Order Information</h2>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Order Date:</span>
                                    <p class="font-medium"><?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Last Updated:</span>
                                    <p class="font-medium"><?php echo $order['updated_at'] ? date('F j, Y \a\t g:i A', strtotime($order['updated_at'])) : 'Not updated'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                <?php 
                                switch($order['status']) {
                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                    case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                    case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                    case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                    default: echo 'bg-gray-100 text-gray-800';
                                }
                                ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="border-t pt-4 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Payment Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Payment Method:</span>
                                <p class="font-medium">
                                    <?php if ($order['payment_method'] === 'khalti'): ?>
                                        <span class="inline-flex items-center">
                                            <div class="w-4 h-3 bg-purple-600 rounded mr-1 flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">K</span>
                                            </div>
                                            Khalti Digital Wallet
                                        </span>
                                    <?php else: ?>
                                        Cash on Delivery
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600">Payment Status:</span>
                                <p class="font-medium">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php 
                                        switch($order['payment_status']) {
                                            case 'paid': echo 'bg-green-100 text-green-800'; break;
                                            case 'failed': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-yellow-100 text-yellow-800';
                                        }
                                        ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <?php if ($order['khalti_token']): ?>
                            <div class="mt-2">
                                <span class="text-gray-600">Khalti Token:</span>
                                <p class="font-mono text-sm bg-gray-100 p-2 rounded"><?php echo $order['khalti_token']; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Order Items -->
                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-gray-800 mb-4">Order Items</h3>
                        <div class="space-y-4">
                            <?php foreach ($order_details as $item): ?>
                                <div class="flex items-center space-x-4 p-4 border rounded-lg">
                                    <img src="../img/product/<?php echo $item['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                         class="w-16 h-16 object-cover rounded">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                        <p class="text-sm text-gray-600">Category: <?php echo ucfirst($item['category']); ?></p>
                                        <p class="text-sm text-gray-600 no-print">Current Stock: <?php echo $item['current_stock']; ?></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">Quantity</p>
                                        <p class="font-semibold"><?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">Unit Price</p>
                                        <p class="font-semibold">$<?php echo number_format($item['order_price'], 2); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Total</p>
                                        <p class="font-semibold text-lg">$<?php echo number_format($item['sub_total'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Order Totals -->
                        <div class="border-t mt-6 pt-4">
                            <div class="flex justify-end">
                                <div class="w-64 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span class="font-medium">$<?php echo number_format($order_spec['sum_sub_total'], 2); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Shipping:</span>
                                        <span class="font-medium">
                                            <?php echo $order['shipping_fee'] > 0 ? '$' . $order['shipping_fee'] : 'Free'; ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold border-t pt-2">
                                        <span>Total:</span>
                                        <span class="text-indigo-600">$<?php echo number_format($order['total'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Notes -->
                <?php if ($order['notes']): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Customer Notes</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Admin Notes -->
                <?php if ($order['admin_notes']): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Admin Notes</h3>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($order['admin_notes'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Customer Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Name:</span>
                            <p class="font-medium"><?php echo htmlspecialchars($customer['username']); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600">Email:</span>
                            <p class="font-medium"><?php echo htmlspecialchars($customer['email']); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600">Phone:</span>
                            <p class="font-medium"><?php echo htmlspecialchars($customer['phone']); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600">Customer Stats:</span>
                            <p class="text-sm text-gray-700">
                                • <?php echo $customer['total_orders']; ?> orders
                                <br>
                                • Rs. <?php echo number_format($customer['total_transaction'], 2); ?> total transaction
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <a href="users.php?search=<?php echo urlencode($customer['email']); ?>" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm">
                            View Customer Profile →
                        </a>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Shipping Address</h3>
                    <div class="text-gray-700">
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                    </div>
                </div>

                <!-- Order Status Management -->
                <div class="bg-white rounded-lg shadow-md p-6 no-print">
                    <h3 class="font-semibold text-gray-800 mb-4">Update Order Status</h3>
                    <form method="POST">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                                <select name="new_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                                <textarea name="admin_notes" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Add internal notes about this order..."><?php echo htmlspecialchars($order['admin_notes'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" name="update_status" 
                                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                                Update Order
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Actions ??? --> 
                <div class="bg-white rounded-lg shadow-md p-6 no-print">
                    <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="mailto:<?php echo $customer['email']; ?>?subject=Order #<?php echo $order_id; ?> Update" 
                           class="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            Email Customer
                        </a>

                        <?php
                           $current_status = $order['status'];
                           $avaliable_status = ['pending','processing','shipped'];
                           $nextStatus = [
                               'pending' => ["processing", "bg-green-600 hover:bg-green-700", "Confirm Order"],
                               'processing' => ["shipped", "bg-purple-600 hover:bg-purple-700", "Mark As Shipped"],
                               'shipped' => ["delivered", "bg-green-600 hover:bg-green-700", "Mark As Delivered"],
                           ];

                           if(in_array($current_status, $avaliable_status)):
                               echo $nextStatus[$current_status][0];
                                
                        ?>
                        
                        <?php //if ($order['status'] === 'pending'): ?>
                            <form method="POST" class="inline-block w-full">
                                <input type="hidden" name="new_status" value="<?php echo $nextStatus[$current_status][0]; ?>">
                                <input type="hidden" name="admin_notes" value="Order confirmed and processing started">
                                <button type="submit" name="update_status" 
                                        class="w-full text-white py-2 px-4 rounded-lg transition-colors <?php echo $nextStatus[$current_status][1]; ?>">
                                    <?php echo $nextStatus[$current_status][2] ?>
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php //if ($order['status'] === 'processing'): ?>
                            <!-- <form method="POST" class="inline-block w-full">
                                <input type="hidden" name="new_status" value="shipped">
                                <input type="hidden" name="admin_notes" value="Order has been shipped">
                                <button type="submit" name="update_status" 
                                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                    Mark as Shipped
                                </button>
                            </form> -->
                        <?php //endif; ?>
                        
                        <?php //if ($order['status'] === 'shipped'): ?>
                            <!-- <form method="POST" class="inline-block w-full">
                                <input type="hidden" name="new_status" value="delivered">
                                <input type="hidden" name="admin_notes" value="Order has been delivered successfully">
                                <button type="submit" name="update_status" 
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                    Mark as Delivered
                                </button>
                            </form> -->
                        <?php //endif; ?>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Order Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-600 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium">Order Placed</p>
                                <p class="text-xs text-gray-600"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <?php if ($order['status'] !== 'pending'): ?>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-600 rounded-full mr-3"></div>
                                <div>
                                    <p class="text-sm font-medium">Status: <?php echo ucfirst($order['status']); ?></p>
                                    <p class="text-xs text-gray-600">
                                        <?php echo $order['updated_at'] ? date('M j, Y g:i A', strtotime($order['updated_at'])) : 'Recently updated'; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($order['payment_status'] === 'paid'): ?>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-600 rounded-full mr-3"></div>
                                <div>
                                    <p class="text-sm font-medium">Payment Confirmed</p>
                                    <p class="text-xs text-gray-600">Via <?php echo ucfirst($order['payment_method']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
