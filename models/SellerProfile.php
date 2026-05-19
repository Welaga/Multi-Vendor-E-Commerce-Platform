<?php
// models/SellerProfile.php

class SellerProfile {
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    public function getByUserId(int $userId): array|false {
        $stmt = $this->db->prepare("SELECT sp.*, u.name, u.email, u.phone, u.address, u.avatar
            FROM seller_profiles sp JOIN users u ON sp.user_id = u.id WHERE sp.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function create(int $userId, string $shopName, string $desc = ''): bool {
        $stmt = $this->db->prepare("INSERT INTO seller_profiles (user_id, shop_name, shop_description) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $shopName, $desc]);
    }

    public function update(int $userId, array $data): bool {
        $stmt = $this->db->prepare("UPDATE seller_profiles SET shop_name=?, shop_description=?, shop_logo=? WHERE user_id=?");
        return $stmt->execute([$data['shop_name'], $data['shop_description'], $data['shop_logo'] ?? 'default_shop.png', $userId]);
    }

    public function setApproval(int $userId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE seller_profiles SET approval_status = ? WHERE user_id = ?");
        return $stmt->execute([$status, $userId]);
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT sp.*, u.name, u.email, u.phone, u.status AS user_status
            FROM seller_profiles sp JOIN users u ON sp.user_id = u.id
            ORDER BY sp.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getPending(): array {
        $stmt = $this->db->query("
            SELECT sp.*, u.name, u.email FROM seller_profiles sp
            JOIN users u ON sp.user_id = u.id WHERE sp.approval_status = 'pending'
        ");
        return $stmt->fetchAll();
    }
}
