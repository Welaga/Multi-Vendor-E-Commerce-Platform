<?php
// models/Wishlist.php

class Wishlist {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /** Get all wishlist items for a buyer */
    public function getByBuyer(int $buyerId): array {
        $stmt = $this->db->prepare("
            SELECT w.*, p.name, p.price, p.discount_price, p.image, p.stock,
                   p.status AS product_status, c.name AS category_name,
                   u.name AS seller_name, sp.shop_name,
                   COALESCE(AVG(r.rating),0) AS avg_rating,
                   COUNT(r.id) AS review_count
            FROM wishlist w
            JOIN products p ON w.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            JOIN users u ON p.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            LEFT JOIN reviews r ON p.id = r.product_id
            WHERE w.buyer_id = ?
            GROUP BY w.id
            ORDER BY w.created_at DESC
        ");
        $stmt->execute([$buyerId]);
        return $stmt->fetchAll();
    }

    /** Toggle: add if not present, remove if present. Returns true if added. */
    public function toggle(int $buyerId, int $productId): bool {
        if ($this->has($buyerId, $productId)) {
            $this->remove($buyerId, $productId);
            return false;
        }
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO wishlist (buyer_id, product_id) VALUES (?, ?)
        ");
        $stmt->execute([$buyerId, $productId]);
        return true;
    }

    /** Check if product is in wishlist */
    public function has(int $buyerId, int $productId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM wishlist WHERE buyer_id = ? AND product_id = ?
        ");
        $stmt->execute([$buyerId, $productId]);
        return (bool)$stmt->fetchColumn();
    }

    /** Remove from wishlist */
    public function remove(int $buyerId, int $productId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM wishlist WHERE buyer_id = ? AND product_id = ?
        ");
        return $stmt->execute([$buyerId, $productId]);
    }

    /** Count wishlist items */
    public function count(int $buyerId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlist WHERE buyer_id = ?");
        $stmt->execute([$buyerId]);
        return (int)$stmt->fetchColumn();
    }
}
