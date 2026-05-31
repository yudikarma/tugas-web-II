<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

$conn = db();
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = '(item_name LIKE ? OR sku LIKE ? OR supplier LIKE ?)';
    $keyword = '%' . $search . '%';
    array_push($params, $keyword, $keyword, $keyword);
    $types .= 'sss';
}

if ($category !== '') {
    $where[] = 'category = ?';
    $params[] = $category;
    $types .= 's';
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$summarySql = "SELECT
        COUNT(*) AS total_items,
        COALESCE(SUM(CASE WHEN stock_level <= reorder_level THEN 1 ELSE 0 END), 0) AS low_stock,
        COALESCE(SUM(stock_level * price), 0) AS inventory_value,
        COUNT(DISTINCT category) AS category_count
    FROM items";
$summaryResult = $conn->query($summarySql);
if (!$summaryResult) {
    die('Query summary gagal: ' . e($conn->error));
}
$summary = $summaryResult->fetch_assoc();

$categoriesResult = $conn->query('SELECT DISTINCT category FROM items ORDER BY category');
if (!$categoriesResult) {
    die('Query kategori gagal: ' . e($conn->error));
}
$categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT * FROM items {$whereSql} ORDER BY updated_at DESC, id DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Query list gagal dipersiapkan: ' . e($conn->error));
}
if ($params) {
    $stmt->bind_param($types, ...$params);
}
if (!$stmt->execute()) {
    die('Query list gagal: ' . e($stmt->error));
}
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Manager</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Work+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ceramic: '#EDEBE9',
                        'neutral-warm': '#F2F0EB',
                        primary: '#00482f',
                        'green-light': '#D4E9E2',
                        'error-red': '#C82014'
                    },
                    fontFamily: {
                        jakarta: ['Plus Jakarta Sans', 'sans-serif'],
                        work: ['Work Sans', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    </style>
</head>
<body class="min-h-screen bg-neutral-warm text-stone-900 font-work">
<header class="sticky top-0 z-40 bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <a href="index.php" class="font-jakarta text-xl font-black text-emerald-900">Inventory Manager</a>
        <form class="hidden md:flex relative w-full max-w-sm" method="get">
            <span class="material-symbols-outlined absolute left-3 top-2.5 text-stone-500">search</span>
            <input class="w-full bg-ceramic border-none rounded-full pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary" name="search" value="<?= e($search) ?>" placeholder="Cari item, SKU, supplier...">
            <?php if ($category !== ''): ?>
                <input type="hidden" name="category" value="<?= e($category) ?>">
            <?php endif; ?>
        </form>
        <a href="create.php" class="inline-flex items-center gap-2 bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-emerald-800">
            <span class="material-symbols-outlined text-base">add</span>
            Tambah
        </a>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <section class="mb-8">
        <h1 class="font-jakarta text-3xl font-extrabold text-emerald-900">Inventory Overview</h1>
        <p class="text-sm text-stone-600 mt-1">Ringkasan stok dan daftar barang terbaru.</p>
    </section>

    <?php if (isset($_GET['message'])): ?>
        <div class="mb-6 rounded-lg border <?= ($_GET['type'] ?? '') === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-800' ?> px-4 py-3 text-sm">
            <?= e($_GET['message']) ?>
        </div>
    <?php endif; ?>

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white p-6 rounded-lg border border-ceramic shadow-sm">
            <span class="material-symbols-outlined text-emerald-700 bg-green-light p-2 rounded-lg">inventory_2</span>
            <p class="text-sm text-stone-500 mt-4">Total Item</p>
            <h2 class="font-jakarta text-3xl font-bold"><?= (int) $summary['total_items'] ?></h2>
        </div>
        <div class="bg-white p-6 rounded-lg border border-ceramic shadow-sm">
            <span class="material-symbols-outlined text-error-red bg-red-100 p-2 rounded-lg">warning</span>
            <p class="text-sm text-stone-500 mt-4">Low Stock</p>
            <h2 class="font-jakarta text-3xl font-bold"><?= (int) $summary['low_stock'] ?></h2>
        </div>
        <div class="bg-white p-6 rounded-lg border border-ceramic shadow-sm">
            <span class="material-symbols-outlined text-amber-700 bg-amber-100 p-2 rounded-lg">account_balance_wallet</span>
            <p class="text-sm text-stone-500 mt-4">Nilai Inventory</p>
            <h2 class="font-jakarta text-2xl font-bold"><?= money((float) $summary['inventory_value']) ?></h2>
        </div>
        <div class="bg-white p-6 rounded-lg border border-ceramic shadow-sm">
            <span class="material-symbols-outlined text-emerald-700 bg-green-light p-2 rounded-lg">category</span>
            <p class="text-sm text-stone-500 mt-4">Kategori</p>
            <h2 class="font-jakarta text-3xl font-bold"><?= (int) $summary['category_count'] ?></h2>
        </div>
    </section>

    <section class="bg-white rounded-lg border border-ceramic overflow-hidden shadow-sm">
        <div class="p-6 border-b border-stone-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="font-jakarta text-xl font-bold text-emerald-900">Current Stock List</h3>
                <p class="text-xs text-stone-500 mt-1">Menampilkan <?= count($items) ?> item.</p>
            </div>
            <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0">
                <a class="px-4 py-2 rounded-full text-xs font-bold <?= $category === '' ? 'bg-stone-900 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:border-emerald-700' ?>" href="index.php<?= $search !== '' ? '?' . http_build_query(['search' => $search]) : '' ?>">SEMUA</a>
                <?php foreach ($categories as $row): ?>
                    <?php $cat = $row['category']; ?>
                    <a class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap <?= $category === $cat ? 'bg-stone-900 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:border-emerald-700' ?>" href="index.php?<?= http_build_query(array_filter(['category' => $cat, 'search' => $search])) ?>"><?= e(strtoupper($cat)) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="md:hidden p-4 border-b border-stone-100">
            <form class="relative" method="get">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-stone-500">search</span>
                <input class="w-full bg-ceramic border-none rounded-full pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary" name="search" value="<?= e($search) ?>" placeholder="Cari item...">
                <?php if ($category !== ''): ?>
                    <input type="hidden" name="category" value="<?= e($category) ?>">
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-stone-50 text-stone-500 text-xs font-bold uppercase">
                <tr>
                    <th class="px-6 py-4">Item</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Stok</th>
                    <th class="px-6 py-4">Unit</th>
                    <th class="px-6 py-4">Harga</th>
                    <th class="px-6 py-4">Supplier</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                <?php if (!$items): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-stone-500">Data inventory belum tersedia.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($items as $item): ?>
                    <?php [$label, $badgeClass, $barClass, $width] = stock_status((int) $item['stock_level'], (int) $item['reorder_level']); ?>
                    <tr class="hover:bg-stone-50">
                        <td class="px-6 py-4 min-w-64">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-stone-100 flex-shrink-0">
                                    <img class="w-full h-full object-cover" src="<?= e($item['image_url'] ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=120&q=80') ?>" alt="<?= e($item['item_name']) ?>">
                                </div>
                                <div>
                                    <p class="font-bold"><?= e($item['item_name']) ?></p>
                                    <p class="text-xs text-stone-500">SKU: <?= e($item['sku']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600"><?= e($item['category']) ?></td>
                        <td class="px-6 py-4 min-w-40">
                            <div class="bg-stone-100 rounded-full h-2 w-32 overflow-hidden">
                                <div class="<?= e($barClass) ?> h-full" style="width: <?= $width ?>%"></div>
                            </div>
                            <span class="text-xs mt-1 block"><?= (int) $item['stock_level'] ?> item</span>
                        </td>
                        <td class="px-6 py-4 text-sm"><?= e($item['unit']) ?></td>
                        <td class="px-6 py-4 text-sm"><?= money((float) $item['price']) ?></td>
                        <td class="px-6 py-4 text-sm"><?= e($item['supplier']) ?></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= e($badgeClass) ?>"><?= e($label) ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a class="text-emerald-700 hover:text-emerald-900 font-bold text-sm" href="edit.php?id=<?= (int) $item['id'] ?>">Edit</a>
                                <form method="post" action="delete.php" onsubmit="return confirm('Hapus item <?= e($item['item_name']) ?>?');">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button class="text-red-600 hover:text-red-800 font-bold text-sm" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer class="bg-emerald-900 text-white mt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 text-xs text-emerald-100">
        Aplikasi Mini CRUD Inventory menggunakan PHP mysqli OOP.
    </div>
</footer>
</body>
</html>
