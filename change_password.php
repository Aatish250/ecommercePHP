<?php
session_start();
// require '../config/verify_session.php';
// verify_user("user", "../");
require 'config/db.php';

// Handle password change submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password_user_id'])) {
    $change_user_id = intval($_POST['change_password_user_id']);
    $new_password = $_POST['new_password'] ?? '';
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashed_password, $change_user_id);
        $stmt->execute();
        $stmt->close();
        $password_change_success = true;
    } else {
        $password_change_error = "Password cannot be empty.";
    }
}

$res = $conn->query("SELECT * from users");
?>
<script>
function showPasswordInput(userId) {
    document.querySelectorAll('.password-change-form').forEach(f => f.style.display = 'none');
    var form = document.getElementById('password-form-' + userId);
    if (form) form.style.display = 'block';
}
</script>
<?php
if($res->num_rows > 0){
    while($row = $res->fetch_assoc()):
?>
    <form style="display:inline-block;">
        <?php echo $row['user_id'];?> | <?php echo $row['role'];?> | <?php echo $row['username'];?> 
        <button type="button" onclick="showPasswordInput(<?php echo $row['user_id']; ?>)">Change Password</button>
    </form>
    <br>
<?php endwhile; ?>
    <!-- Password change forms (hidden by default) -->
    <?php
    // Reset result pointer and fetch again for forms
    $res->data_seek(0);
    while($row = $res->fetch_assoc()):
    ?>
        <form 
            id="password-form-<?php echo $row['user_id']; ?>" 
            class="password-change-form" 
            method="post" 
            style="display:none; margin-top:5px;"
        >
            <input type="hidden" name="change_password_user_id" value="<?php echo $row['user_id']; ?>">
            <input type="password" name="new_password" placeholder="New Password" required>
            <button type="submit">Change Password</button>
        </form>
    <?php endwhile; ?>
    <?php
    if (isset($password_change_success) && $password_change_success) {
        echo "<div style='color:green;'>Password updated successfully!</div>";
    } elseif (isset($password_change_error)) {
        echo "<div style='color:red;'>$password_change_error</div>";
    }
}
?>
<br>
<a href="../Ecom"><- Go Back</a>
