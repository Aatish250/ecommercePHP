<?php
session_start();
require '../config/verify_session.php';
verify_user("admin", "../");
$active_page = 4;
require_once '../config/db.php';
include '../components/admin_nav.php';
include '../components/flashMessage.php';

// Get user_id from GET
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo '<div class="p-6 text-red-600">Invalid user ID.</div>';
    exit();
}
$user_id = intval($_GET['user_id']);

// Query user_details view
$sql = "SELECT * FROM user_details WHERE user_id = $user_id";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    echo '<div class="p-6 text-red-600">User not found.</div>';
    exit();
}
$user = $result->fetch_assoc();
$status = strtolower($user['user_statu']);
$badgeClass = $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
$badgeText = ucfirst($status);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <?php require '../components/link_imports.php'; ?>
    <title>User Profile</title>
    <style>
        .profile-md-text { font-size: 1.125rem; }
        .profile-lg-text { font-size: 1.25rem; }
        .profile-xl-text { font-size: 1.5rem; }
    </style>
</head>
<body class="bg-slate-200">
    <section class="container mx-auto mt-8">
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-xl mx-auto">
            <div class="flex items-center mb-8">
                <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center profile-2xl-text font-bold text-indigo-600" style="font-size:2rem;">
                    <?php echo ucfirst(substr($user['username'], 0, 2)); ?>
                </div>
                <div class="ml-6">
                    <h2 class="profile-xl-text font-bold text-gray-900 flex items-center">
                        <?php echo htmlspecialchars($user['username']); ?>
                        <span class="ml-4 px-4 py-1 text-base font-semibold rounded-full <?php echo $badgeClass; ?>">
                            <?php echo $badgeText; ?>
                        </span>
                    </h2>
                    <div class="flex items-center mt-2 text-gray-500 profile-md-text">
                        <!-- Email icon -->
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    <div class="flex items-center mt-1 text-gray-500 profile-md-text">
                        <!-- Call icon -->
                        <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.86 19.86 0 0 1 3.09 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.13.81.36 1.6.68 2.34a2 2 0 0 1-.45 2.11l-1.27 1.27a16 16 0 0 0 6.29 6.29l1.27-1.27a2 2 0 0 1 2.11-.45c.74.32 1.53.55 2.34.68A2 2 0 0 1 22 16.92z"/></svg>
                        <?php echo htmlspecialchars($user['phone']); ?>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Calendar icon for join date -->
                    <svg class="w-7 h-7 text-blue-500 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <div>
                        <div class="text-base text-gray-500">Join Date</div>
                        <div class="font-bold text-gray-900 profile-lg-text"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Box icon for total orders -->
                    <svg class="w-7 h-7 text-amber-500 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/></svg>
                    <div>
                        <div class="text-base text-gray-500">Total Orders</div>
                        <div class="font-bold text-gray-900 profile-lg-text"><?php echo (int)$user['total_orders']; ?></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Box icon for paid items -->
                    <svg class="w-7 h-7 text-green-500 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/></svg>
                    <div>
                        <div class="text-base text-gray-500">Paid Items</div>
                        <div class="font-bold text-gray-900 profile-lg-text"><?php echo (int)$user['paid_items']; ?></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Box icon for pending items -->
                    <svg class="w-7 h-7 text-red-500 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/></svg>
                    <div>
                        <div class="text-base text-gray-500">Pending Items</div>
                        <div class="font-bold text-gray-900 profile-lg-text"><?php echo (int)$user['pending_items']; ?></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Box icon for total items -->
                    <svg class="w-7 h-7 text-blue-800 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/></svg>
                    <div>
                        <div class="text-base text-gray-500">Total Items</div>
                        <div class="font-bold text-gray-900 profile-lg-text"><?php echo (int)$user['total_items']; ?></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Slim Nepali Rupees icon for total spent -->
                    <span class="w-7 h-7 mr-4 flex items-center justify-center rounded-full bg-green-100">
                        <svg class="w-7 h-7 text-green-700" fill="none" viewBox="0 0 28 28">
                            <text x="14" y="20" text-anchor="middle" font-size="22" font-family="Arial, sans-serif" font-weight="400" fill="currentColor" style="font-stretch:condensed;">रु</text>
                        </svg>
                    </span>
                    <div>
                        <div class="text-base text-gray-500">Total Spent</div>
                        <div class="font-bold text-gray-900 profile-lg-text">Rs. <?php echo number_format($user['paid_total'], 2); ?></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Slim Nepali Rupees icon for pending total -->
                    <span class="w-7 h-7 mr-4 flex items-center justify-center rounded-full bg-gray-200">
                        <svg class="w-7 h-7 text-gray-700" fill="none" viewBox="0 0 28 28">
                            <text x="14" y="20" text-anchor="middle" font-size="22" font-family="Arial, sans-serif" font-weight="400" fill="currentColor" style="font-stretch:condensed;">रु</text>
                        </svg>
                    </span>
                    <div>
                        <div class="text-base text-gray-500">Pending Total</div>
                        <div class="font-bold text-gray-900 profile-lg-text">Rs. <?php echo number_format($user['pending_total'], 2); ?></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-xl p-4 flex items-center">
                    <!-- Slim Nepali Rupees icon for total transaction -->
                    <span class="w-7 h-7 mr-4 flex items-center justify-center rounded-full bg-indigo-100">
                        <svg class="w-7 h-7 text-indigo-700" fill="none" viewBox="0 0 28 28">
                            <text x="14" y="20" text-anchor="middle" font-size="22" font-family="Arial, sans-serif" font-weight="400" fill="currentColor" style="font-stretch:condensed;">रु</text>
                        </svg>
                    </span>
                    <div>
                        <div class="text-base text-gray-500">Total Transaction</div>
                        <div class="font-bold text-gray-900 profile-lg-text">Rs. <?php echo number_format($user['total_transaction'], 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html> 