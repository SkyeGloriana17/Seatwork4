<?php
session_start();
require_once __DIR__ . '/../controllers/adminController.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style4.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
</style>
</head>

<body>

<nav>
<div class="headerTitle">Comfort Zone Hotel</div>
    <a href="../index.php">HOME</a>
    <a href="profile.php">COMPANY PROFILE</a>
    <a href="reservation.php">RESERVATION</a>
    <a href="contact.php">CONTACTS</a>
    <a href="admin.php">ADMIN</a>
    <form method="POST" action="../controllers/logoutController.php" style="display:inline;">
        <button type="submit" class="nav-button" onclick="return confirm('Do you want to log out?')">LOGOUT</button>
    </form>
</nav>

<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
    <?php if ($adminMessage !== ''): ?>
        <div class="message"><?= htmlspecialchars($adminMessage) ?></div>
    <?php endif; ?>

    <h2>System Overview</h2>
    <p>Total Reservations: <strong><?= $totalReservations ?></strong></p>
    <p>Total Revenue: <strong>PHP <?= number_format($totalRevenue, 2) ?></strong></p>

    <hr>

    <h2>Admin Security Question</h2>
    <form method="POST">
        <label>Security Question:</label>
        <select name="security_question" required>
            <option value="">Select Security Question</option>
            <option value="What is your favorite hotel service?">What is your favorite hotel service?</option>
            <option value="What city were you born in?">What city were you born in?</option>
            <option value="What is the name of your first school?">What is the name of your first school?</option>
        </select>

        <label>Security Answer:</label>
        <input type="password" name="security_answer" required>

        <button type="submit" name="update_security_question">Update Security Question</button>
    </form>

    <hr>

    <h2>Room Rates</h2>
    <table width="100%" cellpadding="10">
        <tr><th>Capacity</th><th>Type</th><th>Rate</th></tr>
        <?php foreach ($rooms as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['capacity']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td>PHP <?= number_format($row['rate'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <hr>

    <h2>All Reservations</h2>

    <?php if ($editData): ?>
    <h3>Edit Reservation #<?= $editId ?></h3>
    <form method="POST">
        <input type="hidden" name="update_id" value="<?= $editId ?>">

        <label>Room:</label>
        <select name="room_id" required>
            <?php foreach ($rooms as $rm): ?>
            <option value="<?= $rm['id'] ?>" <?= ((int) $rm['id'] === (int) $editData['room_id']) ? 'selected' : '' ?>>
                Capacity: <?= htmlspecialchars($rm['capacity']) ?>, Type: <?= htmlspecialchars($rm['type']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Check-in:</label>
        <input type="datetime-local" name="check_in" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($editData['check_in']))) ?>" required>
        <br><br>

        <label>Check-out:</label>
        <input type="datetime-local" name="check_out" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($editData['check_out']))) ?>" required>
        <br><br>

        <label>Payment Type:</label>
        <select name="payment_type" required>
            <option value="Cash" <?= $editData['payment_type'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
            <option value="Check" <?= $editData['payment_type'] === 'Check' ? 'selected' : '' ?>>Check</option>
            <option value="Credit Card" <?= $editData['payment_type'] === 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
        </select>
        <br><br>

        <button type="submit">Update Reservation</button>
        <a href="admin.php"><button type="button">Cancel</button></a>
    </form>
    <hr>
    <?php endif; ?>

    <table width="100%" cellpadding="10">
        <tr><th>ID</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Days</th><th>Payment</th><th>Total</th><th>Actions</th></tr>
        <?php foreach ($reservations as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['capacity']) ?> - <?= htmlspecialchars($r['type']) ?></td>
            <td><?= htmlspecialchars($r['check_in']) ?></td>
            <td><?= htmlspecialchars($r['check_out']) ?></td>
            <td><?= $r['days'] ?></td>
            <td><?= htmlspecialchars($r['payment_type']) ?></td>
            <td>PHP <?= number_format($r['total_amount'], 2) ?></td>
            <td>
                <a href="admin.php?edit_id=<?= $r['id'] ?>">Edit</a>
                <a href="admin.php?delete_id=<?= $r['id'] ?>" onclick="return confirm('Delete this reservation?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <hr>

    <h2>Payment Rules</h2>
    <ul>
        <li>Cash - No additional charge</li>
        <li>Check - +5%</li>
        <li>Credit Card - +10%</li>
    </ul>

    <h2>Discount Rules</h2>
    <ul>
        <li>10% discount for 3-5 days stay</li>
        <li>15% discount for 6 days and above</li>
    </ul>

    <br>
    <h2>Quick Actions</h2>
    <button onclick="location.href='reservation.php'">New Reservation</button>
    <form method="POST" action="../controllers/logoutController.php" style="display:inline;">
        <button type="submit" onclick="return confirm('Do you want to log out?')">Logout</button>
    </form>
</div>
</body>
</html>
