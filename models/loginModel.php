<?php

class LoginModel {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CHECK USER LOGIN
    public function loginUser($username, $password) {

        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE username=? AND password=?"
        );

        $stmt->bind_param("ss", $username, $password);

        $stmt->execute();

        return $stmt->get_result();
    }
}

?>