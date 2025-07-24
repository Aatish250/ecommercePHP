<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <?php require '../components/link_imports.php'; ?>
    <title>User Management</title>
</head>

<body class="bg-slate-200">
<?php
    session_start();
    require '../config/verify_session.php';
    verify_user("admin", "../");
    $active_page = 4;
    require_once '../config/db.php';
    include '../components/admin_nav.php';
    include '../components/flashMessage.php';

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $user_id = $_POST['toggle_user_id'];

        $res = mysqli_query($conn, "SELECT user_id, status FROM users where user_id = $user_id");
        $user = mysqli_fetch_assoc($res);

        if ($user) {
            $current_status = $user['status'];
            $status = ($current_status == 'active' ? 'inactive' : 'active');

            $res_update = mysqli_query($conn, "UPDATE users SET status = '$status' where user_id = '$user_id'");

            if($res_update){
                $_SESSION['flash_message'] = "Updated user: $user_id $status";
                $_SESSION['flash_message_status'] = "success";
            } else {
                $_SESSION['flash_message'] = "Failed to update user status for ID: $user_id";
                $_SESSION['flash_message_status'] = "error";
            }
        } else {
            $_SESSION['flash_message'] = "User not found for ID: $user_id";
            $_SESSION['flash_message_status'] = "error";
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Show and clear flash message if set (reference to other session flash message usage)
    if (isset($_SESSION['flash_message']) && isset($_SESSION['flash_message_status'])) {
        echo "<script>setFlashMessage(\"{$_SESSION['flash_message_status']}\", \"{$_SESSION['flash_message']}\");</script>";
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_message_status']);
    }
?>

    <section class="container mx-auto">
        <h2 class="p-5 font-bold text-3xl text-blue-900">User Management</h2>
        <div class="flex flex-col">
            <div class="bg-white p-2 m-1 rounded-md shadow-md flex space-x-3">
                <input type="text" class="outline-1 outline-gray-300 rounded-lg w-full px-3"
                    placeholder="Search Name...">
                <button class="bg-blue-500 text-white p-2 px-4 rounded-md">Search</button>
            </div>
            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden m-1 mt-2">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Join Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orders</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Spent</th>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        require_once '../config/db.php';
                        // Use the user_details view as per your context
                        $sql = "SELECT * FROM user_details";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0):
                            while($user = $result->fetch_assoc()):
                                $status = strtolower($user['user_statu']);
                                $badgeClass = $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                $badgeText = ucfirst($status);
                        ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-medium text-sm">
                                                    <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user['username']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex flex-col justify-center items-center">
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                    <span class="px-2 py-1 mt-2 w-fit text-xs font-semibold rounded-full <?php echo $badgeClass; ?>">
                                        <?php echo $badgeText; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo (int)$user['total_orders']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    $<?php echo number_format($user['paid_total'], 2); ?>
                                </td>
                                <td class="px-6 w-3 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" class="inline">
                                        <button name="toggle_user_id"
                                            value = "<?php echo $user['user_id']?>"
                                            class="<?php echo $status === 'active' ? 'text-red-600 bg-red-100 hover:text-white hover:bg-red-400' : 'text-green-600 bg-green-100 hover:text-white hover:bg-green-400'; ?> px-2 py-1 rounded-md cursor-pointer">
                                            <?php echo $status === 'active' ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="user-profile.php?user_id=<?php echo $user['user_id']; ?>" class="group inline-flex items-center justify-center" title="View Profile">
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 transition-colors duration-200 group-hover:bg-blue-500">
                                            <svg class="w-6 h-6 text-blue-500 transition-colors duration-200 group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <circle cx="12" cy="9" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                                                <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 19c0-2.21 2.91-4 6-4s6 1.79 6 4"/>
                                            </svg>
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-6 text-gray-500">No users found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</body>

</html>