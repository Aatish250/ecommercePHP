<?php
    session_start();
    require_once "config/db.php";
    
    $login_error = "";
    $signup_error = "";
    $signup_success = "";

    // Track which form to show
    $show_form = "login"; // default

    if(isset($_GET['logout']) && $_GET['logout'] == "true"){
        echo "Called to logout";
        session_unset();
        session_destroy();
        header ("Location: ".$_SERVER['PHP_SELF']);
    }

    // to set logged in timestmp
    function set_current_tiemstamp_for_last_loggedin($conn){
        $conn->query("UPDATE `users` SET `last_logged_in` = NOW() WHERE `user_id` = '{$_SESSION['user_id']}'");
    }

    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($email && $password) {
            $stmt = $conn->prepare("SELECT user_id, username, password, role, status FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $user = $result->fetch_assoc()) {
                if (isset($user['status']) && strtolower($user['status']) !== 'active') {
                    $login_error = "Account is inactive. Please contact support.";
                } elseif (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['username'] = $user['username'];
                    set_current_tiemstamp_for_last_loggedin($conn);
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        header("Location: admin/dashboard.php");
                    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
                        header("Location: user/homepage.php");
                    }
                    exit();
                } else {
                    $login_error = "Invalid email or password.";
                }
            } else {
                $login_error = "Invalid email or password.";
            }
            $stmt->close();
        } else {
            $login_error = "Please enter both email and password.";
        }
        $show_form = "login";
    }

    // Handle signup form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'signup') {
        $signup_username = isset($_POST['signup_username']) ? trim($_POST['signup_username']) : '';
        $signup_email = isset($_POST['signup_email']) ? trim($_POST['signup_email']) : '';
        $signup_password = isset($_POST['signup_password']) ? $_POST['signup_password'] : '';
        $signup_confirm = isset($_POST['signup_confirm']) ? $_POST['signup_confirm'] : '';
        $signup_phone = isset($_POST['signup_phone']) ? trim($_POST['signup_phone']) : '';
        $signup_address = isset($_POST['signup_address']) ? trim($_POST['signup_address']) : '';
        $signup_gender = isset($_POST['signup_gender']) ? trim($_POST['signup_gender']) : '';

        if ($signup_username && $signup_email && $signup_password && $signup_confirm && $signup_phone && $signup_address && $signup_gender) {
            if (!filter_var($signup_email, FILTER_VALIDATE_EMAIL)) {
                $signup_error = "Invalid email format.";
            } elseif ($signup_password !== $signup_confirm) {
                $signup_error = "Passwords do not match.";
            } elseif (strlen($signup_password) < 6) {
                $signup_error = "Password must be at least 6 characters.";
            } elseif (!preg_match('/^[0-9\-\+\s\(\)]+$/', $signup_phone)) {
                $signup_error = "Invalid phone number.";
            } elseif (!in_array($signup_gender, ['male', 'female'])) {
                $signup_error = "Please select a valid gender.";
            } else {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
                $stmt->bind_param("s", $signup_email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $signup_error = "Email already registered.";
                } else {
                    $hashed_password = password_hash($signup_password, PASSWORD_DEFAULT);
                    $role = "user";
                    $status = "active";
                    $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, phone, shipping_address, gender, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt_insert === false) {
                        $signup_error = "Database error: " . $conn->error;
                    } else {
                        $stmt_insert->bind_param("ssssssss", $signup_username, $signup_email, $hashed_password, $signup_phone, $signup_address, $signup_gender, $role, $status);
                        if ($stmt_insert->execute()) {
                            $signup_success = "Signup successful! Please login.";
                            $show_form = "login";
                        } else {
                            $signup_error = "Signup failed. Please try again.";
                            $show_form = "signup";
                        }
                        $stmt_insert->close();
                    }
                }
                $stmt->close();
            }
        } else {
            $signup_error = "Please fill all fields.";
        }
        // If there was an error, show signup form again, else show login form with success message
        if (!empty($signup_error)) {
            $show_form = "signup";
        } elseif (!empty($signup_success)) {
            $show_form = "login";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Login / Signup</title>
    <style>
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .relative-input {
            position: relative;
        }
        /* Reduce vertical gaps between form elements when a message is shown */
        .form-message {
            margin-bottom: 0.5rem;
        }
        .form-tight label,
        .form-tight input,
        .form-tight .relative-input,
        .form-tight .flex,
        .form-tight button,
        .form-tight .mt-4,
        .form-tight .text-center {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }
        @media (max-width: 640px) {
            .form-tight {
                padding: 1rem !important;
            }
        }
        /* Prevent overlap with nav bar */
        .container {
            padding-top: 5rem;
        }
    </style>
</head>

<body class="bg-slate-200">
    <nav class="absolute top-0 left-0 w-full bg-slate-100 flex justify-between items-center p-4 text-4xl z-10">
        Logo
    </nav>
    <div class="container flex flex-col justify-center items-center h-screen">
        <!-- Login Form -->
        <form id="loginForm" method="POST"
            class="bg-white py-4 px-8 rounded-lg w-90 flex flex-col md:w-92 shadow-lg <?php echo ($show_form !== 'login') ? 'hidden' : ''; ?><?php echo (!empty($login_error) || !empty($signup_success)) ? ' form-tight' : ' gap-2'; ?>">
            <input type="hidden" name="form_type" value="login">
            <div class="text-center text-2xl font-bold py-3">Login</div>
            <?php if (!empty($login_error)): ?>
                <div class="text-red-500 text-center text-sm mb-2 form-message"><?php echo htmlspecialchars($login_error); ?></div>
            <?php elseif (!empty($signup_success)): ?>
                <div class="text-green-500 text-center text-sm mb-2 form-message"><?php echo htmlspecialchars($signup_success); ?></div>
            <?php endif; ?>
            <label for="email" class="text-gray-500 text-sm -mb-2">Email</label>
            <input type="email" id="email" name="email"
                class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm"
                placeholder="email@example.com" required>
            <label for="password" class="text-gray-500 text-sm -mb-2 mt-2">Password</label>
            <div class="relative-input">
                <input type="password" id="password" name="password"
                    class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm w-full pr-10"
                    placeholder="Password" required>
                <span class="password-toggle" data-target="password" tabindex="0">
                    <svg id="eye-login" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </span>
            </div>
            <button type="submit" class="bg-indigo-500 p-2 rounded-md text-white mt-5">Login</button>
            <div class="mt-4 text-center text-gray-400 flex flex-col items-center">
                <span>No account?</span>
                <a href="javascript:void(0);" id="showSignup" class="underline text-indigo-400 hover:text-white mt-1">Sign up</a>
            </div>
        </form>
        <!-- Signup Form -->
        <form id="signupForm" method="POST"
            class="bg-white py-4 px-8 rounded-lg w-90 flex flex-col md:w-92 shadow-lg <?php echo ($show_form !== 'signup') ? 'hidden' : ''; ?><?php echo (!empty($signup_error)) ? ' form-tight' : ' gap-2'; ?>">
            <input type="hidden" name="form_type" value="signup">
            <div class="text-center text-2xl font-bold py-3">Signup</div>
            <?php if (!empty($signup_error)): ?>
                <div class="text-red-500 text-center text-sm mb-2 form-message"><?php echo htmlspecialchars($signup_error); ?></div>
            <?php endif; ?>
            <label for="signup_username" class="text-gray-500 text-sm -mb-2">Username</label>
            <input type="text" id="signup_username" name="signup_username"
                class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm"
                placeholder="Your name" required value="<?php echo isset($_POST['signup_username']) ? htmlspecialchars($_POST['signup_username']) : ''; ?>">
            <label for="signup_email" class="text-gray-500 text-sm -mb-2 mt-2">Email</label>
            <input type="email" id="signup_email" name="signup_email"
                class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm"
                placeholder="email@example.com" required value="<?php echo isset($_POST['signup_email']) ? htmlspecialchars($_POST['signup_email']) : ''; ?>">
            <label for="signup_phone" class="text-gray-500 text-sm -mb-2 mt-2">Phone</label>
            <input type="text" id="signup_phone" name="signup_phone"
                class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm"
                placeholder="Phone number" required value="<?php echo isset($_POST['signup_phone']) ? htmlspecialchars($_POST['signup_phone']) : ''; ?>">
            <label for="signup_address" class="text-gray-500 text-sm -mb-2 mt-2">Address</label>
            <input type="text" id="signup_address" name="signup_address"
                class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm"
                placeholder="Address" required value="<?php echo isset($_POST['signup_address']) ? htmlspecialchars($_POST['signup_address']) : ''; ?>">
            <label class="text-gray-500 text-sm -mb-2 mt-2">Gender</label>
            <div class="flex items-center gap-4 mb-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="signup_gender" value="male"
                        class="form-radio text-indigo-600" required
                        <?php if(isset($_POST['signup_gender']) && $_POST['signup_gender']=='male') echo 'checked'; ?>>
                    <span class="ml-2 text-gray-700">Male</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="signup_gender" value="female"
                        class="form-radio text-indigo-600" required
                        <?php if(isset($_POST['signup_gender']) && $_POST['signup_gender']=='female') echo 'checked'; ?>>
                    <span class="ml-2 text-gray-700">Female</span>
                </label>
            </div>
            <label for="signup_password" class="text-gray-500 text-sm -mb-2 mt-2">Password</label>
            <div class="relative-input">
                <input type="password" id="signup_password" name="signup_password"
                    class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm w-full pr-10"
                    placeholder="Password" required>
                <span class="password-toggle" data-target="signup_password" tabindex="0">
                    <svg id="eye-signup" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </span>
            </div>
            <label for="signup_confirm" class="text-gray-500 text-sm -mb-2 mt-2">Confirm Password</label>
            <div class="relative-input">
                <input type="password" id="signup_confirm" name="signup_confirm"
                    class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm w-full pr-10"
                    placeholder="Confirm Password" required>
                <span class="password-toggle" data-target="signup_confirm" tabindex="0">
                    <svg id="eye-signup-confirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </span>
            </div>
            <button type="submit" class="bg-indigo-500 p-2 rounded-md text-white mt-5">Signup</button>
            <div class="mt-4 text-center text-gray-400 flex flex-col items-center">
                <span>Already have an account?</span>
                <a href="javascript:void(0);" id="showLogin" class="underline text-indigo-400 hover:text-white mt-1">Login</a>
            </div>
        </form>
    </div>
    <script>
        // Toggle forms with bottom links only
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');
        const showSignup = document.getElementById('showSignup');
        const showLogin = document.getElementById('showLogin');

        function showLoginForm() {
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
        }
        function showSignupForm() {
            signupForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        }

        if (showSignup) showSignup.addEventListener('click', showSignupForm);
        if (showLogin) showLogin.addEventListener('click', showLoginForm);

        // Password view/hide functionality
        document.querySelectorAll('.password-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input) {
                    if (input.type === "password") {
                        input.type = "text";
                        this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.042-3.292m1.414-1.414A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.043 5.197M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" /></svg>`;
                    } else {
                        input.type = "password";
                        this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
                    }
                }
            });
            // Also allow keyboard toggle for accessibility
            toggle.addEventListener('keydown', function(e) {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        // Client-side password match validation for signup
        const signupFormEl = document.getElementById('signupForm');
        if (signupFormEl) {
            signupFormEl.addEventListener('submit', function(e) {
                const pass = document.getElementById('signup_password');
                const conf = document.getElementById('signup_confirm');
                if (pass && conf && pass.value !== conf.value) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    conf.focus();
                }
            });
        }
    </script>
</body>
</html>