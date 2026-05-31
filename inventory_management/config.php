<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_OFF);

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'inventory_management';

function db(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $connection = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($connection->connect_errno) {
        die('Koneksi database gagal: ' . htmlspecialchars($connection->connect_error, ENT_QUOTES, 'UTF-8'));
    }

    if (!$connection->set_charset('utf8mb4')) {
        die('Gagal mengatur charset database: ' . htmlspecialchars($connection->error, ENT_QUOTES, 'UTF-8'));
    }

    return $connection;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect_with_message(string $message, string $type = 'success'): never
{
    header('Location: index.php?' . http_build_query([
        'message' => $message,
        'type' => $type,
    ]));
    exit;
}

function stock_status(int $stock, int $reorderLevel): array
{
    if ($stock <= 0) {
        return ['Habis', 'bg-red-100 text-red-700', 'bg-red-600', 0];
    }

    if ($stock <= $reorderLevel) {
        return ['Low Stock', 'bg-amber-100 text-amber-800', 'bg-amber-400', 25];
    }

    return ['Optimal', 'bg-emerald-100 text-emerald-800', 'bg-emerald-600', 80];
}

function money(float $value): string
{
    return 'Rp ' . number_format($value, 0, ',', '.');
}
