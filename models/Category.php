<?php
// models/Category.php

class Category {
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    public function getAll(bool $activeOnly = true): array {
        $sql = "SELECT c.*, COUNT(p.id) AS product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'";
        if ($activeOnly) $sql .= " WHERE c.status = 'active'";
        $sql .= " GROUP BY c.id ORDER BY c.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO categories (name, slug, icon) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            slugify($data['name']),
            $data['icon'] ?? 'bi-tag'
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE categories SET name=?, slug=?, icon=?, status=? WHERE id=?");
        return $stmt->execute([$data['name'], slugify($data['name']), $data['icon'], $data['status'], $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
