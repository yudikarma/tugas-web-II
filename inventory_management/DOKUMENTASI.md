# Dokumentasi Singkat Aplikasi Mini CRUD Inventory

## 1. Style mysqli yang Digunakan

Aplikasi ini menggunakan `mysqli` object-oriented style. Koneksi dibuat melalui class `mysqli` pada file `config.php`:

```php
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
```

Operasi input, edit, hapus, dan detail item menggunakan prepared statement melalui `$conn->prepare()`, `bind_param()`, dan `execute()`. Prepared statement dipakai agar data dari form tidak langsung digabung ke string SQL sehingga lebih aman dari SQL injection.

## 2. Struktur Database

Nama database: `inventory_management`

Tabel utama: `items`

| Field | Tipe | Keterangan |
| --- | --- | --- |
| `id` | INT UNSIGNED AUTO_INCREMENT | Primary key |
| `item_name` | VARCHAR(120) | Nama barang |
| `sku` | VARCHAR(40) UNIQUE | Kode unik barang |
| `category` | VARCHAR(80) | Kategori barang |
| `stock_level` | INT UNSIGNED | Jumlah stok |
| `unit` | VARCHAR(40) | Satuan barang |
| `reorder_level` | INT UNSIGNED | Batas stok minimum |
| `price` | DECIMAL(12,2) | Harga satuan |
| `supplier` | VARCHAR(120) | Nama supplier |
| `image_url` | VARCHAR(500) | URL gambar item |
| `created_at` | TIMESTAMP | Waktu data dibuat |
| `updated_at` | TIMESTAMP | Waktu data diperbarui |

## 3. Alur Kerja Aplikasi

1. User membuka `index.php`.
2. Aplikasi membuat koneksi ke MySQL melalui `config.php`.
3. Aplikasi mengambil summary inventory dan daftar item dari tabel `items`.
4. User dapat menekan tombol `Tambah` untuk membuka `create.php`.
5. Data dari form tambah disimpan ke database menggunakan prepared statement.
6. User dapat menekan `Edit` pada item untuk membuka `edit.php`.
7. Data edit diperbarui menggunakan prepared statement berdasarkan `id`.
8. User dapat menekan `Hapus`; `delete.php` menghapus data berdasarkan `id` menggunakan prepared statement.
9. Jika koneksi atau query gagal, aplikasi menampilkan pesan error sesuai sumber masalah.

Catatan setup: database bisa dibuat melalui `database.sql` atau halaman `install.php`.
