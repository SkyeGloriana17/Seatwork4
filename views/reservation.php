<?php
session_start();
$message = "";

// DB CONNECTION
$conn = new mysqli("localhost", "root", "", "hotel_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CLEAR BUTTON
if (isset($_POST['clear'])) {
    header("Location: reservation.php");
    exit;
}

// SUBMIT
if (isset($_POST['submit'])) {

    // VALIDATION
    if (
        empty($_POST['name']) ||
        empty($_POST['contact']) ||
        empty($_POST['checkin_date']) ||
        empty($_POST['checkin_time']) ||
        empty($_POST['checkout_date']) ||
        empty($_POST['checkout_time'])
    ) {
        $message = "Please complete all required information.";
    } elseif (empty($_POST['capacity'])) {
        $message = "No selected room capacity.";
    } elseif (empty($_POST['roomtype'])) {
        $message = "No selected room type.";
    } elseif (empty($_POST['payment'])) {
        $message = "No selected type of payment.";
    } else {

        // DATETIME
        $checkinDateTime  = $_POST['checkin_date'] . ' ' . $_POST['checkin_time'];
        $checkoutDateTime = $_POST['checkout_date'] . ' ' . $_POST['checkout_time'];

        $checkin  = strtotime($checkinDateTime);
        $checkout = strtotime($checkoutDateTime);

        if ($checkout <= $checkin) {
            $message = "Check-out must be after check-in.";
        } else {

            // DAYS
            $days = ceil(($checkout - $checkin) / (60 * 60 * 24));

            $capacity = $_POST['capacity'];
            $roomtype = $_POST['roomtype'];
            $payment  = $_POST['payment'];

            // 🔥 GET RATE FROM DATABASE (instead of array)
            $stmt = $conn->prepare("SELECT id, rate FROM rooms WHERE capacity=? AND type=?");
            $stmt->bind_param("ss", $capacity, $roomtype);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $message = "Room not found.";
            } else {
                $room = $result->fetch_assoc();
                $room_id = $room['id'];
                $ratePerDay = $room['rate'];

                // COMPUTE
                $subtotal = $ratePerDay * $days;
                $adjustment = 0;

                // PAYMENT RULES
                if ($payment === "Cash") {
                    if ($days >= 3 && $days <= 5) {
                        $adjustment = -($subtotal * 0.10);
                    } elseif ($days >= 6) {
                        $adjustment = -($subtotal * 0.15);
                    }
                } elseif ($payment === "Check") {
                    $adjustment = $subtotal * 0.05;
                } elseif ($payment === "Credit Card") {
                    $adjustment = $subtotal * 0.10;
                }

                $totalBill = $subtotal + $adjustment;

                // 🔥 INSERT INTO DATABASE
                $stmt = $conn->prepare("
                    INSERT INTO reservations 
                    (user_id, room_id, check_in, check_out, days, payment_type, total_amount)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                // if no login system yet → set user_id = NULL
                $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

                $stmt->bind_param(
                    "iissisd",
                    $user_id,
                    $room_id,
                    $checkinDateTime,
                    $checkoutDateTime,
                    $days,
                    $payment,
                    $totalBill
                );

                $stmt->execute();

                // STORE SESSION FOR BILLING PAGE
                $_SESSION['billing'] = [
                    "name" => $_POST['name'],
                    "contact" => $_POST['contact'],
                    "capacity" => $capacity,
                    "roomtype" => $roomtype,
                    "payment" => $payment,
                    "days" => $days,
                    "rate" => $ratePerDay,
                    "subtotal" => $subtotal,
                    "adjustment" => $adjustment,
                    "total" => $totalBill,
                    "checkin" => $checkinDateTime,
                    "checkout" => $checkoutDateTime
                ];

                header("Location: reservationBill.php");
                exit;
            }
        }
    }
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

    <form method="POST">
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