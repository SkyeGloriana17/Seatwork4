<?php
session_start();

// Redirect if no billing session
if (!isset($_SESSION['billing'])) {
    header("Location: reservation.php");
    exit;
}

$b = $_SESSION['billing'];

/* SAFETY DEFAULTS (prevents undefined errors) */
$b['subtotal'] = $b['subtotal'] ?? 0;
$b['adjustment'] = $b['adjustment'] ?? 0;
$b['days'] = $b['days'] ?? 1;

/* FORMAT DATES */
$reservedDate = date("F d, Y", strtotime($b['checkin']));
$reservedTime = date("h:i A", strtotime($b['checkin']));
$checkoutDate = date("F d, Y", strtotime($b['checkout']));
$checkoutTime = date("h:i A", strtotime($b['checkout']));

/* ADJUSTMENT DETAILS */
$adjustmentText = "No additional charge.";
$adjustmentPercent = "0%";

if ($b['subtotal'] > 0 && $b['adjustment'] != 0) {

    $percent = abs(($b['adjustment'] / $b['subtotal']) * 100);

    if ($b['adjustment'] < 0) {
        $adjustmentPercent = number_format($percent, 0) . "% Discount";

        if ($b['days'] >= 6) {
            $adjustmentText = "15% discount for 6 days or more (Cash payment).";
        } elseif ($b['days'] >= 3) {
            $adjustmentText = "10% discount for 3–5 days (Cash payment).";
        }
    } else {
        $adjustmentPercent = number_format($percent, 0) . "% Additional Charge";

        if ($b['payment'] === "Check") {
            $adjustmentText = "5% additional charge for Check payment.";
        } elseif ($b['payment'] === "Credit Card") {
            $adjustmentText = "10% additional charge for Credit Card payment.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation Billing Information</title>
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
    <a href="login.php">LOGIN</a>
</nav>

<div class="container">
    <h1>Reservation Billing Information</h1>

    <p><strong>Name:</strong> <?= htmlspecialchars($b['name']) ?></p>
    <p><strong>Contact Number:</strong> <?= htmlspecialchars($b['contact']) ?></p>

    <p><strong>Room Capacity:</strong> <?= htmlspecialchars($b['capacity']) ?></p>
    <p><strong>Room Type:</strong> <?= htmlspecialchars($b['roomtype']) ?></p>
    <p><strong>Payment Type:</strong> <?= htmlspecialchars($b['payment']) ?></p>

    <hr>

    <p><strong>Check-in Date:</strong> <?= $reservedDate ?></p>
    <p><strong>Check-in Time:</strong> <?= $reservedTime ?></p>
    <p><strong>Check-out Date:</strong> <?= $checkoutDate ?></p>
    <p><strong>Check-out Time:</strong> <?= $checkoutTime ?></p>

    <hr>

    <p><strong>Number of Days:</strong> <?= $b['days'] ?></p>
    <p><strong>Rate per Day:</strong> ₱<?= number_format($b['rate'], 2) ?></p>
    <p><strong>Subtotal:</strong> ₱<?= number_format($b['subtotal'], 2) ?></p>

    <p>
        <strong><?= $adjustmentPercent ?>:</strong><br>
        <?= $adjustmentText ?><br>
        Amount: ₱<?= number_format($b['adjustment'], 2) ?>
    </p>

    <hr>

    <h2>Total Bill: ₱<?= number_format($b['total'], 2) ?></h2>

    <br>

    <div style="text-align:center;">
        <button onclick="window.print()">🖨 Print Receipt</button>
        <br><br>
        <a href="reservation.php">
            <button type="button">⬅ Back to Reservation</button>
        </a>
    </div>
</div>

</body>
</html>