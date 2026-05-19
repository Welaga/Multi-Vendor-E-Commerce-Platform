<?php
// models/Order.php

class Order {
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    /** Create order from cart */
    public function create(array $data, array $items): int|false {
        $this->db->beginTransaction();
        try {
            // Insert order
            $stmt = $this->db->prepare("
                INSERT INTO orders (buyer_id, total_amount, discount_amount, final_amount, coupon_code, shipping_address, payment_method)
                VALUES (:buyer_id, :total_amount, :discount_amount, :final_amount, :coupon_code, :shipping_address, :payment_method)
            ");
            $stmt->execute([
                'buyer_id'         => $data['buyer_id'],
                'total_amount'     => $data['total_amount'],
                'discount_amount'  => $data['discount_amount'],
                'final_amount'     => $data['final_amount'],
                'coupon_code'      => $data['coupon_code'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'payment_method'   => $data['payment_method'] ?? 'cash_on_delivery',
            ]);
            $orderId = (int)$this->db->lastInsertId();

            // Insert order items & earnings
            $itemStmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, seller_id, quantity, unit_price, total_price)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $earnStmt = $this->db->prepare("
                INSERT INTO seller_earnings (seller_id, order_id, order_item_id, amount, commission, net_earning)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $prodModel = new Product();

            foreach ($items as $item) {
                $price = $item['discount_price'] ?: $item['price'];
                $total = $price * $item['quantity'];
                $itemStmt->execute([
                    $orderId, $item['product_id'], $item['seller_id'],
                    $item['quantity'], $price, $total
                ]);
                $itemId     = (int)$this->db->lastInsertId();
                $commission = round($total * COMMISSION_RATE, 2);
                $net        = $total - $commission;
                $earnStmt->execute([$item['seller_id'], $orderId, $itemId, $total, $commission, $net]);
                // Decrease stock
                $prodModel->decreaseStock($item['product_id'], $item['quantity']);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /** Get orders for a buyer */
    public function getByBuyer(int $buyerId): array {
        $stmt = $this->db->prepare("
            SELECT o.*, COUNT(oi.id) AS item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.buyer_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$buyerId]);
        return $stmt->fetchAll();
    }

    /** Get orders for a seller */
    public function getBySeller(int $sellerId): array {
        $stmt = $this->db->prepare("
            SELECT DISTINCT o.*, u.name AS buyer_name
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN users u ON o.buyer_id = u.id
            WHERE oi.seller_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    /** Get all orders (admin) */
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT o.*, u.name AS buyer_name, COUNT(oi.id) AS item_count
            FROM orders o
            JOIN users u ON o.buyer_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /** Get order with items */
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone
            FROM orders o JOIN users u ON o.buyer_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Get items for an order */
    public function getItems(int $orderId): array {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name AS product_name, p.image, u.name AS seller_name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN users u ON oi.seller_id = u.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    /** Update order status */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /** Get seller earnings summary */
    public function getSellerEarnings(int $sellerId): array {
        $stmt = $this->db->prepare("
            SELECT
                SUM(net_earning) AS total_earned,
                SUM(CASE WHEN payout_status = 'pending' THEN net_earning ELSE 0 END) AS pending,
                SUM(CASE WHEN payout_status = 'completed' THEN net_earning ELSE 0 END) AS paid_out
            FROM seller_earnings WHERE seller_id = ?
        ");
        $stmt->execute([$sellerId]);
        return $stmt->fetch();
    }

    /** Mark seller payout as completed */
    public function markPayout(int $sellerId): bool {
        $stmt = $this->db->prepare("UPDATE seller_earnings SET payout_status = 'completed' WHERE seller_id = ? AND payout_status = 'pending'");
        return $stmt->execute([$sellerId]);
    }

    /** Total platform revenue */
    public function platformStats(): array {
        $stmt = $this->db->query("
            SELECT
                COUNT(DISTINCT o.id) AS total_orders,
                SUM(o.final_amount) AS total_revenue,
                SUM(se.commission) AS platform_commission,
                COUNT(DISTINCT o.buyer_id) AS unique_buyers
            FROM orders o
            LEFT JOIN seller_earnings se ON o.id = se.order_id
        ");
        return $stmt->fetch();
    }
}
