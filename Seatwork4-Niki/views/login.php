<?php
session_start();
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/style4.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
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
    <h1>Login</h1>

    <form method="POST" action="../controllers/loginController.php">
        <label>Username:</label>
        <input type="text" name="username" required>
        <br><br>

        <label>Password:</label>
        <div style="position: relative;">
            <input type="password" name="password" id="password" required>
            <button type="button" onclick="togglePassword()" 
                style="position:absolute; right:-10px; top:0px;"  >
                Show
            </button>
        </div>
        <br>
        <button type="submit" name="login">Login</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        <a href="signup.php">Create an account</a> | <a href="forgot_password.php">Forgot Password?</a>
    </p>

    <div class="message"><?= htmlspecialchars($message) ?></div>
</div>

<script>
function togglePassword() {
    var pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
