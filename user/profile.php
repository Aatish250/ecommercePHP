
<?php
    session_start();
    require '../config/verify_session.php';
    verify_user("user", "../");
    require '../config/db.php';
    $user_id = $_SESSION['user_id'];

    $userResult = mysqli_query($conn, "Select * from user_details where user_id = $user_id");
    $user = mysqli_fetch_assoc($userResult);

    $orderDetail = "SELECT * FROM orders where user_id = $user_id";
    $orderDetailResult = mysqli_query($conn, $orderDetail);
    
    // if($orderDetailResult){
        
    //     if(mysqli_num_rows($orderDetailResult) > 0){
    //         $order_count = 0;
    //         while($row = mysqli_fetch_assoc($orderDetailResult)){
    //             $orders[] = $row;
    //         }
    //     } 
    //     else {
    //         echo "No correct redirection";
    //         exit();
    //     }

    // }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php' ?>
    
    <title>Profile</title>
</head>

<body class="bg-slate-200">

    <?php
    $active_page = 2;
    include '../components/user_nav.php';
    include '../components/flashMessage.php';
    if (isset($_SESSION['message-status']) && isset($_SESSION['message'])) {
        echo "<script>setFlashMessage('" . $_SESSION['message-status'] . "', '" . $_SESSION['message'] . "');</script>";
        unset($_SESSION['message-status']);
        unset($_SESSION['message']);
    }
    ?>

    <section>
        <div class="max-w-7xl mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">My Account</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Account Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-8">
                            <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-600 text-2xl">
                                <?php echo ucfirst(substr($user['username'], 0, 2)); ?>
                            </div>
                            <div class="ml-6 flex flex-col justify-center w-full">
                                <h2 class="text-2xl font-bold text-gray-900">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </h2>
                                <span class="mt-2 px-3 py-1 text-sm font-semibold rounded-full self-start
                                    <?php 
                                        $status = strtolower($user['user_statu'] ?? $user['status'] ?? 'active');
                                        echo $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <!-- Email -->
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6" />
                                </svg>
                                <div class="flex flex-col sm:flex-row sm:items-center w-full">
                                    <label class="block text-xs font-medium text-gray-600 min-w-[90px] mb-1 sm:mb-0">Email</label>
                                    <p class="text-gray-800 sm:ml-3 break-all"><?php echo $user['email']; ?></p>
                                </div>
                            </div>
                            <!-- Phone -->
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2A19.72 19.72 0 013 5.18 2 2 0 015 3h3a2 2 0 012 1.72c.13.81.36 1.6.68 2.34a2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006.29 6.29l1.27-1.27a2 2 0 012.11-.45c.74.32 1.53.55 2.34.68A2 2 0 0122 16.92z"/>
                                </svg>
                                <div class="flex flex-col sm:flex-row sm:items-center w-full">
                                    <label class="block text-xs font-medium text-gray-600 min-w-[90px] mb-1 sm:mb-0">Phone</label>
                                    <p class="text-gray-800 sm:ml-3"><?php echo $user['phone']; ?></p>
                                </div>
                            </div>
                            <!-- Member Since -->
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="flex flex-col sm:flex-row sm:items-center w-full">
                                    <label class="block text-xs font-medium text-gray-600 min-w-[90px] mb-1 sm:mb-0">Member Since</label>
                                    <p class="text-gray-800 sm:ml-3"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                                </div>
                            </div>
                            <!-- Gender -->
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="8" r="4"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 20c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                <div class="flex flex-col sm:flex-row sm:items-center w-full">
                                    <label class="block text-xs font-medium text-gray-600 min-w-[90px] mb-1 sm:mb-0">Gender</label>
                                    <p class="text-gray-800 sm:ml-3"><?php echo ucfirst($user['gender']); ?></p>
                                </div>
                            </div>
                            <!-- Date of Birth -->
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                                <div class="flex flex-col sm:flex-row sm:items-center w-full">
                                    <label class="block text-xs font-medium text-gray-600 min-w-[90px] mb-1 sm:mb-0">Date of Birth</label>
                                    <p class="text-gray-800 sm:ml-3"><?php echo !empty($user['dob']) ? date('Y-m-d', strtotime($user['dob'])) : ''; ?></p>
                                </div>
                            </div>
                            <!-- Shipping Address (vertical, at last) -->
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-indigo-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10A6 6 0 0112 5a6 6 0 016 6c0 4.314-6 10-6 10z"/>
                                    <circle cx="12" cy="11" r="2.5" />
                                </svg>
                                <div class="flex flex-col w-full">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Shipping Address</label>
                                    <p class="text-gray-800 whitespace-pre-line break-words"><?php echo $user['shipping_address']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-center">
                            <a href="edit-profile.php"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 flex items-center justify-center space-x-2 w-full max-w-xs mx-auto">
                                <svg class="w-7 h-7 mr-1 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2H7a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2z" />
                                </svg>
                                <span class="w-full text-center">Edit Profile</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Order History</h2>
                        <?php if (empty($orders)): ?>
                        <p class="text-gray-500">No orders found.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900">#<?php echo $order['order_id']; ?></td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    $<?php echo number_format($order['total'], 2); ?>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
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
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <a href="order-tracking.php?order_id=<?php echo $order['order_id']; ?>" 
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                        Track Order
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>