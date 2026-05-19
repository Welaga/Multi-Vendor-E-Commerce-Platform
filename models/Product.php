<?php
// models/Product.php

class Product {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /** Get all active products with category & seller info */
    public function getAll(array $filters = [], int $limit = 12, int $offset = 0): array {
        $sql = "
            SELECT p.*, c.name AS category_name, u.name AS seller_name,
                   sp.shop_name,
                   COALESCE(AVG(r.rating), 0) AS avg_rating,
                   COUNT(r.id) AS review_count
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN users u ON p.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            LEFT JOIN reviews r ON p.id = r.product_id
            WHERE p.status = 'active' AND sp.approval_status = 'approved'
        ";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['seller_id'])) {
            $sql .= " AND p.seller_id = ?";
            $params[] = $filters['seller_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params[] = $s; $params[] = $s;
        }
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }

        $sql .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Count products matching filters */
    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(DISTINCT p.id) FROM products p
                JOIN seller_profiles sp ON p.seller_id = sp.user_id
                WHERE p.status = 'active' AND sp.approval_status = 'approved'";
        $params = [];
        if (!empty($filters['category_id'])) { $sql .= " AND p.category_id = ?"; $params[] = $filters['category_id']; }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params[] = $s; $params[] = $s;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /** Get single product by ID. Pass $publicOnly=true on public pages to hide unapproved sellers. */
    public function findById(int $id, bool $publicOnly = false): array|false {
        $approvalClause = $publicOnly ? "AND sp.approval_status = 'approved' AND p.status = 'active'" : '';
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name, u.name AS seller_name, sp.shop_name,
                   COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS review_count
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN users u ON p.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            LEFT JOIN reviews r ON p.id = r.product_id
            WHERE p.id = ? $approvalClause
            GROUP BY p.id
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Get products for a specific seller (includes inactive) */
    public function getBySeller(int $sellerId): array {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name,
                   COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS review_count
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN reviews r ON p.id = r.product_id
            WHERE p.seller_id = ?
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    /** Create product */
    public function create(array $data): int {
        $slug = slugify($data['name']) . '-' . uniqid();
        $stmt = $this->db->prepare("
            INSERT INTO products (seller_id, category_id, name, slug, description, price, discount_price, stock, image, status)
            VALUES (:seller_id, :category_id, :name, :slug, :description, :price, :discount_price, :stock, :image, :status)
        ");
        $stmt->execute([
            'seller_id'      => $data['seller_id'],
            'category_id'    => $data['category_id'],
            'name'           => $data['name'],
            'slug'           => $slug,
            'description'    => $data['description'],
            'price'          => $data['price'],
            'discount_price' => $data['discount_price'] ?: null,
            'stock'          => $data['stock'],
            'image'          => $data['image'] ?? 'default_product.png',
            'status'         => $data['status'] ?? 'active',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update product */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE products SET
                category_id = :category_id, name = :name, description = :description,
                price = :price, discount_price = :discount_price, stock = :stock,
                image = :image, status = :status
            WHERE id = :id
        ");
        return $stmt->execute([
            'category_id'    => $data['category_id'],
            'name'           => $data['name'],
            'description'    => $data['description'],
            'price'          => $data['price'],
            'discount_price' => $data['discount_price'] ?: null,
            'stock'          => $data['stock'],
            'image'          => $data['image'],
            'status'         => $data['status'],
            'id'             => $id,
        ]);
    }

    /** Delete product */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Update stock */
    public function decreaseStock(int $id, int $qty): bool {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        return $stmt->execute([$qty, $id, $qty]);
    }

    /** Get featured/latest products */
    public function getFeatured(int $limit = 8): array {
        return $this->getAll([], $limit, 0);
    }

    /** Admin: get all products */
    public function adminGetAll(): array {
        $stmt = $this->db->query("
            SELECT p.*, c.name AS category_name, u.name AS seller_name, sp.shop_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN users u ON p.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }
}
