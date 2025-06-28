# 📦 Sistem Inventaris Barang (ruberman-inventory)

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</p>

**Sistem Inventaris Barang** adalah aplikasi web sederhana yang dibangun menggunakan **PHP** dan **MySQL**. Aplikasi ini dirancang untuk membantu pengguna dalam mengelola dan melacak stok barang, mencatat transaksi barang masuk dan keluar, serta mengelola akses pengguna.

Proyek ini merupakan contoh implementasi sistem inventaris dasar dengan fokus pada fungsionalitas inti, keamanan, dan integritas data.

---

## ✨ Fitur Utama

-   **📦 Manajemen Stok Barang:** Menambah, mengedit, dan menghapus data barang beserta gambar produk.
-   **📥 Transaksi Barang Masuk:** Mencatat setiap barang yang masuk ke inventaris dan secara otomatis memperbarui jumlah stok.
-   **📤 Transaksi Barang Keluar:** Mencatat setiap barang yang keluar dari inventaris dan memastikan stok selalu sinkron.
-   **👥 Manajemen Pengguna:** Sistem memiliki dua peran (roles): **Admin** (akses penuh) dan **User** (akses terbatas).
-   **🔐 Sistem Login:** Mekanisme otentikasi yang aman untuk membatasi akses ke sistem.
-   **📊 Ekspor Data:** Fungsionalitas untuk mengekspor data stok ke dalam format **Excel**.
-   **⚠️ Notifikasi Stok:** Memberikan peringatan visual ketika stok suatu barang telah habis.

---

## 🛠️ Teknologi yang Digunakan

| Kategori      | Teknologi / Pustaka                                    |
| :------------ | :----------------------------------------------------- |
| **Backend** | `PHP` (Prosedural)                                     |
| **Database** | `MySQL`                                                |
| **Frontend** | `HTML`, `CSS`, `JavaScript`                            |
| **Framework & Perpustakaan** | `Bootstrap`, `Font Awesome`, `Simple DataTables` |

---

## 🚀 Instalasi dan Penggunaan Lokal

Untuk menjalankan proyek ini di komputer lokal Anda, ikuti langkah-langkah berikut:

**1. Clone Repositori**
```bash
git clone [https://github.com/username/ruberman-inventory.git](https://github.com/username/ruberman-inventory.git)
````

*(Jangan lupa ganti `username` dengan nama pengguna GitHub Anda)*

**2. Setup Database**

  - Buat sebuah database baru di phpMyAdmin (atau tool sejenis) dengan nama `inventory_ruberman`.
  - Impor file `db/iventory.sql` ke dalam database yang baru saja Anda buat.

**3. Konfigurasi Koneksi**

  - Buka file `includes/function.php`.
  - Sesuaikan detail koneksi database pada baris berikut jika diperlukan.
    ```php
    $conn = mysqli_connect("localhost","root","","inventory_ruberman");
    ```

**4. Jalankan Aplikasi**

  - Letakkan folder proyek di dalam direktori `htdocs` (jika menggunakan XAMPP) atau `www` (jika menggunakan WAMP).
  - Buka browser dan akses `http://localhost/ruberman-inventory/`.

**5. Login**

  - Anda bisa login menggunakan akun yang sudah Anda buat. Jika belum ada, Anda bisa menambahkannya langsung melalui database di tabel `login` dengan password yang sudah di-hash menggunakan `PASSWORD_BCRYPT`.

<!-- end list -->