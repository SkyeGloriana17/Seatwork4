<?php
session_start();

if (!isset($_SESSION['reset_username'])) {
    header('Location: forgot_password.php');
    exit();
}

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/style4.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
</style>
</head>
<body>

<nav>
<div class="headerTitle">
Comfort Zone Hotel
</div>
    <a href="../index.php">HOME</a>
    <a href="profile.php">COMPANY PROFILE</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="reservation.php">RESERVATION</a>
    <?php endif; ?>
    <a href="contact.php">CONTACTS</a>
</nav>

<div class="container">
    <h1>Reset Password</h1>
    <p>Enter your new password</p>

    <form method="POST" action="../controllers/resetPasswordController.php">
        <label>New Password:</label>
        <div style="position: relative;">
            <input type="password" name="new_password" id="new_password" required>
            <button type="button" onclick="togglePassword('new_password')" style="position:absolute; right:-10px; top:0px;">
                Show
            </button>
        </div>
        <br><br>

        <label>Confirm Password:</label>
        <div style="position: relative;">
            <input type="password" name="confirm_password" id="confirm_password" required>
            <button type="button" onclick="togglePassword('confirm_password')" style="position:absolute; right:-10px; top:0px;">
                Show
            </button>
        </div>
        <br>
        <button type="submit" name="submit">Reset Password</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        <a href="login.php">Back to Login</a>
    </p>

    <div class="message"><?= htmlspecialchars($message) ?></div>
</div>

<script>
function togglePassword(fieldId) {
    var pass = document.getElementById(fieldId);
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
