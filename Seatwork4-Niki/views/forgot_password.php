<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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
    <h1>Forgot Password</h1>
    <p>Answer your security question to reset your password</p>

    <form method="POST" action="../controllers/forgotPasswordController.php">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Security Question:</label>
        <select name="security_question" required>
            <option value="">Select Security Question</option>
            <option value="What is your favorite hotel service?">What is your favorite hotel service?</option>
            <option value="What city were you born in?">What city were you born in?</option>
            <option value="What is the name of your first school?">What is the name of your first school?</option>
        </select>

        <label>Security Answer:</label>
        <input type="password" name="security_answer" required>
        <br>
        <button type="submit" name="submit">Submit</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        <a href="login.php">Back to Login</a>
    </p>

    <div class="message"><?= htmlspecialchars($_SESSION['message'] ?? '') ?></div>
</div>

<?php
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
}
?>

</body>
</html>
