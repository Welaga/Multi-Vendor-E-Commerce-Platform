<?php
// config/helpers.php - Utility / helper functions

/**
 * Redirect to a URL
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Sanitize output for HTML
 */
function e(mixed $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Require login — redirect to login if not authenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect(BASE_URL . 'index.php?page=login');
    }
}

/**
 * Require a specific role
 */
function requireRole(string $role): void {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        redirect(BASE_URL . 'index.php?page=unauthorized');
    }
}

/**
 * Get current user role
 */
function currentRole(): string {
    return $_SESSION['role'] ?? 'guest';
}

/**
 * Get current user id
 */
function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Flash message: set or get
 */
function flash(string $key, string $message = '', string $type = 'success'): string {
    if ($message !== '') {
        $_SESSION['flash'][$key] = ['msg' => $message, 'type' => $type];
        return '';
    }
    if (isset($_SESSION['flash'][$key])) {
        $f = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        $cls = match($f['type']) {
            'error'   => 'danger',
            'warning' => 'warning',
            'info'    => 'info',
            default   => 'success'
        };
        return '<div class="alert alert-' . $cls . ' alert-dismissible fade show" role="alert">'
             . e($f['msg'])
             . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    return '';
}

/**
 * Generate a URL-safe slug from a string
 */
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Format currency (GHS)
 */
function currency(float $amount): string {
    return 'GH₵ ' . number_format($amount, 2);
}

/**
 * Handle file upload, return filename on success
 */
function uploadImage(array $file, string $destDir = null): string|false {
    $destDir = $destDir ?? UPLOAD_PATH;
    $allowed      = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowedMimes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
    if (!in_array($file['type'], $allowed)) return false;
    if ($file['size'] > 5 * 1024 * 1024) return false; // 5MB max
    // Verify actual file contents rather than trusting browser-supplied MIME type
    $imageType = exif_imagetype($file['tmp_name']);
    if ($imageType === false || !in_array($imageType, $allowedMimes)) return false;

    $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('img_', true) . '.' . strtolower($ext);
    $dest = rtrim($destDir, '/') . '/' . $name;

    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
    if (move_uploaded_file($file['tmp_name'], $dest)) return $name;
    return false;
}

/**
 * Render star rating HTML
 */
function starRating(float $avg, int $count = 0): string {
    $html = '<span class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= round($avg)
            ? '<i class="bi bi-star-fill text-warning"></i>'
            : '<i class="bi bi-star text-warning"></i>';
    }
    $html .= ' <small class="text-muted">(' . $count . ')</small></span>';
    return $html;
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100): string {
    return strlen($text) > $length ? substr($text, 0, $length) . '…' : $text;
}

/**
 * Resolve a product/upload image to a browser-ready URL.
 * Falls back to no-image.png when the file is missing or NULL.
 * Accepts extra ignored arguments so callers can pass alt text.
 */
function productImage(?string $filename, mixed ...$ignored): string {
    if ($filename && file_exists(UPLOAD_PATH . $filename)) {
        return UPLOAD_URL . $filename;
    }
    return BASE_URL . 'assets/images/no-image.png';
}

/**
 * Get cart count for current user
 */
function cartCount(): int {
    if (!isLoggedIn() || currentRole() !== 'buyer') return 0;
    $db   = Database::connect();
    $stmt = $db->prepare("SELECT SUM(quantity) FROM cart WHERE buyer_id = ?");
    $stmt->execute([currentUserId()]);
    return (int)$stmt->fetchColumn();
}
