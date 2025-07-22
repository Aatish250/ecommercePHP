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
    
    $user_id = "1"; // to be changed later
    $user_username = "Aatish"; // to be changed later
    $user_email = "machamasi321@gmail.com"; // to be changed later
    $user_phone = "9800000000"; // to be changed later
    $user_shipping_address = "Kathmandu, Nepal"; // to be changed later
    if(isset($_GET['selected_cart_ids'])){
        $selected_cart_id = $_GET['selected_cart_ids'];
        $whereClause = " AND cart_id IN ($selected_cart_id)";
        echo "Selected Cart ID: ".$selected_cart_id;
    }else{
        $whereClause = "";
    }

    $cartQuery = "SELECT * FROM cart_details WHERE user_id = $user_id $whereClause";
    

    $subtotal = 0;

    $cartResult = mysqli_query($conn, $cartQuery);

    if(!mysqli_num_rows($cartResult)) {
        header ("Location: cart.php");
        exit();
    }

    // to verify if there is any data found
    while($row = mysqli_fetch_assoc($cartResult)){
        $cart_items[] = $row;
    }
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['product_price'] * $item['quantity'];
    }

    $shipping_fee = $subtotal > 1000 ? 0 : 10; // Free shipping over $100
    $total = $subtotal + $shipping_fee;
    
    
    // interact with the database
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $shipping_address = $_POST['shipping_address'];
        $phone = $_POST['phone'];
        $payment_method = $_POST['payment_method'];
        $notes = $_POST['notes'] ?? '';
        
        echo "<br>Placed Order Details:<br><pre>";
        echo $shipping_address;
        echo $phone;
        echo $payment_method;
        echo $notes;

            
            // Create order
            $orderQuery = "INSERT INTO orders (uid, total, shipping_address, phone, payment_method, notes, status) 
                           VALUES ('$user_id', '$total', '$shipping_address', '$phone', '$payment_method', '$notes', 'pending')";
            
            
            $resultOrderQuery = mysqli_query($conn, $orderQuery);
            
            $order_id = mysqli_insert_id($conn);
            
            // Add order items
            foreach ($cart_items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $product_price = $item['product_price'];
                
                $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '$product_id', '$quantity', '$product_price')";
                $resultOrderItemQuery = mysqli_query($conn, $orderItemQuery);
                // Update product stock
                $updateStockQuery = "UPDATE products SET stock = stock - $quantity WHERE product_id = $product_id";
                $resultUpdateStockQuery = mysqli_query($conn, $updateStockQuery);
            }
            
            // Clear cart
            $clearCartQuery = "DELETE FROM cart WHERE user_id = $user_id $whereClause";
            $resultClearCartQuery = mysqli_query($conn, $clearCartQuery);
            
            if($resultOrderQuery &&$resultOrderItemQuery && $resultUpdateStockQuery && $resultClearCartQuery){
                $_SESSION['message-status'] = 'success';
                $_SESSION['message-'] = "Order placed successfully";
                if ($payment_method === 'khalti') {
                    // Redirect to Khalti payment
                    header("Location: khalti-payment.php?order_id=$order_id");
                } else {
                    // Redirect to order confirmation
                    header("Location: order-confirmation.php?order_id=$order_id");
                }
                // exit();
            }else{
                $_SESSION['message-status'] = 'fail';
                $_SESSION['message-'] = "Order placement failed";
            }
            echo "<br>Order ID: ".$order_id."</pre><br>";
            
    }
    ?>

    <?php
    if (isset($_SESSION['message-status']) && isset($_SESSION['message'])) {
        ?>
        <script>
            setFlashMessage("<?php echo $_SESSION['message-status'] ?>", "<?php echo $_SESSION['message'] ?>");
        </script>
        <?php
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
                               value="<?php echo htmlspecialchars($user_phone); ?>"
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
                    <?php foreach ($cart_items as $item): ?>
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo '../img/product/'.$item['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <p class="text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">
                                    $<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Totals -->
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-800">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-800">
                            <?php echo $shipping_fee > 0 ? '$' . number_format($shipping_fee, 2) : 'Free'; ?>
                        </span>
                    </div>
                    <?php if ($shipping_fee == 0 && $subtotal > 100): ?>
                        <div class="text-sm text-green-600">
                            🎉 You qualify for free shipping!
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-lg font-semibold border-t pt-2">
                        <span>Total</span>
                        <span class="text-indigo-600">$<?php echo number_format($total, 2); ?></span>
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