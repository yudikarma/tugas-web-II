# Inventory Manager CRUD

Aplikasi mini CRUD inventory untuk tugas Pemrograman Web II. Aplikasi dibuat dengan PHP, MySQL, dan `mysqli` object-oriented style.

## Cara Menjalankan

1. Buat database dan tabel dengan menjalankan file `database.sql` melalui phpMyAdmin atau MySQL CLI.
2. Alternatif lain: buka `install.php` dari browser untuk membuat database, tabel, dan data awal secara otomatis.
3. Sesuaikan konfigurasi koneksi di `config.php` jika user/password MySQL berbeda.
4. Jalankan aplikasi melalui server PHP/XAMPP:

```bash
php -S localhost:8000
```

5. Buka `http://localhost:8000/install.php`, lalu lanjut ke `index.php`.

## Fitur

- Menampilkan summary header inventory.
- Menampilkan daftar item dari database.
- Menambah data item baru dengan prepared statement.
- Mengedit data item dengan prepared statement.
- Menghapus data item dengan prepared statement.
- Menampilkan pesan error saat koneksi atau query gagal.
- Pencarian dan filter kategori sederhana.
