<?php
session_start();
require '../config/verify_session.php';
verify_user("user", "../");
require '../config/db.php';

// For now, use static user_id as in profile.php
$user_id = $_SESSION['user_id'];

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
        // Hash the password before storing it
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields .= ", password = '$hashed_password'";
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
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Password <span class="text-xs text-gray-400">(Leave blank to keep current, min 8 characters)</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 pr-10" 
                                autocomplete="new-password"
                                minlength="8"
                            >
                            <button type="button" id="toggle-password" tabindex="-1" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 focus:outline-none" aria-label="Show password">
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path id="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-7 9-9 9s-9-4-9-9 7-9 9-9 9 4 9 9z" />
                                    <path id="eye-closed" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-5.12M6.1 6.1C4.29 7.64 3 9.71 3 12c0 5 7 9 9 9 1.61 0 3.13-.31 4.5-.86M17.9 17.9A8.96 8.96 0 0021 12c0-2.29-1.29-4.36-3.1-5.9" />
                                </svg>
                            </button>
                        </div>
                        <p id="password-error" class="text-xs text-red-500 mt-1 hidden">Password must be at least 8 characters.</p>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.querySelector('form[method="POST"]');
                            const passwordInput = document.getElementById('password');
                            const passwordError = document.getElementById('password-error');
                            const togglePassword = document.getElementById('toggle-password');
                            const eyeIcon = document.getElementById('eye-icon');
                            const eyeOpen = document.getElementById('eye-open');
                            const eyeClosed = document.getElementById('eye-closed');

                            // Password show/hide toggle
                            togglePassword.addEventListener('click', function() {
                                if (passwordInput.type === 'password') {
                                    passwordInput.type = 'text';
                                    eyeOpen.classList.add('hidden');
                                    eyeClosed.classList.remove('hidden');
                                } else {
                                    passwordInput.type = 'password';
                                    eyeOpen.classList.remove('hidden');
                                    eyeClosed.classList.add('hidden');
                                }
                            });

                            form.addEventListener('submit', function(e) {
                                if (passwordInput.value.length > 0 && passwordInput.value.length < 8) {
                                    passwordError.classList.remove('hidden');
                                    passwordInput.classList.add('border-red-500');
                                    e.preventDefault();
                                } else {
                                    passwordError.classList.add('hidden');
                                    passwordInput.classList.remove('border-red-500');
                                }
                            });

                            passwordInput.addEventListener('input', function() {
                                if (passwordInput.value.length >= 8 || passwordInput.value.length === 0) {
                                    passwordError.classList.add('hidden');
                                    passwordInput.classList.remove('border-red-500');
                                }
                            });
                        });
                    </script>
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