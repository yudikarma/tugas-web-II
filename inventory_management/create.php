<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = [
        'item_name' => trim($_POST['item_name'] ?? ''),
        'sku' => trim($_POST['sku'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'stock_level' => (int) ($_POST['stock_level'] ?? 0),
        'unit' => trim($_POST['unit'] ?? ''),
        'reorder_level' => (int) ($_POST['reorder_level'] ?? 0),
        'price' => (float) ($_POST['price'] ?? 0),
        'supplier' => trim($_POST['supplier'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
    ];

    if ($item['item_name'] === '' || $item['sku'] === '' || $item['category'] === '' || $item['unit'] === '' || $item['supplier'] === '') {
        $error = 'Field nama, SKU, kategori, unit, dan supplier wajib diisi.';
    } else {
        $stmt = db()->prepare('INSERT INTO items (item_name, sku, category, stock_level, unit, reorder_level, price, supplier, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            $error = 'Query tambah gagal dipersiapkan: ' . db()->error;
        } else {
            $stmt->bind_param('sssisidss', $item['item_name'], $item['sku'], $item['category'], $item['stock_level'], $item['unit'], $item['reorder_level'], $item['price'], $item['supplier'], $item['image_url']);
            if ($stmt->execute()) {
                redirect_with_message('Data item berhasil ditambahkan.');
            }
            $error = 'Query tambah gagal: ' . $stmt->error;
        }
    }
}

require __DIR__ . '/form.php';
