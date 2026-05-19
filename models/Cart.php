<?php
// models/Cart.php

class Cart {
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    /** Get cart items for buyer */
    public function getItems(int $buyerId): array {
        $stmt = $this->db->prepare("
            SELECT c.*, p.name, p.price, p.discount_price, p.image, p.stock, p.seller_id,
                   u.name AS seller_name, sp.shop_name
            FROM cart c
            JOIN products p ON c.product_id = p.id
            JOIN users u ON p.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            WHERE c.buyer_id = ?
        ");
        $stmt->execute([$buyerId]);
        return $stmt->fetchAll();
    }

    /** Add or update cart item */
    public function addOrUpdate(int $buyerId, int $productId, int $qty = 1): bool {
        $stmt = $this->db->prepare("
            INSERT INTO cart (buyer_id, product_id, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + ?
        ");
        return $stmt->execute([$buyerId, $productId, $qty, $qty]);
    }

    /** Update quantity directly */
    public function updateQty(int $buyerId, int $productId, int $qty): bool {
        $stmt = $this->db->prepare("UPDATE cart SET quantity = ? WHERE buyer_id = ? AND product_id = ?");
        return $stmt->execute([$qty, $buyerId, $productId]);
    }

    /** Remove an item */
    public function remove(int $buyerId, int $productId): bool {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE buyer_id = ? AND product_id = ?");
        return $stmt->execute([$buyerId, $productId]);
    }

    /** Clear entire cart */
    public function clear(int $buyerId): bool {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE buyer_id = ?");
        return $stmt->execute([$buyerId]);
    }

    /** Calculate cart totals */
    public function getTotals(int $buyerId): array {
        $items    = $this->getItems($buyerId);
        $subtotal = 0;
        foreach ($items as $item) {
            $price     = $item['discount_price'] ?: $item['price'];
            $subtotal += $price * $item['quantity'];
        }
        return ['items' => $items, 'subtotal' => $subtotal];
    }

    /** Count distinct items */
    public function countItems(int $buyerId): int {
        $stmt = $this->db->prepare("SELECT SUM(quantity) FROM cart WHERE buyer_id = ?");
        $stmt->execute([$buyerId]);
        return (int)$stmt->fetchColumn();
    }
}
