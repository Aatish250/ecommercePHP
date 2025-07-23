<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php'; ?>
    <title>Checkout</title>
</head>
<body class="bg-slate-200">

    <?php

    session_start();
    $active_page = 1;
    include '../components/user_nav.php';
    include '../components/flashMessage.php';
    require '../config/db.php';
    // flash message script
    if (isset($_SESSION['message-status']) && isset($_SESSION['message'])) {
        ?>
        <script>
            setFlashMessage("<?php echo $_SESSION['message-status'] ?>", "<?php echo $_SESSION['message'] ?>");
        </script>
        <?php
     }


    // session data
    $user_id = "1"; // to be changed later
    $user_username = "Aatish"; // to be changed later
    $user_email = "machamasi321@gmail.com"; // to be changed later
    $user_number = "9800000000"; // to be changed later
    $user_shipping_address = "Bhaktapur, Suryabinayak"; // to be changed later

    // if selected items are sent
    if(isset($_GET['selected_cart_ids'])){
        $selected_cart_id = $_GET['selected_cart_ids'];
        $whereClause = "AND cart_id IN ($selected_cart_id)";
        echo "Selected Cart ID: ".$selected_cart_id;
    }else{
        $whereClause = "";
    }

    // scan from cart_detail view
    $cartDetailQuery = "SELECT * FROM cart_details WHERE user_id = $user_id $whereClause";
    $cartDetailResult = mysqli_query($conn, $cartDetailQuery);

    
    echo "<br> $cartDetailQuery <br>";
    if(mysqli_num_rows($cartDetailResult) == 0){
        echo " NO DATA FOUND ";
        exit();
    }
    
    // fetch data form cart_details and store it in _cart_details
    while ($row = mysqli_fetch_assoc($cartDetailResult)) $cart_details[] = $row;
    
    
    // tally upp the sum of cart_total to sub total
    function getTotals($cart_details){
        $subtotal = 0;
        foreach($cart_details as $cart) {
            $subtotal += $cart['cart_total'];
        }
        // pre defined variables to be showed in the page and sdded to database
        $shipping_fee = $subtotal > 100 ? 0 : 10;
        $total = $subtotal + $shipping_fee;

        $object = [
            'subtotal' => $subtotal,
            'shipping_fee' => $shipping_fee,
            'total' => $total
        ];
        return $object;
    }
    $totals = getTotals($cart_details);
    
    
    // now when place order is clicked
    if ($_SERVER['REQUEST_METHOD'] == "POST"){

        // echo "<br>Sub Total: ".$totals['subtotal'];
        // echo "<br>Shipping Fee: ".$totals['shipping_fee'];
        // echo "<br>Total: ".$totals['total'];
        // echo "<br>Where Clause : ".$whereClause;

        // Insert data to orders table
        $insertOrderSQL = "INSERT INTO `orders`(`uid`, `shipping_fee`, `total`, `status`, `shipping_address`, `phone`, `payment_method`, `payment_status`, `notes`) VALUES (
            '$user_id',
            '{$totals['shipping_fee']}',
            '{$totals['total']}',
            'pending',
            '".mysqli_real_escape_string($conn, $_POST['shipping_address'])."',
            '".mysqli_real_escape_string($conn, $_POST['phone'])."',
            '".mysqli_real_escape_string($conn, $_POST['payment_method'])."',
            'pending',
            '".mysqli_real_escape_string($conn, $_POST['notes'])."'
        )";

        $resultOrder = mysqli_query($conn, $insertOrderSQL);

        // Get the last inserted order id
        $order_id = mysqli_insert_id($conn);

        $allOrderItemsInserted = true;
        $allStockUpdated = true;

        foreach ($cart_details as $cart_item) {
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['quantity'];
            $order_price = $cart_item['product_price'];
            $sub_total = $cart_item['cart_total'];

            // Insert into order_items
            $insertOrderItemsSQL = "INSERT INTO `order_items`(`order_id`, `product_id`, `quantity`, `order_price`, `sub_total`) 
                VALUES ( '$order_id', '$product_id', '$quantity', '$order_price', '$sub_total' )";
            $resultOrderItem = mysqli_query($conn, $insertOrderItemsSQL);
            
            if (!$resultOrderItem) $allOrderItemsInserted = false;

            // Update product stock
            $updateProductStockSQL = "UPDATE `products` SET `stock` = `stock` - $quantity WHERE `product_id` = $product_id";
            $resultUpdateStock = mysqli_query($conn, $updateProductStockSQL);
            
            if (!$resultUpdateStock) $allStockUpdated = false;
        }

        // Delete the cart items which are selected
        $deleteCartSelected = "DELETE FROM `cart` WHERE `user_id` = $user_id $whereClause";
        $resultDeleteCart = mysqli_query($conn, $deleteCartSelected);

        // Only execute if all SQLs are successful
        if ($resultOrder && $allOrderItemsInserted && $allStockUpdated && $resultDeleteCart) {
            $_SESSION['message-status'] = 'success';
            $_SESSION['message'] = "Order placed successfully!";
            if ($_POST['payment_method'] === 'khalti') {
                // Redirect to Khalti payment
                echo "Redirect to Khalti payment";
                header("Location: khalti-payment.php?order_id=$order_id");
            } else {
                // Redirect to order confirmation
                echo "Redirect to order confirmation";
                header("Location: order-confirmation.php?order_id=$order_id");
            }
            exit();
        } else {
            $_SESSION['message-status'] = 'error';
            $_SESSION['message'] = "There was an error placing your order. Please try again.";
        }
    }
    
    ?>


    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="mb-2 -mt-4">
            <a href="cart.php" class="text-indigo-600 hover:text-indigo-800">&larr; Back to Cart</a>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-2">Checkout</h1>
            
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Checkout Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Shipping & Payment Details</h2>
                
                <form method="POST" id="checkoutForm">
                    <!-- Personal Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" value="<?php echo htmlspecialchars($user_username); ?>" readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel" name="phone" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               value="<?php echo htmlspecialchars($user_number); ?>"
                               placeholder="Enter your phone number">
                    </div>

                    <!-- Shipping Address -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address *</label>
                        <textarea name="shipping_address" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Enter your complete shipping address"><?php echo htmlspecialchars($user_shipping_address); ?></textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Payment Method</h3>
                        <div class="space-y-4">
                            <!-- Cash on Delivery -->
                            <div class="border rounded-lg p-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="payment_method" value="cod" checked
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="ml-3 flex items-center">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Cash on Delivery</span>
                                        </div>
                                    </div>
                                </label>
                                <p class="ml-7 text-sm text-gray-600 mt-1">Pay when your order is delivered to your doorstep</p>
                            </div>

                            <!-- Khalti Payment -->
                            <div class="border rounded-lg p-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="payment_method" value="khalti"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="ml-3 flex items-center">
                                        <div class="flex items-center">
                                            <div class="w-8 h-6 bg-purple-600 rounded mr-2 flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">K</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">Khalti Digital Wallet</span>
                                        </div>
                                    </div>
                                </label>
                                <p class="ml-7 text-sm text-gray-600 mt-1">Pay securely using your Khalti digital wallet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Notes (Optional)</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Any special instructions for your order"></textarea>
                    </div>

                    <!-- <a type="submit" href="order-confirmation.php"
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                        Place Order
                    </a> -->
                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                        Place Order
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Order Summary</h2>
                
                <!-- Order Items -->
                <div class="space-y-4 mb-6">
                    <?php foreach ($cart_details as $cart): ?>
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo '../img/product/'.$cart['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($cart['product_name']); ?>" 
                                 class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($cart['product_name']); ?></h3>
                                <p class="text-gray-600">Qty: <?php echo $cart['quantity']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">
                                    $<?php echo number_format($cart['product_price'] * $cart['quantity'], 2); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Totals -->
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">subtotal</span>
                        <span class="text-gray-800">$<?php echo number_format($totals['subtotal'], 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-800">
                            <?php echo $totals['shipping_fee'] > 0 ? '$' . number_format($totals['shipping_fee'], 2) : 'Free'; ?>
                        </span>
                    </div>
                    <?php if ($totals['shipping_fee'] == 0 && $totals['subtotal'] > 100): ?>
                        <div class="text-sm text-green-600">
                            🎉 You qualify for free shipping!
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-lg font-semibold border-t pt-2">
                        <span>Total</span>
                        <span class="text-indigo-600">$<?php echo number_format($totals['total'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const phone = document.querySelector('input[name="phone"]').value;
            const address = document.querySelector('textarea[name="shipping_address"]').value;
            
            if (!phone.trim()) {
                alert('Please enter your phone number');
                e.preventDefault();
                return;
            }
            
            if (!address.trim()) {
                alert('Please enter your shipping address');
                e.preventDefault();
                return;
            }
        });
    </script>

</body>
</html>