<?php
// models/User.php

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /** Find user by email */
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /** Find user by ID */
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Register a new user */
    public function create(array $data): int|false {
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, name, email, password, phone, status)
            VALUES (:role_id, :name, :email, :password, :phone, :status)
        ");
        $stmt->execute([
            'role_id'  => $data['role_id'],
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone'    => $data['phone'] ?? null,
            'status'   => $data['status'] ?? 'active',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update user profile */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach (['name','email','phone','address','avatar'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = :$f";
                $params[$f] = $data[$f];
            }
        }
        if (empty($fields)) return false;
        $params['id'] = $id;
        $stmt = $this->db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    /** Change password */
    public function changePassword(int $id, string $newPassword): bool {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $id]);
    }

    /** Update status */
    public function setStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /** Get all users with optional role filter */
    public function getAll(string $role = null, int $limit = 50, int $offset = 0): array {
        $sql = "SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id";
        $params = [];
        if ($role) {
            $sql .= " WHERE r.name = ?";
            $params[] = $role;
        }
        $sql .= " ORDER BY u.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Count users */
    public function count(string $role = null): int {
        $sql = "SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id";
        $params = [];
        if ($role) { $sql .= " WHERE r.name = ?"; $params[] = $role; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /** Delete user */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Get role_id by name */
    public function getRoleId(string $name): int {
        $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute([$name]);
        return (int)$stmt->fetchColumn();
    }
}
