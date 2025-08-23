<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php'; ?>
    <title>Order Tracking</title>
</head>

<body class="bg-slate-200">

    <?php

    session_start();
    $active_page = 1;
    include '../components/user_nav.php';
    include '../components/flashMessage.php';
    require '../config/db.php';

    $user_id = $_SESSION['user_id'];
    $order_id = $_GET['order_id']; // to be changed later


    
    
    $orderDetail = "SELECT * FROM orders where order_id=$order_id and user_id = $user_id";
    $orderDetailResult = mysqli_query($conn, $orderDetail);
    
    if($orderDetailResult){
        
        if(mysqli_num_rows($orderDetailResult) > 0){
            
            $order = mysqli_fetch_assoc($orderDetailResult);
            
            $orderItemsDetail = "SELECT * from order_details WHERE order_id = $order_id and user_id = $user_id";
            $orderItemsDetailResult = mysqli_query($conn, $orderItemsDetail);
            
            $sum_sub_total = 0;
            while ($itemRow = mysqli_fetch_assoc($orderItemsDetailResult)){
                $order_items[] = $itemRow;
                $sum_sub_total += $itemRow['sub_total'];
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
        <div class="mb-4">
            <a href="profile.php" class="text-indigo-600 hover:text-indigo-800">&larr; Back to Profile</a>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-8">Order Tracking</h1>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Details</h2>
                    <div class="space-y-2">
                        <p><span class="font-medium">Order ID:</span> #<?php echo $order_id;?></p>
                        <p><span class="font-medium">Order Date:</span><?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                        <p><span class="font-medium">Sub-Total:</span> Rs. <?php echo $sum_sub_total;?></p>
                        <p><span class="font-medium">Shipping Fee:</span> <?php echo $order['shipping_fee'] > 0 ? '$' . number_format($order['shipping_fee'], 2) : 'Free'; ?></p>
                        <p><span class="font-medium">Total:</span> Rs. <?php echo $order['total']; ?></p>
                        <p><span class="font-medium">Status:</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                <?php
                                    switch ($order['status']) {
                                        case 'pending':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'processing':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'shipped':
                                            echo 'bg-purple-100 text-purple-800';
                                            break;
                                        case 'delivered':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'cancelled':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Shipping Address</h2>
                    <div class="text-gray-600">
                        <?php echo $order['shipping_address'];?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Progress -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Order Progress</h2>
            <div class="flex items-center justify-between">
                <?php
                $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                $current_status_index = array_search($order['status'], $statuses);

                foreach ($statuses as $index => $status):
                    $is_completed = $index <= $current_status_index;
                    $is_current = $index == $current_status_index;
                    ?>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center 
                            <?php echo $is_completed ? 'bg-indigo-600 text-white' : 'bg-gray-300 text-gray-600'; ?>">
                            <?php echo $index + 1; ?>
                        </div>
                        <span
                            class="mt-2 text-sm <?php echo $is_current ? 'font-semibold text-indigo-600' : 'text-gray-600'; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </div>
                    <?php if ($index < count($statuses) - 1): ?>
                        <div
                            class="flex-1 h-1 mx-4 <?php echo $index < $current_status_index ? 'bg-indigo-600' : 'bg-gray-300'; ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Items</h2>
            <div class="space-y-4">
                <?php foreach ($order_items as $item): 
                    // echo $item['product_name'];
                    ?>
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <img src="../img/product/<?php echo $item['image']; ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                             class="w-16 h-16 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p class="text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                            <p class="text-gray-600">Price: Rs. <?php echo number_format($item['order_price'], 2); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">
                                Rs.<?php echo number_format($item['sub_total'], 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</body>

</html>