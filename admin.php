<?php
session_start();

//  ACCESS CONTROL
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

//  DB CONNECTION
$conn = new mysqli("localhost", "root", "", "hotel_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

//  HANDLE DELETE
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error deleting: " . $conn->error;
    }
}

//  HANDLE UPDATE (EDIT)
if (isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $payment_type = $_POST['payment_type'];
    $room_id = intval($_POST['room_id']);

    // Calculate days
    $days = ceil((strtotime($check_out) - strtotime($check_in)) / (60*60*24));

    // Get room rate
    $room = $conn->query("SELECT rate FROM rooms WHERE id = $room_id")->fetch_assoc();
    $rate = $room['rate'];

    $total_amount = $rate * $days;

    // Payment fees
    if ($payment_type == 'Check') $total_amount *= 1.05;
    if ($payment_type == 'Credit Card') $total_amount *= 1.10;

    // Discounts
    if ($days >= 3 && $days <= 5) $total_amount *= 0.9;
    if ($days >= 6) $total_amount *= 0.85;

    // Update DB
    $stmt = $conn->prepare("UPDATE reservations SET room_id=?, check_in=?, check_out=?, days=?, payment_type=?, total_amount=? WHERE id=?");
    $stmt->bind_param("issisdi", $room_id, $check_in, $check_out, $days, $payment_type, $total_amount, $id);
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error updating: " . $conn->error;
    }
}

//  FETCH DATA
$rooms = $conn->query("SELECT * FROM rooms ORDER BY capacity, type");
$reservations = $conn->query("
    SELECT r.*, rm.capacity, rm.type 
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.id
    ORDER BY r.id DESC
");

//  SIMPLE STATS
$totalReservations = $conn->query("SELECT COUNT(*) AS total FROM reservations")->fetch_assoc()['total'];
$totalRevenue = $conn->query("SELECT SUM(total_amount) AS total FROM reservations")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style4.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');
</style>
</head>

<body>

<nav>
<div class="headerTitle">Comfort Zone Hotel</div>
    <a href="index.php">HOME</a>
    <a href="profile.php">COMPANY PROFILE</a>
    <a href="reservation.php">RESERVATION</a>
    <a href="contact.php">CONTACTS</a>
    <a href="admin.php">ADMIN</a>
</nav>

<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> 👑</p>

    <!-- STATS -->
    <h2>System Overview</h2>
    <p>Total Reservations: <strong><?= $totalReservations ?></strong></p>
    <p>Total Revenue: <strong>₱<?= number_format($totalRevenue, 2) ?></strong></p>

    <hr>

    <!-- ROOM RATES -->
    <h2>Room Rates (Database)</h2>
    <table width="100%" cellpadding="10">
        <tr><th>Capacity</th><th>Type</th><th>Rate</th></tr>
        <?php while ($row = $rooms->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['capacity']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td>₱<?= number_format($row['rate'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <hr>

    <!-- RESERVATIONS -->
    <h2>All Reservations</h2>

    <?php if (isset($_GET['edit_id'])):
        $edit_id = intval($_GET['edit_id']);
        $editData = $conn->query("SELECT * FROM reservations WHERE id = $edit_id")->fetch_assoc();
    ?>
    <h3>Edit Reservation #<?= $edit_id ?></h3>
    <form method="POST">
        <input type="hidden" name="update_id" value="<?= $edit_id ?>">

        <label>Room:</label>
        <select name="room_id" required>
            <?php
            $roomList = $conn->query("SELECT * FROM rooms ORDER BY capacity, type");
            while ($rm = $roomList->fetch_assoc()):
            ?>
            <option value="<?= $rm['id'] ?>" <?= ($rm['id']==$editData['room_id'])?'selected':'' ?>>
                Capacity: <?= $rm['capacity'] ?>, Type: <?= $rm['type'] ?>
            </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Check-in:</label>
        <input type="date" name="check_in" value="<?= $editData['check_in'] ?>" required>
        <br><br>

        <label>Check-out:</label>
        <input type="date" name="check_out" value="<?= $editData['check_out'] ?>" required>
        <br><br>

        <label>Payment Type:</label>
        <select name="payment_type">
            <option value="Cash" <?= $editData['payment_type']=='Cash'?'selected':'' ?>>Cash</option>
            <option value="Check" <?= $editData['payment_type']=='Check'?'selected':'' ?>>Check</option>
            <option value="Credit Card" <?= $editData['payment_type']=='Credit Card'?'selected':'' ?>>Credit Card</option>
        </select>
        <br><br>

        <button type="submit">Update Reservation</button>
    </form>
    <hr>
    <?php endif; ?>

    <table width="100%" cellpadding="10">
        <tr><th>ID</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Days</th><th>Payment</th><th>Total</th><th>Actions</th></tr>
        <?php while ($r = $reservations->fetch_assoc()): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['capacity']) ?> - <?= htmlspecialchars($r['type']) ?></td>
            <td><?= $r['check_in'] ?></td>
            <td><?= $r['check_out'] ?></td>
            <td><?= $r['days'] ?></td>
            <td><?= htmlspecialchars($r['payment_type']) ?></td>
            <td>₱<?= number_format($r['total_amount'], 2) ?></td>
            <td>
                <a href="admin.php?edit_id=<?= $r['id']; ?>">Edit</a> 
                <a href="admin.php?delete_id=<?= $r['id']; ?>" onclick="return confirm('Delete this reservation?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <hr>

    <h2>Payment Rules</h2>
    <ul>
        <li>Cash – No additional charge</li>
        <li>Check – +5%</li>
        <li>Credit Card – +10%</li>
    </ul>

    <h2>Discount Rules</h2>
    <ul>
        <li>10% discount (3–5 days stay)</li>
        <li>15% discount (6 days and above)</li>
    </ul>

    <br>
    <h2>Quick Actions</h2>
    <button onclick="location.href='reservation.php'">New Reservation</button>
    <button onclick="location.href='logout.php'">Logout</button>

</div>
</body>
</html>