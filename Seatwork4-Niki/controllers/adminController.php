<?php
require_once __DIR__ . '/../models/reservationModel.php';
require_once __DIR__ . '/../models/userModel.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$reservationModel = new ReservationModel();
$userModel = new UserModel();
$adminMessage = $_SESSION['admin_message'] ?? '';
unset($_SESSION['admin_message']);

if (isset($_GET['delete_id'])) {
    $reservationModel->deleteReservation((int) $_GET['delete_id']);
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = (int) $_POST['update_id'];
    $roomId = (int) $_POST['room_id'];
    $checkIn = $_POST['check_in'] ?? '';
    $checkOut = $_POST['check_out'] ?? '';
    $paymentType = $_POST['payment_type'] ?? '';
    $room = null;

    foreach ($reservationModel->getAllRooms() as $availableRoom) {
        if ((int) $availableRoom['id'] === $roomId) {
            $room = $availableRoom;
            break;
        }
    }

    $checkInTime = strtotime($checkIn);
    $checkOutTime = strtotime($checkOut);

    if ($room && $checkInTime && $checkOutTime && $checkOutTime > $checkInTime && $paymentType !== '') {
        $days = (int) ceil(($checkOutTime - $checkInTime) / (60 * 60 * 24));
        $subtotal = (float) $room['rate'] * $days;
        $adjustment = 0;

        if ($paymentType === 'Cash') {
            if ($days >= 3 && $days <= 5) {
                $adjustment = -($subtotal * 0.10);
            } elseif ($days >= 6) {
                $adjustment = -($subtotal * 0.15);
            }
        } elseif ($paymentType === 'Check') {
            $adjustment = $subtotal * 0.05;
        } elseif ($paymentType === 'Credit Card') {
            $adjustment = $subtotal * 0.10;
        }

        $reservationModel->updateReservation($id, $roomId, $checkIn, $checkOut, $days, $paymentType, $subtotal + $adjustment);
    }

    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_security_question'])) {
    $securityQuestion = trim($_POST['security_question'] ?? '');
    $securityAnswer = trim($_POST['security_answer'] ?? '');

    if ($securityQuestion === '' || $securityAnswer === '') {
        $_SESSION['admin_message'] = 'Please select a security question and enter an answer.';
    } else {
        $userModel->updateSecurityQuestion((int) $_SESSION['user_id'], $securityQuestion, $securityAnswer);
        $_SESSION['admin_message'] = 'Security question updated successfully.';
    }

    header('Location: admin.php');
    exit();
}

$rooms = $reservationModel->getAllRooms();
$reservations = $reservationModel->getAllReservationsWithRooms();
$totalReservations = $reservationModel->getTotalReservations();
$totalRevenue = $reservationModel->getTotalRevenue();
$editData = null;
$editId = isset($_GET['edit_id']) ? (int) $_GET['edit_id'] : 0;

if ($editId > 0) {
    $editData = $reservationModel->findReservationById($editId);
}
