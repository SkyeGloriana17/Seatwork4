<?php
session_start();

require_once __DIR__ . '/../models/reservationModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/reservation.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$checkinDate = $_POST['checkin_date'] ?? '';
$checkinTime = $_POST['checkin_time'] ?? '';
$checkoutDate = $_POST['checkout_date'] ?? '';
$checkoutTime = $_POST['checkout_time'] ?? '';
$capacity = $_POST['capacity'] ?? '';
$roomtype = $_POST['roomtype'] ?? '';
$payment = $_POST['payment'] ?? '';

if ($name === '' || $contact === '' || $checkinDate === '' || $checkinTime === '' || $checkoutDate === '' || $checkoutTime === '') {
    $_SESSION['message'] = 'Please complete all required information.';
    header('Location: ../views/reservation.php');
    exit();
}

if ($capacity === '' || $roomtype === '' || $payment === '') {
    $_SESSION['message'] = 'Please select room capacity, room type, and payment type.';
    header('Location: ../views/reservation.php');
    exit();
}

$checkinDateTime = "$checkinDate $checkinTime";
$checkoutDateTime = "$checkoutDate $checkoutTime";
$checkin = strtotime($checkinDateTime);
$checkout = strtotime($checkoutDateTime);

if ($checkout <= $checkin) {
    $_SESSION['message'] = 'Check-out must be after check-in.';
    header('Location: ../views/reservation.php');
    exit();
}

$days = ceil(($checkout - $checkin) / (60 * 60 * 24));
$reservationModel = new ReservationModel();
$room = $reservationModel->findRoom($capacity, $roomtype);

if (!$room) {
    $_SESSION['message'] = 'Room not found.';
    header('Location: ../views/reservation.php');
    exit();
}

$ratePerDay = $room['rate'];
$subtotal = $ratePerDay * $days;
$adjustment = 0;

if ($payment === 'Cash') {
    if ($days >= 3 && $days <= 5) {
        $adjustment = -($subtotal * 0.10);
    } elseif ($days >= 6) {
        $adjustment = -($subtotal * 0.15);
    }
} elseif ($payment === 'Check') {
    $adjustment = $subtotal * 0.05;
} elseif ($payment === 'Credit Card') {
    $adjustment = $subtotal * 0.10;
}

$total = $subtotal + $adjustment;

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$reservationModel->createReservation($userId, $room['id'], $checkinDateTime, $checkoutDateTime, $days, $payment, $total);

$_SESSION['billing'] = [
    'name' => $name,
    'contact' => $contact,
    'capacity' => $capacity,
    'roomtype' => $roomtype,
    'payment' => $payment,
    'days' => $days,
    'rate' => $ratePerDay,
    'subtotal' => $subtotal,
    'adjustment' => $adjustment,
    'total' => $total,
    'checkin' => $checkinDateTime,
    'checkout' => $checkoutDateTime,
];

header('Location: ../views/reservationBill.php');
exit();
