<?php
session_start();
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
    <a href="profile.php">COMPANY PROFILE</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="reservation.php">RESERVATION</a>
    <?php endif; ?>
    <a href="contact.php">CONTACTS</a>
</nav>

<div class="container">
    <h1>Register</h1>

    <form method="POST" action="../controllers/signupController.php">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>

        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <div style="position: relative;">
            <input type="password" name="password" id="password" required>
            <button type="button" onclick="togglePassword('password')" style="position:absolute; right:-10px; top:0px;">
                Show
            </button>
        </div>

        <label>Confirm Password:</label>
        <div style="position: relative;">
            <input type="password" name="confirm_password" id="confirm_password" required>
            <button type="button" onclick="togglePassword('confirm_password')" style="position:absolute; right:-10px; top:0px;">
                Show
            </button>
        </div>

        <label>Security Question:</label>
        <select name="security_question" required>
            <option value="">Select Security Question</option>
            <option value="What is your favorite hotel service?">What is your favorite hotel service?</option>
            <option value="What city were you born in?">What city were you born in?</option>
            <option value="What is the name of your first school?">What is the name of your first school?</option>
        </select>

        <label>Security Answer:</label>
        <input type="password" name="security_answer" required>

        <input type="hidden" name="admin_code" value="">

        <button type="submit" name="signup">Sign Up</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        <a href="login.php">Already have an account? Login</a>
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
