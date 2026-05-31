<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

$messages = [];
$error = '';

$connection = @new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($connection->connect_errno) {
    $error = 'Koneksi MySQL gagal: ' . $connection->connect_error;
} else {
    $queries = [
        'Membuat database' => "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        'Memilih database' => "USE " . DB_NAME,
        'Membuat tabel items' => "CREATE TABLE IF NOT EXISTS items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            item_name VARCHAR(120) NOT NULL,
            sku VARCHAR(40) NOT NULL UNIQUE,
            category VARCHAR(80) NOT NULL,
            stock_level INT UNSIGNED NOT NULL DEFAULT 0,
            unit VARCHAR(40) NOT NULL,
            reorder_level INT UNSIGNED NOT NULL DEFAULT 0,
            price DECIMAL(12,2) NOT NULL DEFAULT 0,
            supplier VARCHAR(120) NOT NULL,
            image_url VARCHAR(500) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
    ];

    foreach ($queries as $label => $query) {
        if (!$connection->query($query)) {
            $error = $label . ' gagal: ' . $connection->error;
            break;
        }
        $messages[] = $label . ' berhasil.';
    }

    if ($error === '') {
        $seed = $connection->prepare('INSERT INTO items (item_name, sku, category, stock_level, unit, reorder_level, price, supplier, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE item_name = VALUES(item_name), category = VALUES(category), stock_level = VALUES(stock_level), unit = VALUES(unit), reorder_level = VALUES(reorder_level), price = VALUES(price), supplier = VALUES(supplier), image_url = VALUES(image_url)');
        if (!$seed) {
            $error = 'Prepared statement seed gagal: ' . $connection->error;
        } else {
            $items = [
                ['Espresso Roast', 'ESP-001', 'Coffee Beans', 84, '5lb Bags', 20, 185000.0, 'PT Kopi Nusantara', 'https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=200&q=80'],
                ['Whole Milk', 'DRY-042', 'Dairy', 12, 'Gallons', 15, 32000.0, 'CV Segar Dairy', 'https://images.unsplash.com/photo-1563636619-e9143da7973b?auto=format&fit=crop&w=200&q=80'],
                ['Butter Croissant', 'PAS-112', 'Pastries', 4, 'Trays', 10, 95000.0, 'Bakery Central', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=200&q=80'],
                ['Vanilla Syrup', 'SYR-009', 'Syrups', 45, 'Bottles', 12, 78000.0, 'Sweet Supply Co', 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?auto=format&fit=crop&w=200&q=80'],
            ];

            foreach ($items as $row) {
                [$itemName, $sku, $category, $stockLevel, $unit, $reorderLevel, $price, $supplier, $imageUrl] = $row;
                $seed->bind_param('sssisidss', $itemName, $sku, $category, $stockLevel, $unit, $reorderLevel, $price, $supplier, $imageUrl);
                if (!$seed->execute()) {
                    $error = 'Seed data gagal: ' . $seed->error;
                    break;
                }
            }

            if ($error === '') {
                $messages[] = 'Seed data berhasil.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Database</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#F2F0EB] text-stone-900 flex items-center justify-center p-6">
    <main class="w-full max-w-xl bg-white border border-stone-200 rounded-lg p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-emerald-900">Install Database Inventory</h1>
        <p class="text-sm text-stone-600 mt-1">Halaman ini membuat database, tabel, dan data awal.</p>

        <?php if ($error !== ''): ?>
            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><?= e($error) ?></div>
        <?php else: ?>
            <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                <?php foreach ($messages as $message): ?>
                    <p><?= e($message) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-6 flex gap-3">
            <a class="px-4 py-2 rounded-lg bg-emerald-700 text-white text-sm font-bold hover:bg-emerald-800" href="index.php">Buka Aplikasi</a>
            <a class="px-4 py-2 rounded-lg border border-stone-200 text-sm font-bold hover:bg-stone-50" href="database.sql">Lihat SQL</a>
        </div>
    </main>
</body>
</html>
