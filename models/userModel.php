<?php

class UserModel {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CHECK IF USER EXISTS
    public function userExists($username) {

        $stmt = $this->conn->prepare(
            "SELECT id FROM users WHERE username = ?"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result();
    }

    // CREATE NEW USER (CLIENT ONLY)
    public function createUser($username, $password) {

        $role = "guest";

        $stmt = $this->conn->prepare(
            "INSERT INTO users (username, password, role)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param("sss", $username, $password, $role);

        return $stmt->execute();
    }

    // GET RESERVATIONS OF LOGGED IN GUEST
    public function getReservations($user_id) {

    $stmt = $this->conn->prepare(
        "SELECT r.*, rm.capacity, rm.type
         FROM reservations r
         JOIN rooms rm ON r.room_id = rm.id
         WHERE r.user_id = ?
         ORDER BY r.id DESC"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    return $stmt->get_result();
}
}

?>