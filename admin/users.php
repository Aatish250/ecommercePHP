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
    $active_page = 4;
    include '../components/admin_nav.php';
    include '../components/flashMessage.php';
    ?>

    <script>
        setFlashMesg("fail", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam, assumenda.");
    </script>

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
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Join Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orders</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Spent</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-medium text-sm">
                                                    <!-- <?php echo strtoupper(substr($user['name'], 0, 2)); ?> -->
                                                    JO
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                John Doe

                                            </div>
                                            <div class="text-sm text-gray-500">
                                                user@example.com
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex flex-col justify-center items-center">
                                    <!-- <?php echo date('M j, Y', strtotime($user['created_at'])); ?> -->
                                    June 19, 2025
                                    <span
                                        class="px-2 py-1 mt-2 w-fit text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    2
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    $400.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" class="inline">
                                        <!-- <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>"> -->
                                        <!-- <input type="hidden" name="current_status" value="<?php echo $user['status'] ?? 'active'; ?>"> -->
                                        <input type="hidden" name="user_id" value="1">
                                        <input type="hidden" name="current_status" value="active">
                                        <button type="submit" name="toggle_status"
                                            class="text-red-600 bg-red-100 px-2 py-1 rounded-md hover:text-white hover:bg-red-400 cursor-pointer">
                                            Deactivate
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