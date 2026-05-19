<?php
// config/init.php — Auto-setup checks (called from config.php)

// Create uploads directory if missing
$uploadsDir = __DIR__ . '/../uploads/products';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

// Create a blank index.php in uploads to prevent directory listing
$indexFile = __DIR__ . '/../uploads/index.php';
if (!file_exists($indexFile)) {
    file_put_contents($indexFile, '<?php // Silence is golden');
}
$indexFile2 = $uploadsDir . '/index.php';
if (!file_exists($indexFile2)) {
    file_put_contents($indexFile2, '<?php // Silence is golden');
}
