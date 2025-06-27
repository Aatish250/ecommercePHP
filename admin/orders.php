<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <title>Order Management</title>
    <?php require '../components/link_imports.php'; ?>
</head>

<body class="bg-slate-200">
    <?php
    $active_page = 3;
    include '../components/admin_nav.php';
    include '../components/flashMessage.php';
    ?>

    <script>
        setFlashMesg("success", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam, assumenda.");
    </script>

    <section class="container mx-auto">
        <h2 class="p-5 font-bold text-3xl text-blue-900">Order Management</h2>
        <!-- filters -->
        <div class="flex flex-col">
            <div class="bg-white p-2 m-1 rounded-md shadow-md flex space-x-3">
                <input type="text" class="outline-1 outline-gray-300 rounded-lg w-full px-3"
                    placeholder="Search by customer name, email, or order ID...">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button class="bg-blue-500 text-white p-2 px-4 rounded-md">Search</button>
            </div>
            <!-- Orders Table -->
            <div class="bg-white m-1 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order ID</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #2
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Jhon Doe</div>
                                    <div class="text-sm text-gray-500">user@example.com</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <!-- <?php echo date('M j, Y', strtotime($order['created_at'])); ?> -->
                                    Jun 21, 2025

                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    $200.00
                                    <a href="" class="text-indigo-600 hover:text-indigo-900 text-sm mt-1 block">
                                        View Details
                                    </a>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <!-- switch($order['status']) {
                                                case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                                case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                                case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            } -->
                                        Pending
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST"
                                        class="flex items-center justify-center max-md:flex-col max-md:space-y-2 md:space-x-2">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="new_status"
                                            class="text-sm w-[120px] border border-gray-300 rounded px-2 py-1">
                                            <option value="pending">Pending</option>
                                            <option value="processing">Processing</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status"
                                            class="bg-indigo-600 text-white px-3 py-1 max-md:w-full rounded text-sm hover:bg-indigo-700">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #2
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Jhon Doe</div>
                                    <div class="text-sm text-gray-500">user@example.com</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <!-- <?php echo date('M j, Y', strtotime($order['created_at'])); ?> -->
                                    Jun 21, 2025

                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    $200.00
                                    <a href="" class="text-indigo-600 hover:text-indigo-900 text-sm mt-1 block">
                                        View Details
                                    </a>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <!-- switch($order['status']) {
                                                case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                                case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                                case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            } -->
                                        Processing
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST"
                                        class="flex items-center justify-center max-md:flex-col max-md:space-y-2 md:space-x-2">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="new_status"
                                            class="text-sm w-[120px] border border-gray-300 rounded px-2 py-1">
                                            <option value="pending">Pending</option>
                                            <option value="processing" selected>Processing</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status"
                                            class="bg-indigo-600 text-white px-3 py-1 max-md:w-full rounded text-sm hover:bg-indigo-700">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</body>

</html>