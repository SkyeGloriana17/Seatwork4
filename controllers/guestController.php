<?php

require_once "../config/db.php";

// Prevent access if not logged in
if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

// Current logged in user
$user_id = $_SESSION['user_id'];

// SQL query
$sql = "
    SELECT
        reservations.id,
        reservations.check_in,
        reservations.check_out,
        reservations.days,
        reservations.payment_type,
        reservations.total_amount,

        rooms.capacity,
        rooms.type

    FROM reservations

    INNER JOIN rooms
        ON reservations.room_id = rooms.id

    WHERE reservations.user_id = ?
";

// Prepare statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind user id
$stmt->bind_param("i", $user_id);

// Execute
$stmt->execute();

// Get results
$reservations = $stmt->get_result();
?>