<?php
session_start();
if (!isset($_SESSION['billing'])) {
    header('Location: reservation.php');
    exit();
}
$b = $_SESSION['billing'];
$b['subtotal'] = $b['subtotal'] ?? 0;
$b['adjustment'] = $b['adjustment'] ?? 0;
$b['days'] = $b['days'] ?? 1;
$reservedDate = date('F d, Y', strtotime($b['checkin']));
$reservedTime = date('h:i A', strtotime($b['checkin']));
$checkoutDate = date('F d, Y', strtotime($b['checkout']));
$checkoutTime = date('h:i A', strtotime($b['checkout']));
$receiptDate = date('F d, Y h:i A');
$adjustmentText = 'No additional charge.';
$adjustmentPercent = '0%';

if ($b['subtotal'] > 0 && $b['adjustment'] != 0) {
    $percent = abs(($b['adjustment'] / $b['subtotal']) * 100);
    if ($b['adjustment'] < 0) {
        $adjustmentPercent = number_format($percent, 0) . '% Discount';
        if ($b['days'] >= 6) {
            $adjustmentText = '15% discount for 6 days or more (Cash payment).';
        } elseif ($b['days'] >= 3) {
            $adjustmentText = '10% discount for 3-5 days (Cash payment).';
        }
    } else {
        $adjustmentPercent = number_format($percent, 0) . '% Additional Charge';
        if ($b['payment'] === 'Check') {
            $adjustmentText = '5% additional charge for Check payment.';
        } elseif ($b['payment'] === 'Credit Card') {
            $adjustmentText = '10% additional charge for Credit Card payment.';
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

.receipt-header,
.receipt-footer {
    display: none;
}

.receipt-actions {
    text-align: center;
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    padding: 8px 0;
}

.receipt-row span:first-child {
    font-weight: 600;
}

.receipt-total {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    font-size: 24px;
    font-weight: 700;
    margin-top: 14px;
}

@media print {
    @page {
        margin: 16mm;
    }

    body {
        background: #ffffff !important;
        color: #000000;
        font-family: Arial, sans-serif;
    }

    nav,
    .receipt-actions {
        display: none !important;
    }

    .container {
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        padding: 0;
        background: #ffffff !important;
        border-radius: 0;
    }

    .receipt-header,
    .receipt-footer {
        display: block;
        text-align: center;
    }

    .receipt-header {
        border-bottom: 2px solid #000000;
        margin-bottom: 16px;
        padding-bottom: 12px;
    }

    .receipt-header h1 {
        margin: 0 0 6px;
        font-size: 22px;
    }

    .container > h1 {
        display: none;
    }

    p {
        font-size: 14px;
        margin: 0;
    }

    hr {
        border: 0;
        border-top: 1px solid #000000;
        margin: 14px 0;
    }

    .receipt-row,
    .receipt-total {
        font-size: 14px;
    }

    .receipt-total {
        border-top: 2px solid #000000;
        padding-top: 10px;
    }

    .receipt-footer {
        border-top: 1px solid #000000;
        font-size: 12px;
        margin-top: 24px;
        padding-top: 10px;
    }
}
</style>
</head>
<body>

<nav>
<div class="headerTitle">Comfort Zone Hotel</div>
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
    <div class="receipt-header">
        <h1>Comfort Zone Hotel</h1>
        <p>Official Reservation Receipt</p>
        <p>Printed: <?= htmlspecialchars($receiptDate) ?></p>
    </div>

    <h1>Reservation Billing Information</h1>
    <div class="receipt-row"><span>Name</span><span><?= htmlspecialchars($b['name']) ?></span></div>
    <div class="receipt-row"><span>Contact Number</span><span><?= htmlspecialchars($b['contact']) ?></span></div>
    <div class="receipt-row"><span>Room Capacity</span><span><?= htmlspecialchars($b['capacity']) ?></span></div>
    <div class="receipt-row"><span>Room Type</span><span><?= htmlspecialchars($b['roomtype']) ?></span></div>
    <div class="receipt-row"><span>Payment Type</span><span><?= htmlspecialchars($b['payment']) ?></span></div>
    <hr>
    <div class="receipt-row"><span>Check-in Date</span><span><?= $reservedDate ?></span></div>
    <div class="receipt-row"><span>Check-in Time</span><span><?= $reservedTime ?></span></div>
    <div class="receipt-row"><span>Check-out Date</span><span><?= $checkoutDate ?></span></div>
    <div class="receipt-row"><span>Check-out Time</span><span><?= $checkoutTime ?></span></div>
    <hr>
    <div class="receipt-row"><span>Number of Days</span><span><?= $b['days'] ?></span></div>
    <div class="receipt-row"><span>Rate per Day</span><span>PHP <?= number_format($b['rate'], 2) ?></span></div>
    <div class="receipt-row"><span>Subtotal</span><span>PHP <?= number_format($b['subtotal'], 2) ?></span></div>
    <div class="receipt-row"><span><?= $adjustmentPercent ?></span><span>PHP <?= number_format($b['adjustment'], 2) ?></span></div>
    <p><?= htmlspecialchars($adjustmentText) ?></p>
    <div class="receipt-total"><span>Total Bill</span><span>PHP <?= number_format($b['total'], 2) ?></span></div>

    <div class="receipt-footer">
        <p>Thank you for choosing Comfort Zone Hotel.</p>
        <p>Please present this receipt for reservation concerns.</p>
    </div>

    <br>
    <div class="receipt-actions">
        <button onclick="window.print()">Print Receipt</button>
        <br><br>
        <a href="reservation.php"><button type="button">Back to Reservation</button></a>
    </div>
</div>

</body>
</html>
