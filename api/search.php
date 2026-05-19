<?php
/**
 * api/search.php — AJAX product search autocomplete endpoint
 * Returns JSON array of matching products
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/helpers.php';

$q = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db   = Database::connect();
    $stmt = $db->prepare("
        SELECT p.id, p.name, p.price, p.discount_price, p.image,
               c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN seller_profiles sp ON p.seller_id = sp.user_id
        WHERE p.status = 'active'
          AND sp.approval_status = 'approved'
          AND (p.name LIKE ? OR c.name LIKE ?)
        ORDER BY p.name ASC
        LIMIT 8
    ");
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
    $results = $stmt->fetchAll();

    $out = [];
    foreach ($results as $r) {
        $price = $r['discount_price'] ?: $r['price'];
        $out[] = [
            'id'       => $r['id'],
            'name'     => $r['name'],
            'category' => $r['category_name'],
            'price'    => 'GH₵ ' . number_format($price, 2),
            'image'    => productImage($r['image'], $r['name'], $r['category_name']),
            'url'      => BASE_URL . 'index.php?page=product_detail&id=' . $r['id'],
        ];
    }
    echo json_encode($out);
} catch (Exception $e) {
    echo json_encode([]);
}
