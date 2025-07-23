<?php
require '../config/db.php';
$active_page = 2; // For nav highlighting
include '../components/admin_nav.php';
include '../components/flashMessage.php';

// Get date range for filtering
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get sales data (daily revenue and order count)
$sales_data = array();
$salesQuery = "SELECT DATE(created_at) as sale_date, COUNT(*) as orders_count, SUM(total) as daily_total FROM orders WHERE status != 'cancelled' AND DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY sale_date DESC";
if ($stmt = mysqli_prepare($conn, $salesQuery)) {
    mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $sales_data[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Get top selling products using the order_details view
$top_products = array();
$topProductsQuery = "SELECT product_name, order_price as product_price, SUM(quantity) as total_sold, SUM(sub_total) as revenue FROM order_details WHERE order_id IN (SELECT order_id FROM orders WHERE status != 'cancelled' AND DATE(created_at) BETWEEN ? AND ?) GROUP BY product_id ORDER BY total_sold DESC LIMIT 10";
if ($stmt = mysqli_prepare($conn, $topProductsQuery)) {
    mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $top_products[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Calculate totals
$total_revenue = 0;
$total_orders = 0;
foreach ($sales_data as $sale) {
    $total_revenue += $sale['daily_total'];
    $total_orders += $sale['orders_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <?php require '../components/link_imports.php'; ?>
    <title>Sales Report</title>
</head>
<body class="bg-slate-200">
    <div class="container m-auto p-3">
        <h2 class="p-5 font-bold text-3xl text-blue-900">Sales Report</h2>
        <!-- Date Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" class="flex flex-col md:flex-row md:space-x-6 space-y-4 md:space-y-0 items-stretch md:items-end w-full">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input 
                        type="date" 
                        id="start_date" 
                        name="start_date" 
                        value="<?php echo $start_date; ?>" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input 
                        type="date" 
                        id="end_date" 
                        name="end_date" 
                        value="<?php echo $end_date; ?>" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>
                <div class="flex-shrink-0 flex items-end">
                    <button type="submit" class="w-full md:w-auto bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Filter</button>
                </div>
            </form>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                const todayStr = `${yyyy}-${mm}-${dd}`;

                const startInput = document.getElementById('start_date');
                const endInput = document.getElementById('end_date');

                // Set max for both to today
                startInput.max = todayStr;
                endInput.max = todayStr;

                // Set min for end date to start date
                function updateEndMin() {
                    endInput.min = startInput.value;
                    // If end date is before start date, adjust it
                    if (endInput.value < startInput.value) {
                        endInput.value = startInput.value;
                    }
                }

                // Set max for start date to end date (but not after today)
                function updateStartMax() {
                    if (endInput.value) {
                        // The max for start date is the lesser of end date and today
                        startInput.max = (endInput.value < todayStr) ? endInput.value : todayStr;
                        if (startInput.value > startInput.max) {
                            startInput.value = startInput.max;
                        }
                    } else {
                        startInput.max = todayStr;
                    }
                }

                startInput.addEventListener('change', function() {
                    updateEndMin();
                });

                endInput.addEventListener('change', function() {
                    updateStartMax();
                });

                // On page load, set the correct min/max
                updateEndMin();
                updateStartMax();
            });
        </script>
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">Rs.<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $total_orders; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Average Order Value</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            Rs.<?php echo $total_orders > 0 ? number_format($total_revenue / $total_orders, 2) : '0.00'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Daily Sales -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Daily Sales</h2>
                <?php if (empty($sales_data)): ?>
                    <p class="text-gray-500">No sales data for the selected period.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($sales_data as $sale): ?>
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <?php echo date('M j, Y', strtotime($sale['sale_date'])); ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $sale['orders_count']; ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900">Rs.<?php echo number_format($sale['daily_total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Top Selling Products -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Top Selling Products</h2>
                <?php if (empty($top_products)): ?>
                    <p class="text-gray-500">No product sales data for the selected period.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($top_products as $product): ?>
                            <div class="flex justify-between items-center p-3 border rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($product['product_name']); ?></p>
                                    <p class="text-sm text-gray-600">Units sold: <?php echo $product['total_sold']; ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">Rs.<?php echo number_format($product['revenue'], 2); ?></p>
                                    <p class="text-sm text-gray-600">Rs.<?php echo number_format($product['product_price'], 2); ?> each</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
