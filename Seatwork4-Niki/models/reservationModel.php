<?php
require_once __DIR__ . '/database.php';

class ReservationModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function findRoom(string $capacity, string $type): ?array {
        $stmt = $this->pdo->prepare('SELECT id, rate FROM rooms WHERE capacity = ? AND type = ? LIMIT 1');
        $stmt->execute([$capacity, $type]);
        $room = $stmt->fetch();
        return $room ?: null;
    }

    public function createReservation(?int $userId, int $roomId, string $checkIn, string $checkOut, int $days, string $paymentType, float $totalAmount): bool {
        $stmt = $this->pdo->prepare('INSERT INTO reservations (user_id, room_id, check_in, check_out, days, payment_type, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$userId, $roomId, $checkIn, $checkOut, $days, $paymentType, $totalAmount]);
    }

    public function getAllRooms(): array {
        $stmt = $this->pdo->query('SELECT * FROM rooms ORDER BY capacity, type');
        return $stmt->fetchAll();
    }

    public function getAllReservationsWithRooms(): array {
        $stmt = $this->pdo->query(
            'SELECT r.*, rm.capacity, rm.type
             FROM reservations r
             JOIN rooms rm ON r.room_id = rm.id
             ORDER BY r.id DESC'
        );
        return $stmt->fetchAll();
    }

    public function findReservationById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM reservations WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $reservation = $stmt->fetch();
        return $reservation ?: null;
    }

    public function getTotalReservations(): int {
        $stmt = $this->pdo->query('SELECT COUNT(*) AS total FROM reservations');
        return (int) $stmt->fetch()['total'];
    }

    public function getTotalRevenue(): float {
        $stmt = $this->pdo->query('SELECT COALESCE(SUM(total_amount), 0) AS total FROM reservations');
        return (float) $stmt->fetch()['total'];
    }

    public function updateReservation(int $id, int $roomId, string $checkIn, string $checkOut, int $days, string $paymentType, float $totalAmount): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE reservations
             SET room_id = ?, check_in = ?, check_out = ?, days = ?, payment_type = ?, total_amount = ?
             WHERE id = ?'
        );
        return $stmt->execute([$roomId, $checkIn, $checkOut, $days, $paymentType, $totalAmount, $id]);
    }

    public function deleteReservation(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM reservations WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
