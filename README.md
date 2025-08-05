# 📦 Sistem Inventaris Barang (ruberman-inventory)

<p align="center">
<img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
<img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
<img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</p>

**Sistem Inventaris Barang** adalah aplikasi web sederhana yang dibangun menggunakan **PHP** dan **MySQL**. Aplikasi ini dirancang untuk membantu pengguna dalam mengelola dan melacak stok barang, mencatat transaksi barang masuk dan keluar, serta mengelola akses pengguna. Proyek ini merupakan contoh implementasi sistem inventaris dasar dengan fokus pada fungsionalitas inti, keamanan, dan integritas data.

-----

## ✨ Fitur Utama

  - **📦 Manajemen Stok Barang:** Menambah, mengedit, dan menghapus data barang beserta gambar produk.
  - **📥 Transaksi Barang Masuk:** Mencatat setiap barang yang masuk ke inventaris dan secara otomatis memperbarui jumlah stok.
  - **📤 Transaksi Barang Keluar:** Mencatat setiap barang yang keluar dari inventaris dan memastikan stok selalu sinkron.
  - **🤝 Peminjaman Barang:** Fitur untuk melacak peminjaman barang dan status pengembaliannya.
  - **🎉 Manajemen Event:** Mengelola pengeluaran dan pengembalian barang untuk keperluan acara atau event tertentu.
  - **👥 Manajemen Pengguna:** Sistem memiliki dua peran (roles): **Admin** (akses penuh) dan **User** (akses terbatas).
  - **🔐 Sistem Login:** Mekanisme otentikasi yang aman untuk membatasi akses ke sistem.
  - **📊 Ekspor Data:** Fungsionalitas untuk mengekspor data stok, peminjaman, dan event ke dalam format **Excel & PDF**.
  - **⚠️ Notifikasi Stok:** Memberikan peringatan visual ketika stok suatu barang telah habis.

-----

## 🛠️ Arsitektur & Teknologi

| Kategori      | Teknologi / Pustaka                                    |
| :------------ | :----------------------------------------------------- |
| **Backend** | `PHP` (Prosedural)                                     |
| **Database** | `MySQL`                                                |
| **Frontend** | `HTML`, `CSS`, `JavaScript`                            |
| **Framework & Perpustakaan** | `Bootstrap`, `Font Awesome`, `Simple DataTables` |

<br>

<details>
<summary>📂 Struktur Proyek</summary>

<pre>
ruberman-inventory/
├── assets/
│   ├── css/
│   │   ├── responsive.css
│   │   └── styles.css
│   └── img/
├── db/
│   └── inventory\_ruberman.sql
├── includes/
│   ├── admin\_cek.php
│   ├── cek.php
│   ├── function.php
│   └── sidebar.php
├── js/
│   ├── chart-area-demo.js
│   ├── chart-bar-demo.js
│   ├── chart-pie-demo.js
│   ├── datatables-demo.js
│   └── scripts.js
├── uploads/
│   └── (gambar barang yang di-upload)
├── admin.php
├── detail\_event.php
├── event.php
├── export.php
├── export\_event.php
├── export\_peminjaman.php
├── index.php
├── keluar.php
├── login.php
├── logout.php
├── masuk.php
├── masuk\_event.php
├── peminjaman.php
└── README.md
</pre>

</details>

-----

## 🚀 Panduan Instalasi & Penggunaan

### **Prasyarat**

1.  **Web Server** (contoh: XAMPP, WAMP).
2.  **PHP** versi 7.4 atau lebih tinggi.
3.  **Database MySQL** atau MariaDB.

### **Langkah-langkah Instalasi**

1.  **Clone Repositori**

    ```bash
    git clone https://github.com/username/ruberman-inventory.git
    ```

    *(Ganti `username` dengan nama pengguna GitHub Anda)*

2.  **Setup Database**

      - Buka **phpMyAdmin** dan buat database baru bernama `inventory_ruberman`.
      - Impor file `db/inventory_ruberman.sql` ke dalam database yang baru saja Anda buat.

3.  **Konfigurasi Koneksi**

      - Buka file `includes/function.php`.
      - Sesuaikan detail koneksi database pada baris berikut jika diperlukan.
        ```php
        $conn = mysqli_connect("localhost","root","","inventory_ruberman");
        ```

4.  **Jalankan Aplikasi**

      - Letakkan folder proyek di dalam direktori `htdocs` (jika menggunakan XAMPP) atau `www` (jika menggunakan WAMP).
      - Buka browser dan akses `http://localhost/ruberman-inventory/`.

### **Akses dan Penggunaan Aplikasi**

  - Buka halaman login dan gunakan kredensial yang sudah ada di database.
  - Data login awal dapat dilihat atau ditambahkan langsung pada tabel `login` di database. Password di-hash menggunakan `PASSWORD_BCRYPT`.
  - Setelah login, Anda dapat mulai mengelola data melalui menu navigasi yang tersedia.
