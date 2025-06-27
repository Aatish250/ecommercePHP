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
    include '../components/user_nav.php';
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
                        <p><span class="font-medium">Order ID:</span> #1</p>
                        <p><span class="font-medium">Order Date:</span>June 3, 2025</p>
                        <p><span class="font-medium">Total:</span> $1299.99</p>
                        <p><span class="font-medium">Status:</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                <?php
                                $order['status'] = "processing";
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
                        Address
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
                <div class="flex items-center space-x-4 p-4 border border-gray-400 rounded-lg">
                    <img src="https://picsum.photos/500" alt="" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-800">Name</h3>
                        <p class="text-gray-600">Quantity: 2</p>
                        <p class="text-gray-600">Price: $1299.99</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">
                            $1299.99
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>