<?php
session_start();

$message = "";

if(isset($_SESSION['message'])) {

    $message = $_SESSION['message'];

    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation</title>
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
    <a href="index.php">HOME</a>
    <a href="profile.php">COMPANY PROFILE</a>
    <a href="reservation.php">RESERVATION</a>
    <a href="contact.php">CONTACTS</a>
</nav>

<div class="container">
    <h1>Reservation Form</h1>

    <form action="../controllers/reservationController.php" method ="POST">
        <input type="text" name="name" placeholder="Full Name">
        <input type="tel" name="contact" placeholder="Contact Number">

        <label>Check-in Date</label>
        <input type="date" name="checkin_date">

        <label>Check-in Time</label>
        <input type="time" name="checkin_time"> 

        <label>Check-out Date</label>
        <input type="date" name="checkout_date">

        <label>Check-out Time</label>
        <input type="time" name="checkout_time">

        <select name="capacity">
            <option value="">Select Room Capacity</option>
            <option value="Single">Single</option>
            <option value="Double">Double</option>
            <option value="Family">Family</option>
        </select>

        <select name="roomtype">
            <option value="">Select Room Type</option>
            <option value="Regular">Regular</option>
            <option value="De Luxe">De Luxe</option>
            <option value="Suite">Suite</option>
        </select>

        <select name="payment">
            <option value="">Select Payment Type</option>
            <option value="Cash">Cash</option>
            <option value="Check">Check</option>
            <option value="Credit Card">Credit Card</option>
        </select>

        <button type="submit" name="submit">Submit Reservation</button>
        <button type="submit" name="clear">Clear Entry</button>
    </form>

    <div class="message"><?= $message ?></div>
</div>

</body>
</html>