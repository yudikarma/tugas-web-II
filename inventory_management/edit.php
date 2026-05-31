<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect_with_message('ID item tidak valid.', 'error');
}

$conn = db();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = [
        'id' => $id,
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
        $stmt = $conn->prepare('UPDATE items SET item_name = ?, sku = ?, category = ?, stock_level = ?, unit = ?, reorder_level = ?, price = ?, supplier = ?, image_url = ? WHERE id = ?');
        if (!$stmt) {
            $error = 'Query edit gagal dipersiapkan: ' . $conn->error;
        } else {
            $stmt->bind_param('sssisidssi', $item['item_name'], $item['sku'], $item['category'], $item['stock_level'], $item['unit'], $item['reorder_level'], $item['price'], $item['supplier'], $item['image_url'], $id);
            if ($stmt->execute()) {
                redirect_with_message('Data item berhasil diperbarui.');
            }
            $error = 'Query edit gagal: ' . $stmt->error;
        }
    }
} else {
    $stmt = $conn->prepare('SELECT * FROM items WHERE id = ?');
    if (!$stmt) {
        die('Query detail gagal dipersiapkan: ' . e($conn->error));
    }
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        die('Query detail gagal: ' . e($stmt->error));
    }
    $item = $stmt->get_result()->fetch_assoc();
    if (!$item) {
        redirect_with_message('Data item tidak ditemukan.', 'error');
    }
}

require __DIR__ . '/form.php';
