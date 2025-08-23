<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-200">

    <?php

    session_start();
    $active_page = 1;
    include '../components/user_nav.php';
    include '../components/flashMessage.php';
    require '../config/db.php';

    $user_id = $_SESSION['user_id'];
    $order_id = $_GET['order_id'];


    $orderDetail = "SELECT * FROM orders where order_id=$order_id and user_id = $user_id";
    $orderDetailResult = mysqli_query($conn, $orderDetail);
    
    if($orderDetailResult){
        
        if(mysqli_num_rows($orderDetailResult) > 0){
            
            $order = mysqli_fetch_assoc($orderDetailResult);
            
            $orderItemsDetail = "SELECT * from order_details WHERE order_id = $order_id and user_id = $user_id";
            $orderItemsDetailResult = mysqli_query($conn, $orderItemsDetail);
            while ($itemRow = mysqli_fetch_assoc($orderItemsDetailResult)){
                $order_items[] = $itemRow;
            }
        } 
        else {
            echo "No correct redirection";
            exit();
        }

    }else{
        echo "Failed reading sql";
        exit();
    }
    ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600">Thank you for your purchase. Your order has been successfully placed.</p>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Information</h2>
                    <div class="space-y-2">
                        <p><span class="font-medium">Order ID:</span> #<?php echo $order['order_id']; ?></p>
                        <p><span class="font-medium">Order Date:</span> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                        <p><span class="font-medium">Total Amount:</span> Rs. <?php echo number_format($order['total'], 2); ?></p>
                        <p><span class="font-medium">Payment Method:</span> 
                            <?php if ($order['payment_method'] === 'khalti'): ?>
                                <span class="inline-flex items-center">
                                    <div class="w-4 h-3 bg-purple-600 rounded mr-1 flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">K</span>
                                    </div>
                                    Khalti
                                </span>
                            <?php else: ?>
                                Cash on Delivery
                            <?php endif; ?>
                        </p>
                        <p><span class="font-medium">Status:</span> 
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Shipping Details</h2>
                    <div class="space-y-2">
                        <?php
                            // Fetch username from users table using user_id
                            $user_id = $order['user_id'];
                            $username = '';
                            $user_query = mysqli_query($conn, "SELECT username FROM users WHERE user_id = " . intval($user_id));
                            if ($user_query && mysqli_num_rows($user_query) > 0) {
                                $user_row = mysqli_fetch_assoc($user_query);
                                $username = $user_row['username'];
                            }
                        ?>
                        <p><span class="font-medium">Customer:</span> <?php echo htmlspecialchars($username); ?></p>
                        <p><span class="font-medium">Phone:</span> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p><span class="font-medium">Address:</span></p>
                        <div class="text-gray-600 ml-4">
                            <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                        </div>
                        <?php if ($order['notes']): ?>
                            <p><span class="font-medium">Notes:</span> <?php echo htmlspecialchars($order['notes']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Items</h2>
            <div class="space-y-4">
                <?php foreach ($order_items as $item): ?>
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <img src="../img/product/<?php echo $item['image']; ?>" 
                             alt="<?php echo htmlspecialchars($item['image']); ?>" 
                             class="w-16 h-16 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p class="text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                            <p class="text-gray-600">Price: Rs. <?php echo number_format($item['order_price'], 2); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">
                                Rs. <?php echo number_format($item['order_price'] * $item['quantity'], 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-blue-800 mb-3">What happens next?</h2>
            <div class="space-y-2 text-blue-700">
                <?php if ($order['payment_method'] === 'khalti'): ?>
                    <p>KHALTI:</p>
                    <p>✓ Your payment has been processed successfully</p>
                    <p>• We will process your order within 1-2 business days</p>
                <?php else: ?>
                    <p>COD:</p>
                    <p>• We will contact you to confirm your order</p>
                    <p>• Prepare the exact amount for cash on delivery</p>
                <?php endif; ?>
                <!-- <p>GENERAL:</p> -->
                <p>• You will receive updates via email and SMS</p>
                <p>• Track your order status in your account</p>
                <p>• Estimated delivery: 3-5 business days</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center space-x-4">
            <a href="order-tracking.php?order_id=<?php echo $order_id; ?>" 
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                Track Your Order
            </a>
            <a href="homepage.php" 
               class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-semibold">
                Continue Shopping
            </a>
        </div>

        <!-- Contact Information -->
        <div class="mt-8 text-center text-gray-600">
            <p>Need help? Contact us at <a href="mailto:machamasi321@gmail.com" class="text-indigo-600 hover:text-indigo-800">machamasi321@gmail.com</a> or call +977-984-169-3432</p>
        </div>
    </div>
    <?php
    // if (isset($_SESSION['message-status']) && isset($_SESSION['message'])) {
    //     ?>
         <script>
    //         setFlashMessage("<?php //echo $_SESSION['message-status'] ?>", "<?php //echo $_SESSION['message'] ?>");
    //     </script>
         <?php
    // }
    // unset($_SESSION['message-ststus']);
    // unset($_SESSION['message']);
    ?>
</body>
</html>
