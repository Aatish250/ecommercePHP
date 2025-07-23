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
    require_once '../config/db.php';
    ?>

    <script>
    // Helper to render status badge
    function renderStatusBadge(status) {
        const map = {
            pending: ['bg-yellow-100 text-yellow-800', 'Pending'],
            processing: ['bg-blue-100 text-blue-800', 'Processing'],
            shipped: ['bg-purple-100 text-purple-800', 'Shipped'],
            delivered: ['bg-green-100 text-green-800', 'Delivered'],
            cancelled: ['bg-red-100 text-red-800', 'Cancelled'],
        };
        const badge = map[status] || ['bg-gray-100 text-gray-800', status];
        return `<span class="px-2 py-1 text-xs font-semibold rounded-full ${badge[0]}">${badge[1]}</span>`;
    }

    // Render orders into table
    function renderOrders(orders) {
        const tbody = document.getElementById('orders-table-body');
        tbody.innerHTML = '';
        if (!orders.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-6 text-gray-500">No orders found.</td></tr>';
            return;
        }
        orders.forEach(order => {
            tbody.innerHTML += `
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${order.order_id}</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${order.username || 'Unknown'}</div>
                        <div class="text-sm text-gray-500">${order.email || ''}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${order.created_at ? (new Date(order.created_at)).toLocaleDateString() : ''}</td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                        $${order.total}
                        <a href="order-details.php?order_id=${order.order_id}" class="text-indigo-600 hover:text-indigo-900 text-sm mt-1 block">View Details</a>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        ${renderStatusBadge(order.status)}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center justify-center order-status-form" data-order-id="${order.order_id}">
                            <label for="status-select-${order.order_id}" class="bg-blue-200 text-xs text-gray-600 rounded-l px-2 py-1 h-[34px] flex items-center border border-r-0 border-gray-300">
                                Change Status:
                            </label>
                            <select id="status-select-${order.order_id}" name="new_status" class="text-sm w-[120px] border border-gray-300 border-l-0 rounded-r px-2 py-1 order-status-select h-[34px]">
                                <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="processing" ${order.status === 'processing' ? 'selected' : ''}>Processing</option>
                                <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''}>Shipped</option>
                                <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                                <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    // Fetch orders from backend
    function fetchOrders() {
        const search = document.getElementById('order-search').value;
        const status = document.getElementById('order-status-filter').value;
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        fetch('proccess/orders_ajax.php?' + params.toString())
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderOrders(data.orders);
                }
            });
    }

    // Update order status via AJAX
    function updateOrderStatus(order_id, new_status, cb) {
        const formData = new FormData();
        formData.append('order_id', order_id);
        formData.append('new_status', new_status);
        fetch('proccess/orders_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (cb) cb(data);
        });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        fetchOrders();
        document.getElementById('order-search-btn').addEventListener('click', fetchOrders);
        document.getElementById('order-search').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') fetchOrders();
        });
        document.getElementById('order-status-filter').addEventListener('change', fetchOrders);

        // Delegate status change and form submit
        document.getElementById('orders-table-body').addEventListener('change', function(e) {
            if (e.target.classList.contains('order-status-select')) {
                const form = e.target.closest('.order-status-form');
                const order_id = form.dataset.orderId;
                const new_status = e.target.value;
                updateOrderStatus(order_id, new_status, function(data) {
                    if (data.success) fetchOrders();
                });
            }
        });
    });
    </script>

    <section class="container mx-auto">
        <h2 class="p-5 font-bold text-3xl text-blue-900">Order Management</h2>
        <!-- filters -->
        <div class="flex flex-col">
            <div class="bg-white p-2 m-1 rounded-md shadow-md flex space-x-3">
                <input id="order-search" type="text" class="outline-1 outline-gray-300 rounded-lg w-full px-3"
                    placeholder="Search by customer name, email, or order ID...">
                <select id="order-status-filter" name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button id="order-search-btn" class="bg-blue-500 text-white p-2 px-4 rounded-md">Search</button>
            </div>
            <!-- Orders Table -->
            <div class="bg-white m-1 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Orders will be rendered here by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</body>

</html>