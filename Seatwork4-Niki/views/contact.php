<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Contacts</title>
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
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin.php">ADMIN</a>
        <?php endif; ?>
        <form method="POST" action="../controllers/logoutController.php" style="display:inline;">
            <button type="submit" class="nav-button" onclick="return confirm('Do you want to log out?')">LOGOUT</button>
        </form>
    <?php endif; ?>
</nav>

<div class="container">
    <h1>Contact Us</h1>
    <p>Comfort Zone Hotel cares about your experience in any of our establishments. If you have any concerns or 
        issues, you can contact us through the following:</p>
    <p>Email: officialcomfortzonestay@email.com</p>
    <p>Phone: 0912-345-6789</p>
</div>

</body>
</html>
