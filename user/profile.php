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
    ?>

    <section>
        <div class="max-w-7xl mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">My Account</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Account Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Information</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Name</label>
                                <p class="text-gray-800">Username</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Email</label>
                                <p class="text-gray-800">user@example.com</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Member Since</label>
                                <p class="text-gray-800">
                                    June 19, 2025
                                </p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="edit-profile.php"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Order History</h2>


                        <p class="text-gray-500">No orders found.</p>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Order ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">

                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">#2</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            June 19, 2025
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            $200.00
                                        </td>
                                        <?php $order['status'] = 'processing' ?>
                                        <td class="px-4 py-3 text-sm">
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
                                                Processing
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <a href="order_traking.php" class="text-indigo-600 hover:text-indigo-900">
                                                Track Order
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>