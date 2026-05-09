<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="assets/style4.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
</style>
</head>
<body>

<nav>
<div class="headerTitle">
Comfort Zone Hotel
</div>
    <a href="index.php">HOME</a>
    <a href="views/profile.php">COMPANY PROFILE</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="views/reservation.php">RESERVATION</a>
    <?php endif; ?>
    <a href="views/contact.php">CONTACTS</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="views/admin.php">ADMIN</a>
        <?php endif; ?>
        <form method="POST" action="controllers/logoutController.php" style="display:inline;">
            <button type="submit" class="nav-button" onclick="return confirm('Do you want to log out?')">LOGOUT</button>
        </form>
    <?php endif; ?>
</nav>

<div class="container">
    <h1>Welcome to Comfort Zone Hotel</h1>
   <center> <p>Your comfort is our priority. Book your stay easily and hassle-free.</p> </center>
   
   <?php if (!isset($_SESSION['user_id'])): ?>
   <a href="views/login.php">
   <center> <button type="button" style="border-radius: 15px;"> Login </button></center> </a>
   <?php endif; ?>
   
   <a href="views/reservation.php">
                  <center> <button type="button" style="border-radius:15px;">Book a reservation now!</button> </center>
   </a>
</div>

</body>
</html>
