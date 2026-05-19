<?php
// models/Coupon.php

class Coupon {
    private PDO $db;
    public function __construct() { $this->db = Database::connect(); }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
    }

    public function findByCode(string $code): array|false {
        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    /** Apply coupon to an amount; returns [discount, message] */
    public function apply(string $code, float $orderTotal): array {
        $coupon = $this->findByCode($code);
        if (!$coupon) return [0, 'Invalid or expired coupon.'];
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) return [0, 'Coupon usage limit reached.'];
        if ($orderTotal < $coupon['minimum_order']) return [0, 'Minimum order of ' . currency($coupon['minimum_order']) . ' required.'];

        $discount = $coupon['discount_type'] === 'percentage'
            ? $orderTotal * ($coupon['discount_value'] / 100)
            : $coupon['discount_value'];

        return [round(min($discount, $orderTotal), 2), 'Coupon applied!'];
    }

    /** Increment usage */
    public function incrementUsage(string $code): void {
        $stmt = $this->db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?");
        $stmt->execute([$code]);
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO coupons (code, discount_type, discount_value, minimum_order, usage_limit, expiry_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            strtoupper($data['code']), $data['discount_type'],
            $data['discount_value'], $data['minimum_order'] ?? 0,
            $data['usage_limit'] ?: null, $data['expiry_date']
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM coupons WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function setStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE coupons SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
