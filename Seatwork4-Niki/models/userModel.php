<?php
require_once __DIR__ . '/database.php';

class UserModel {
    private const DEFAULT_ADMIN_FULL_NAME = 'System Administrator';
    private const DEFAULT_ADMIN_USERNAME = 'admin';
    private const DEFAULT_ADMIN_PASSWORD = 'admin123';
    private const DEFAULT_ADMIN_SECURITY_QUESTION = 'What is your favorite hotel service?';
    private const DEFAULT_ADMIN_SECURITY_ANSWER = 'front desk';

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
        $this->ensureSecurityQuestionColumns();
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    private function ensureSecurityQuestionColumns(): void {
        $databaseName = (string) $this->pdo->query('SELECT DATABASE()')->fetchColumn();
        $stmt = $this->pdo->prepare(
            'SELECT COLUMN_NAME
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME IN (?, ?)'
        );
        $stmt->execute([$databaseName, 'users', 'security_question', 'security_answer']);
        $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!in_array('security_question', $existingColumns, true)) {
            $this->pdo->exec('ALTER TABLE users ADD security_question VARCHAR(255) NULL');
        }

        if (!in_array('security_answer', $existingColumns, true)) {
            $this->pdo->exec('ALTER TABLE users ADD security_answer VARCHAR(255) NULL');
        }
    }

    private function normalizeSecurityAnswer(string $answer): string {
        return strtolower(trim(preg_replace('/\s+/', ' ', $answer)));
    }

    public function verifySecurityAnswer(string $username, string $securityQuestion, string $securityAnswer): ?array {
        $user = $this->findByUsername($username);

        if (!$user || empty($user['security_question']) || empty($user['security_answer'])) {
            return null;
        }

        if ($user['security_question'] !== $securityQuestion) {
            return null;
        }

        if ($user['security_answer'] !== $this->normalizeSecurityAnswer($securityAnswer)) {
            return null;
        }

        return $user;
    }

    public function ensureDefaultAdminAccount(): void {
        $admin = $this->findByUsername(self::DEFAULT_ADMIN_USERNAME);
        $securityAnswer = $this->normalizeSecurityAnswer(self::DEFAULT_ADMIN_SECURITY_ANSWER);

        if (!$admin) {
            $passwordHash = password_hash(self::DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare('INSERT INTO users (full_name, username, password, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                self::DEFAULT_ADMIN_FULL_NAME,
                self::DEFAULT_ADMIN_USERNAME,
                $passwordHash,
                'admin',
                self::DEFAULT_ADMIN_SECURITY_QUESTION,
                $securityAnswer,
            ]);
            return;
        }

        if (
            ($admin['role'] ?? '') !== 'admin' ||
            !password_verify(self::DEFAULT_ADMIN_PASSWORD, $admin['password']) ||
            empty($admin['security_question']) ||
            empty($admin['security_answer'])
        ) {
            $passwordHash = password_hash(self::DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare('UPDATE users SET full_name = ?, password = ?, role = ?, security_question = ?, security_answer = ? WHERE id = ?');
            $stmt->execute([
                self::DEFAULT_ADMIN_FULL_NAME,
                $passwordHash,
                'admin',
                self::DEFAULT_ADMIN_SECURITY_QUESTION,
                $securityAnswer,
                $admin['id'],
            ]);
        }
    }

    public function createUser(string $fullName, string $username, string $password, string $securityQuestion, string $securityAnswer): bool {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $securityAnswer = $this->normalizeSecurityAnswer($securityAnswer);
        $stmt = $this->pdo->prepare('INSERT INTO users (full_name, username, password, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$fullName, $username, $passwordHash, 'guest', $securityQuestion, $securityAnswer]);
    }

    public function createAdminUser(string $fullName, string $username, string $password, string $securityQuestion, string $securityAnswer): bool {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $securityAnswer = $this->normalizeSecurityAnswer($securityAnswer);
        $stmt = $this->pdo->prepare('INSERT INTO users (full_name, username, password, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$fullName, $username, $passwordHash, 'admin', $securityQuestion, $securityAnswer]);
    }

    public function authenticate(string $username, string $password): ?array {
        $user = $this->findByUsername($username);
        if (!$user) {
            return null;
        }

        $storedPassword = $user['password'];
        if (password_verify($password, $storedPassword)) {
            return $user;
        }

        if ($storedPassword === $password) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $update->execute([$passwordHash, $user['id']]);
            return $user;
        }

        return null;
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([$passwordHash, $userId]);
    }

    public function updateSecurityQuestion(int $userId, string $securityQuestion, string $securityAnswer): bool {
        $stmt = $this->pdo->prepare('UPDATE users SET security_question = ?, security_answer = ? WHERE id = ?');
        return $stmt->execute([$securityQuestion, $this->normalizeSecurityAnswer($securityAnswer), $userId]);
    }
}
