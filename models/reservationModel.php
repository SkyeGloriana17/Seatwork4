<?php

class ReservationModel {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET ROOM INFO
    public function getRoom($capacity, $roomtype) {

        $stmt = $this->conn->prepare(
            "SELECT id, rate FROM rooms WHERE capacity=? AND type=?"
        );

        $stmt->bind_param("ss", $capacity, $roomtype);

        $stmt->execute();

        return $stmt->get_result();
    }

    // INSERT RESERVATION
    public function saveReservation(
        $user_id,
        $room_id,
        $checkin,
        $checkout,
        $days,
        $payment,
        $totalBill
    ) {

        $stmt = $this->conn->prepare("
            INSERT INTO reservations
            (user_id, room_id, check_in, check_out, days, payment_type, total_amount)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iissisd",
            $user_id,
            $room_id,
            $checkin,
            $checkout,
            $days,
            $payment,
            $totalBill
        );

        return $stmt->execute();
    }
}

?>