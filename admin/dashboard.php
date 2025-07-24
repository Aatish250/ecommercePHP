<?php
session_start();
require '../config/verify_session.php';
verify_user("admin", "../");
require '../config/db.php';

// Initialize variables with safe defaults
$total_products = 0;
$total_users = 0;
$low_stock_products = array();
$orders = array();


// Total Sales AND Orders
$res = mysqli_query($conn, "SELECT COUNT(*) as cnt, COALESCE(sum(total), 0) as sum_total FROM orders");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $total_orders = $row['cnt'];
    $total_sales = $row['sum_total'];
}

// Total Products
$res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM products");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $total_products = $row['cnt'];
}

// Total Users
$res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $total_users = $row['cnt'];
}

// Low Stock Products (stock <= 2 is No Stock, 3-5 is Low Stock)
$res = mysqli_query($conn, "SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $low_stock_products[] = $row;
    }
}

// recent orders
$res = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at ASC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $orders[] = $row;
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <?php require '../components/link_imports.php'; ?>
    <title>Dashboard</title>
</head>

<body class="bg-slate-200">

    <?php
    $active_page = 0;
    include '../components/admin_nav.php';
    include '../components/flashMessage.php';
    ?>

    <script>
        setFlashMesg("info", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam, assumenda.");
    </script>

    <section>
        <div class="container m-auto p-3">

            <h2 class="p-5 font-bold text-3xl text-blue-900">Dashboard</h2>
            <div class="flex flex-col flex-wrap md:flex-row">
                <!-- total sales -->
                <div
                    class="px-5 py-3 m-1 rounded-lg flex items-center bg-white max-md:justify-between max-md:items-center md:flex-1/3 lg:flex-1/5 shadow-sm">
                    <!-- <span><img class="w-8" src="../img/coins.svg" alt=""></span> -->
                    <span class="bg-green-200 p-3 rounded-full">
                        <svg class="w-6 h-auto fill-green-600" xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                            data-name="Layer 1" viewBox="0 0 24 24" width="512" height="512">
                            <path
                                d="M16.5,0c-4.206,0-7.5,1.977-7.5,4.5v2.587c-.483-.057-.985-.087-1.5-.087C3.294,7,0,8.977,0,11.5v8c0,2.523,3.294,4.5,7.5,4.5,3.407,0,6.216-1.297,7.16-3.131,.598,.087,1.214,.131,1.84,.131,4.206,0,7.5-1.977,7.5-4.5V4.5c0-2.523-3.294-4.5-7.5-4.5Zm5.5,12.5c0,1.18-2.352,2.5-5.5,2.5-.512,0-1.014-.035-1.5-.103v-1.984c.49,.057,.992,.087,1.5,.087,2.194,0,4.14-.538,5.5-1.411v.911ZM2,14.589c1.36,.873,3.306,1.411,5.5,1.411s4.14-.538,5.5-1.411v.911c0,1.18-2.352,2.5-5.5,2.5s-5.5-1.32-5.5-2.5v-.911Zm20-6.089c0,1.18-2.352,2.5-5.5,2.5-.535,0-1.06-.038-1.566-.112-.193-.887-.8-1.684-1.706-2.323,.984,.28,2.092,.435,3.272,.435,2.194,0,4.14-.538,5.5-1.411v.911Zm-5.5-6.5c3.148,0,5.5,1.32,5.5,2.5s-2.352,2.5-5.5,2.5-5.5-1.32-5.5-2.5,2.352-2.5,5.5-2.5ZM7.5,9c3.148,0,5.5,1.32,5.5,2.5s-2.352,2.5-5.5,2.5-5.5-1.32-5.5-2.5,2.352-2.5,5.5-2.5Zm0,13c-3.148,0-5.5-1.32-5.5-2.5v-.911c1.36,.873,3.306,1.411,5.5,1.411s4.14-.538,5.5-1.411v.911c0,1.18-2.352,2.5-5.5,2.5Zm9-3c-.512,0-1.014-.035-1.5-.103v-1.984c.49,.057,.992,.087,1.5,.087,2.194,0,4.14-.538,5.5-1.411v.911c0,1.18-2.352,2.5-5.5,2.5Z" />
                        </svg>
                    </span>
                    <div class="flex mb-1 md:flex-col w-full">
                        <span class="flex-1 ml-5 text-[18px] text-slate-700">Total Sales</span>
                        <span class="mr-5 ml-5 font-bold text-2xl text-slate-500">Rs. <?php echo $total_sales; ?></span>
                    </div>
                </div>
                <!-- Total order -->
                <div
                    class="px-5 py-3 m-1 rounded-lg flex items-center bg-white max-md:justify-between max-md:items-center md:flex-1/3 lg:flex-1/5 shadow-sm">
                    <!-- <span><img class="w-6" src="../img/shopping-cart.svg" alt=""></span> -->
                    <span class="bg-amber-100 p-3 rounded-full">
                        <svg class="w-6 h-auto fill-amber-400" xmlns="http://www.w3.org/2000/svg" id="Outline"
                            viewBox="0 0 24 24" width="512" height="512">
                            <path
                                d="M21,6H18A6,6,0,0,0,6,6H3A3,3,0,0,0,0,9V19a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V9A3,3,0,0,0,21,6ZM12,2a4,4,0,0,1,4,4H8A4,4,0,0,1,12,2ZM22,19a3,3,0,0,1-3,3H5a3,3,0,0,1-3-3V9A1,1,0,0,1,3,8H6v2a1,1,0,0,0,2,0V8h8v2a1,1,0,0,0,2,0V8h3a1,1,0,0,1,1,1Z" />
                        </svg>
                    </span>
                    <div class="flex mb-1 md:flex-col w-full">
                        <span class="flex-1 ml-5 text-[18px] text-slate-700">Total Orders</span>
                        <span class="mr-5 ml-5 font-bold text-2xl text-slate-500"><?php echo $total_orders; ?></span>
                    </div>
                </div>
                <!-- total products -->
                <div
                    class="px-5 py-3 m-1 rounded-lg flex items-center bg-white max-md:justify-between max-md:items-center md:flex-1/3 lg:flex-1/5 shadow-sm">
                    <!-- <span><img class="w-6" src="../img/apps.svg" alt=""></span> -->
                    <span class="bg-indigo-200 p-3 rounded-full">
                        <svg class="w-8 h-auto fill-indigo-600" xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                            data-name="Layer 1" viewBox="0 0 24 24" width="512" height="512">
                            <path
                                d="M23.61,10.836l-1.718-3.592c-.218-.455-.742-.678-1.219-.517l-8.677,2.896L3.307,6.728c-.477-.159-1.001,.062-1.219,.518L.436,10.719c-.477,.792-.567,1.743-.247,2.609,.31,.84,.964,1.491,1.801,1.795l-.006,2.315c0,2.157,1.373,4.065,3.419,4.747l4.365,1.456c.714,.237,1.464,.356,2.214,.356s1.5-.119,2.214-.357l4.369-1.456c2.044-.682,3.418-2.586,3.419-4.738l.006-2.322c.846-.296,1.508-.945,1.819-1.788,.316-.858,.228-1.8-.198-2.5Zm-21.416,.83l1.318-2.763,7.065,2.354-1.62,3.256c-.242,.406-.729,.584-1.174,.436l-5.081-1.695c-.298-.099-.53-.324-.639-.618-.108-.293-.078-.616,.13-.97Zm3.842,8.623c-1.228-.41-2.053-1.555-2.052-2.848l.004-1.65,3.164,1.055c1.346,.446,2.793-.09,3.559-1.372l.277-.555-.004,6.979c-.197-.04-.391-.091-.582-.154l-4.365-1.455Zm11.896-.002l-4.369,1.456c-.19,.063-.384,.115-.58,.155l.004-6.995,.319,.64c.557,.928,1.532,1.46,2.562,1.46,.318,0,.643-.051,.96-.157l3.16-1.053-.004,1.651c0,1.292-.825,2.435-2.052,2.844Zm4-7.645c-.105,.285-.331,.504-.619,.601l-5.118,1.705c-.438,.147-.935-.036-1.136-.365l-1.654-3.322,7.064-2.357,1.382,2.88c.156,.261,.187,.574,.081,.859ZM5.214,5.896c-.391-.391-.391-1.023,0-1.414L9.111,.586c.779-.779,2.049-.779,2.828,0l1.596,1.596c.753-.385,1.738-.27,2.353,.345l2.255,2.255c.391,.391,.391,1.023,0,1.414s-1.023,.391-1.414,0l-2.255-2.255-3.151,3.151c-.195,.195-.451,.293-.707,.293s-.512-.098-.707-.293c-.391-.391-.391-1.023,0-1.414l2.147-2.147-1.53-1.53-3.897,3.896c-.195,.195-.451,.293-.707,.293s-.512-.098-.707-.293Z" />
                        </svg>
                    </span>
                    <div class="flex mb-1 md:flex-col w-full">
                        <span class="flex-1 ml-5 text-[18px] text-slate-700">Total Products</span>
                        <span class="mr-5 ml-5 font-bold text-2xl text-slate-500"><?php echo $total_products; ?></span>
                    </div>
                </div>
                <!-- total users -->
                <div
                    class="px-5 py-3 m-1 rounded-lg flex items-center bg-white max-md:justify-between max-md:items-center md:flex-1/3 lg:flex-1/5 shadow-sm">
                    <span class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-auto fill-blue-400" xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                            data-name="Layer 1" viewBox="0 0 24 24">
                            <path
                                d="M17.5,24c-3.584,0-6.5-2.916-6.5-6.5s2.916-6.5,6.5-6.5,6.5,2.916,6.5,6.5-2.916,6.5-6.5,6.5Zm0-11c-2.481,0-4.5,2.019-4.5,4.5s2.019,4.5,4.5,4.5,4.5-2.019,4.5-4.5-2.019-4.5-4.5-4.5Zm.999,6.354l1.886-1.833c.396-.385,.405-1.018,.021-1.414-.385-.395-1.018-.406-1.414-.02l-1.892,1.838c-.099,.1-.262,.1-.362,0l-.876-.858c-.395-.386-1.027-.379-1.414,.016s-.38,1.027,.015,1.414l.876,.858c.437,.428,1.01,.641,1.582,.641s1.146-.215,1.579-.643Zm-9.499-7.354c-3.309,0-6-2.691-6-6S5.691,0,9,0s6,2.691,6,6-2.691,6-6,6Zm0-10c-2.206,0-4,1.794-4,4s1.794,4,4,4,4-1.794,4-4-1.794-4-4-4ZM2,23c0-3.524,2.633-6.511,6.124-6.946,.548-.068,.937-.568,.869-1.116s-.574-.931-1.116-.868C3.386,14.629,0,18.469,0,23c0,.553,.448,1,1,1s1-.447,1-1Z" />
                        </svg>
                    </span>
                    <div class="flex mb-1 md:flex-col w-full">
                        <span class="flex-1 ml-5 text-[18px] text-slate-700">Total Users</span>
                        <span class="mr-5 ml-5 font-bold text-2xl text-slate-500"><?php echo $total_users; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto flex justify-between space-x-6 flex-col lg:flex-row mb-10 px-3">
        <div class="w-full lg:w-1/2 bg-white mt-6 rounded-lg p-4 min-h-24 h-fit">
            <div class="text-2xl font-medium text-slate-500">Low Stock Alert</div>
            <div class="border-2p-2 mt-4 rounded-lg">
                <table class="w-full">
                    <tr class="text-slate-500">
                        <th class="border-r-2 border-b-2 border-slate-100">Id</th>
                        <th class="border-r-2 border-b-2 border-slate-100">Item</th>
                        <th class="border-b-2 border-slate-100">Status</th>
                    </tr>
                    <?php foreach ($low_stock_products as $prod): ?>
                    <tr class="text-center <?php echo ($prod['stock'] <= 2) ? 'bg-red-100' : ''; ?>">
                        <td class="border-l-5 <?php echo ($prod['stock'] <= 2) ? 'border-red-300' : 'border-amber-300'; ?>"><?php echo $prod['product_id']; ?></td>
                        <td class="text-left w-3/4 flex flex-col p-1 pl-6">
                            <span class="text-xl"><?php echo htmlspecialchars($prod['product_name']); ?></span>
                            <span class="text-sm text-slate-500">Stock: <?php echo $prod['stock']; ?></span>
                        </td>
                        <td>
                            <?php if ($prod['stock'] <= 2): ?>
                                <div class="w-fit bg-red-200 text-red-600 m-auto px-3 py-2 my-1 rounded-full text-sm">No Stock</div>
                            <?php else: ?>
                                <div class="w-fit bg-amber-100 text-amber-600 m-auto px-3 py-2 my-1 rounded-full text-sm">Low Stock</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>


        <div class="w-full lg:w-1/2 bg-white mt-6 rounded-lg p-4 min-h-24">
            <div class="text-2xl font-medium text-slate-700">Recent Orders</div>
            <div class="mt-4 space-y-4">

                <?php
                foreach ($orders as $order):
                    // Fetch username
                    $userResult = mysqli_query($conn, "SELECT username FROM users WHERE user_id = {$order['user_id']}");
                    $username = ($userResult && $row = mysqli_fetch_assoc($userResult)) ? $row['username'] : 'Unknown';

                    // Fetch total quantity from order_details view
                    $qtyResult = mysqli_query($conn, "SELECT SUM(quantity) AS total_quantity FROM order_details WHERE order_id = {$order['order_id']}");
                    $quantity = ($qtyResult && $qtyRow = mysqli_fetch_assoc($qtyResult)) ? ($qtyRow['total_quantity'] ?? 0) : 0;

                    // Status badge color
                    $status = strtolower($order['status'] ?? 'Processing');
                    $status_map = [
                        'delivered' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-slate-100 text-slate-700',
                        'pending' => 'bg-amber-100 text-amber-700',
                        'processing' => 'bg-blue-100 text-blue-700'
                    ];
                    $status_class = $status_map[$status] ?? 'bg-blue-100 text-blue-700';
                ?>
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-center">
                            <div class="text-gray-600"><?php echo htmlspecialchars($username); ?></div>
                            <div class="text-sm text-gray-500"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></div>
                        </div>
                        <div class="flex-1 ml-5">
                            <div class="text-lg font-medium text-gray-900">Order #<?php echo $order['order_id']; ?></div>
                            <div class="text-sm text-gray-500">Quantity: <?php echo $quantity; ?></div>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-lg font-semibold text-gray-900">
                                $<?php echo number_format($order['total'] ?? 0, 2); ?>
                            </div>
                            <div class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-sm font-medium">
                                <?php echo ucfirst($status); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>
</body>

</html>