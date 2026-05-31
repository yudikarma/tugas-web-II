<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_message('Metode request tidak valid.', 'error');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    redirect_with_message('ID item tidak valid.', 'error');
}

$stmt = db()->prepare('DELETE FROM items WHERE id = ?');
if (!$stmt) {
    redirect_with_message('Query hapus gagal dipersiapkan: ' . db()->error, 'error');
}

$stmt->bind_param('i', $id);
if (!$stmt->execute()) {
    redirect_with_message('Query hapus gagal: ' . $stmt->error, 'error');
}

redirect_with_message('Data item berhasil dihapus.');
