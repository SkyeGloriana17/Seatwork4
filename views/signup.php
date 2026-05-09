<?php
session_start();

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="../assets/style4.css">
</head>

<body>

<nav>
    <div class="headerTitle">
        Comfort Zone Hotel
    </div>

    <a href="index.php">HOME</a>
    <a href="profile.php">COMPANY PROFILE</a>
    <a href="reservation.php">RESERVATION</a>
    <a href="contact.php">CONTACTS</a>
</nav>

<div class="container">

    <h1>Create Account</h1>

    <form action="../controllers/signUpController.php" method="POST">

        <label for="username">Username:</label>
        <input 
            type="text" 
            id="username" 
            name="username" 
            required
        >

        <br><br>

        <label for="password">Password:</label>
        <div style="position: relative;">
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
            >

            <button 
                type="button"
                onclick="togglePassword('password')"
                style="position:absolute; right:-10px; top:0px;"
            >
                👁
            </button>
        </div>

        <br>

        <label for="confirm_password">Confirm Password:</label>
        <div style="position: relative;">
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                required
            >

            <button 
                type="button"
                onclick="togglePassword('confirm_password')"
                style="position:absolute; right:-10px; top:0px;"
            >
                👁
            </button>
        </div>

        <br><br>

        <button type="submit" name="signup">
            Sign Up
        </button>

    </form>

    <div class="message">
        <?= htmlspecialchars($message) ?>
    </div>

    <br>

    <a href="login.php">
        Already have an account? Login
    </a>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);

    input.type = input.type === "password" 
        ? "text" 
        : "password";
}
</script>

</body>
</html>