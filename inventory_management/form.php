<?php
declare(strict_types=1);

$isEdit = isset($item);
$action = $isEdit ? 'edit.php?id=' . (int) $item['id'] : 'create.php';
$title = $isEdit ? 'Edit Item' : 'Tambah Item';
$subtitle = $isEdit ? 'Perbarui data inventory yang sudah ada.' : 'Masukkan item baru ke dalam inventory.';

$values = array_merge([
    'item_name' => '',
    'sku' => '',
    'category' => '',
    'stock_level' => 0,
    'unit' => '',
    'reorder_level' => 0,
    'price' => 0,
    'supplier' => '',
    'image_url' => '',
], $item ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> - Inventory Manager</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Work+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { ceramic: '#EDEBE9', 'neutral-warm': '#F2F0EB', primary: '#00482f' },
                    fontFamily: { jakarta: ['Plus Jakarta Sans', 'sans-serif'], work: ['Work Sans', 'sans-serif'] }
                }
            }
        };
    </script>
    <style>.material-symbols-outlined { vertical-align: middle; }</style>
</head>
<body class="min-h-screen bg-neutral-warm text-stone-900 font-work">
<header class="bg-white border-b border-stone-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <a href="index.php" class="font-jakarta text-xl font-black text-emerald-900">Inventory Manager</a>
        <a href="index.php" class="inline-flex items-center gap-2 text-sm font-bold text-emerald-800 hover:text-emerald-950">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali
        </a>
    </div>
</header>

<main class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <section class="mb-6">
        <h1 class="font-jakarta text-3xl font-extrabold text-emerald-900"><?= e($title) ?></h1>
        <p class="text-sm text-stone-600 mt-1"><?= e($subtitle) ?></p>
    </section>

    <?php if (!empty($error)): ?>
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e($action) ?>" class="bg-white border border-ceramic rounded-lg shadow-sm p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <label class="block">
            <span class="text-sm font-bold text-stone-700">Nama Item</span>
            <input required name="item_name" value="<?= e($values['item_name']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700" placeholder="Espresso Roast">
        </label>
        <label class="block">
            <span class="text-sm font-bold text-stone-700">SKU</span>
            <input required name="sku" value="<?= e($values['sku']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700" placeholder="ESP-001">
        </label>
        <label class="block">
            <span class="text-sm font-bold text-stone-700">Kategori</span>
            <input required name="category" value="<?= e($values['category']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700" placeholder="Coffee Beans">
        </label>
        <label class="block">
            <span class="text-sm font-bold text-stone-700">Unit</span>
            <input required name="unit" value="<?= e($values['unit']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700" placeholder="5lb Bags">
        </label>
        <label class="block">
            <span class="text-sm font-bold text-stone-700">Stok</span>
            <input required min="0" type="number" name="stock_level" value="<?= e((string) $values['stock_level']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700">
        </label>
        <label class="block">
            <span class="text-sm font-bold text-stone-700">Batas Reorder</span>
            <input required min="0" type="number" name="reorder_level" value="<?= e((string) $values['reorder_level']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700">
        </label>
        <label class="block">
            <span class="text-sm font-bold text-stone-700">Harga Satuan</span>
            <input required min="0" step="0.01" type="number" name="price" value="<?= e((string) $values['price']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700">
        </label>
        <label class="block">
            <span class="text-sm font-bold text-stone-700">Supplier</span>
            <input required name="supplier" value="<?= e($values['supplier']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700" placeholder="PT Kopi Nusantara">
        </label>
        <label class="block md:col-span-2">
            <span class="text-sm font-bold text-stone-700">URL Gambar</span>
            <input type="url" name="image_url" value="<?= e($values['image_url']) ?>" class="mt-1 w-full rounded-lg border-stone-200 focus:border-emerald-700 focus:ring-emerald-700" placeholder="https://...">
        </label>
        <div class="md:col-span-2 flex justify-end gap-3 pt-2">
            <a href="index.php" class="px-4 py-2 rounded-lg border border-stone-200 text-sm font-bold hover:bg-stone-50">Batal</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 text-white text-sm font-bold hover:bg-emerald-800">Simpan</button>
        </div>
    </form>
</main>
</body>
</html>
