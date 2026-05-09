<?php

session_start();

include "../config/db.php";
include "../models/reservationModel.php";

$model = new ReservationModel($conn);

$message = "";

// CLEAR BUTTON
if (isset($_POST['clear'])) {
    header("Location: ../views/reservation.php");
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

        $_SESSION['message'] = "Please complete all required information.";

        header("Location: ../views/reservation.php");
        exit;
    }

    elseif (empty($_POST['capacity'])) {

        $_SESSION['message'] = "No selected room capacity.";

        header("Location: ../views/reservation.php");
        exit;
    }

    elseif (empty($_POST['roomtype'])) {

        $_SESSION['message'] = "No selected room type.";

        header("Location: ../views/reservation.php");
        exit;
    }

    elseif (empty($_POST['payment'])) {

        $_SESSION['message'] = "No selected type of payment.";

        header("Location: ../views/reservation.php");
        exit;
    }

    else {

        // DATETIME
        $checkinDateTime  = $_POST['checkin_date'] . ' ' . $_POST['checkin_time'];
        $checkoutDateTime = $_POST['checkout_date'] . ' ' . $_POST['checkout_time'];

        $checkin  = strtotime($checkinDateTime);
        $checkout = strtotime($checkoutDateTime);

        if ($checkout <= $checkin) {

            $_SESSION['message'] = "Check-out must be after check-in.";

            header("Location: ../views/reservation.php");
            exit;
        }

        // DAYS
        $days = ceil(($checkout - $checkin) / (60 * 60 * 24));

        $capacity = $_POST['capacity'];
        $roomtype = $_POST['roomtype'];
        $payment  = $_POST['payment'];

        // GET ROOM
        $result = $model->getRoom($capacity, $roomtype);

        if ($result->num_rows == 0) {

            $_SESSION['message'] = "Room not found.";

            header("Location: ../views/reservation.php");
            exit;
        }

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
            }

            elseif ($days >= 6) {
                $adjustment = -($subtotal * 0.15);
            }
        }

        elseif ($payment === "Check") {
            $adjustment = $subtotal * 0.05;
        }

        elseif ($payment === "Credit Card") {
            $adjustment = $subtotal * 0.10;
        }

        $totalBill = $subtotal + $adjustment;

        // USER ID
        $user_id = isset($_SESSION['user_id'])
            ? $_SESSION['user_id']
            : NULL;

        // SAVE TO DATABASE
        $model->saveReservation(
            $user_id,
            $room_id,
            $checkinDateTime,
            $checkoutDateTime,
            $days,
            $payment,
            $totalBill
        );

        // STORE BILLING SESSION
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

        header("Location: ../views/reservationBill.php");
        exit;
    }
}

?>