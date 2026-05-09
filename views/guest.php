<?php

session_start();

require_once "../controllers/guestController.php";

?>

<!DOCTYPE html>
<html>

<head>

    <title>Guest Dashboard</title>

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
    <a href="guest.php">DASHBOARD</a>

</nav>

<div class="container">

    <h1>Guest Dashboard</h1>

    <p>

        Welcome,

        <strong>
            <?= htmlspecialchars($_SESSION['username']) ?>
        </strong>

        🏨

    </p>

    <hr>

    <h2>Your Reservations</h2>

    <table border="1" cellpadding="10" width="100%">

        <tr>

            <th>ID</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Days</th>
            <th>Payment</th>
            <th>Total</th>

        </tr>

        <?php if ($reservations && $reservations->num_rows > 0): ?>

            <?php while ($r = $reservations->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= $r['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['capacity']) ?>
                        -
                        <?= htmlspecialchars($r['type']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['check_in']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['check_out']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['days']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['payment_type']) ?>
                    </td>

                    <td>
                        ₱<?= number_format($r['total_amount'], 2) ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>

                <td colspan="7">
                    No reservations found.
                </td>

            </tr>

        <?php endif; ?>

    </table>

    <br>

    <button onclick="location.href='reservation.php'">
        New Reservation
    </button>

    <button onclick="location.href='index.php'">
        Logout
    </button>

</div>

</body>
</html>