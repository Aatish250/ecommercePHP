<?php
session_start();
require '../config/db.php';

// For now, use static user_id as in profile.php
$user_id = "1";

// Fetch current user data
$userResult = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user = mysqli_fetch_assoc($userResult);

// Handle form submission
$update_success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $new_gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $new_dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $new_password = $_POST['password'];
    $update_fields = "username = '$new_username', email = '$new_email', phone = '$new_phone', shipping_address = '$new_shipping_address', gender = '$new_gender', dob = " . (empty($new_dob) ? 'NULL' : "'$new_dob'");
    if (!empty($new_password)) {
        // TODO: Use password_hash for security. Storing plain text for now (not recommended)
        // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields .= ", password = '$new_password'";
    }
    $updateQuery = "UPDATE users SET $update_fields WHERE user_id = $user_id";
    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION['message-status'] = 'success';
        $_SESSION['message'] = 'Profile updated successfully!';
        header('Location: profile.php');
        exit();
    } else {
        $update_success = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php'; ?>
    <title>Edit Profile</title>
</head>
<body class="bg-slate-200">
    <?php
    $active_page = 2;
    include '../components/user_nav.php';
    include '../components/flashMessage.php';
    ?>
    <section>
        <div class="max-w-xl mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Edit Profile</h1>
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Name</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                        <input type="text" name="phone" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Shipping Address</label>
                        <input type="text" name="shipping_address" value="<?php echo isset($user['shipping_address']) ? htmlspecialchars($user['shipping_address']) : ''; ?>" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Gender</label>
                        <select name="gender" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo isset($user['dob']) && $user['dob'] ? date('Y-m-d', strtotime($user['dob'])) : ''; ?>" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Password <span class="text-xs text-gray-400">(Leave blank to keep current)</span></label>
                        <input type="password" name="password" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" autocomplete="new-password">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save Changes</button>
                        <a href="profile.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        <?php if ($update_success === false): ?>
            setFlashMessage('fail', 'Failed to update profile.');
        <?php endif; ?>
    </script>
</body>
</html> 