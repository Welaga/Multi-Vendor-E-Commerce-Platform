<?php
// models/Review.php

class Review {
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    public function getByProduct(int $productId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, u.name AS buyer_name, u.avatar
            FROM reviews r JOIN users u ON r.buyer_id = u.id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function hasReviewed(int $productId, int $buyerId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE product_id = ? AND buyer_id = ?");
        $stmt->execute([$productId, $buyerId]);
        return (bool)$stmt->fetchColumn();
    }

    public function create(int $productId, int $buyerId, int $rating, string $text): bool {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (product_id, buyer_id, rating, review_text) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), review_text = VALUES(review_text)
        ");
        return $stmt->execute([$productId, $buyerId, $rating, $text]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
