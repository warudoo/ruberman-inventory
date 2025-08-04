<?php
session_start();

// 1. KONEKSI DATABASE
$conn = mysqli_connect("localhost","root","","inventory_ruberman");


// 2. FUNGSI UTAMA UNTUK SINKRONISASI STOK (SUMBER KEBENARAN TUNGGAL)
function recalculateStock($idbarang, $conn) {
    // Hitung total kuantitas dari semua transaksi masuk
    $query_masuk = mysqli_query($conn, "SELECT SUM(qty) as total FROM masuk WHERE idbarang = '$idbarang'");
    $total_masuk = (int)mysqli_fetch_assoc($query_masuk)['total'];

    // Hitung total kuantitas dari semua transaksi keluar
    $query_keluar = mysqli_query($conn, "SELECT SUM(qty) as total FROM keluar WHERE idbarang = '$idbarang'");
    $total_keluar = (int)mysqli_fetch_assoc($query_keluar)['total'];

    // Stok akhir adalah total masuk dikurangi total keluar
    $stok_sebenarnya = $total_masuk - $total_keluar;

    // Perbarui tabel stock dengan angka yang pasti benar
    mysqli_query($conn, "UPDATE stock SET stock = '$stok_sebenarnya' WHERE idbarang = '$idbarang'");
}


// 3. PROSES FORM UNTUK MANAJEMEN BARANG

// Menambah barang baru (dengan pencatatan stok awal dan lokasi)
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock_awal = (int)$_POST['stock'];
    $lokasi = $_POST['lokasi']; // <-- DITAMBAHKAN
    $image = '';

    // Proses upload gambar jika ada
    if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
        $allowed_extension = array('png','jpg','jpeg');
        $nama = $_FILES['file']['name'];
        $dot = explode('.', $nama);
        $ekstensi = strtolower(end($dot));
        if(in_array($ekstensi, $allowed_extension)) {
            $image = md5(uniqid($nama,true).time()).'.'.$ekstensi;
            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$image);
        }
    }
    
    // Masukkan barang baru ke tabel stock dengan stok awal 0 dan lokasi
    $addtostock = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, lokasi, image) VALUES ('$namabarang', '$deskripsi', '0', '$lokasi', '$image')"); // <-- DIPERBARUI
    
    // Jika berhasil, dan jika ada input stok awal, catat sebagai transaksi "Barang Masuk"
    if($addtostock && $stock_awal > 0){
        $idbarang_baru = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$idbarang_baru', 'Stok Awal', '$stock_awal')");
        recalculateStock($idbarang_baru, $conn);
    }
    header('location:index.php');
}


// Menambah barang masuk
if(isset($_POST['barangmasuk'])) {
    $idbarang = (int)$_POST['barangnya'];
    $keterangan = $_POST['penerima'];
    $qty = (int)$_POST['qty'];

    $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$idbarang', '$keterangan', '$qty')");
    if($addtomasuk){
        recalculateStock($idbarang, $conn);
        header('location:masuk.php');
    }
}


// Menambah barang keluar 
if(isset($_POST['addbarangkeluar'])) {
    $idbarang = (int)$_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = (int)$_POST['qty'];
    
    $stock_query = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang = '$idbarang'");
    $stock_sekarang = (int)mysqli_fetch_assoc($stock_query)['stock'];

    if($stock_sekarang >= $qty) {
        $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$idbarang', '$penerima', '$qty')");
        if($addtokeluar){
            recalculateStock($idbarang, $conn);
            header('location:keluar.php');
        }
    } else {
        echo '<script>alert("Stock tidak mencukupi"); window.location.href="keluar.php";</script>';
    }
}

    
// Mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])) {
    $idb = (int)$_POST['idb'];
    $idm = (int)$_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = (int)$_POST['qty'];

    $update_masuk = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan='$deskripsi' WHERE idmasuk = '$idm'");
    if($update_masuk) {
        recalculateStock($idb, $conn);
        header('location:masuk.php');
    }
}


// Mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])) {
    $idb = (int)$_POST['idb'];
    $idk = (int)$_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty_baru = (int)$_POST['qty'];

    $keluar_query = mysqli_query($conn, "SELECT qty FROM keluar WHERE idkeluar = '$idk'");
    $qty_lama_keluar = (int)mysqli_fetch_assoc($keluar_query)['qty'];

    $stock_query = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang = '$idb'");
    $stock_sekarang = (int)mysqli_fetch_assoc($stock_query)['stock'];
    
    $stok_setelah_edit = $stock_sekarang + $qty_lama_keluar - $qty_baru;

    if ($stok_setelah_edit >= 0) {
        $update_keluar = mysqli_query($conn, "UPDATE keluar SET qty = '$qty_baru', penerima='$penerima' WHERE idkeluar = '$idk'");
        if($update_keluar) {
            recalculateStock($idb, $conn);
            header('location:keluar.php');
        }
    } else {
         echo '<script>alert("Stock tidak mencukupi untuk perubahan ini"); window.location.href="keluar.php";</script>';
    }
}


// Menghapus data barang masuk
if(isset($_POST['hapusbarangmasuk'])) {
    $idb = (int)$_POST['idb'];
    $idm = (int)$_POST['idm'];

    $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk = '$idm'");
    if($hapusdata) {
        recalculateStock($idb, $conn);
        header('location:masuk.php');
    }
}


// Menghapus data barang keluar
if(isset($_POST['hapusbarangkeluar'])) {
    $idb = (int)$_POST['idb'];
    $idk = (int)$_POST['idk'];

    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar = '$idk'");
    if($hapusdata) {
        recalculateStock($idb, $conn);
        header('location:keluar.php');
    }
}


// Update info barang (nama, deskripsi, lokasi, gambar)
if(isset($_POST['updatebarang'])) {
    $idb = (int)$_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $lokasi = $_POST['lokasi']; // <-- DITAMBAHKAN

    if(isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $allowed_extension = array('png','jpg','jpeg');
        $nama = $_FILES['file']['name'];
        $dot = explode('.', $nama);
        $ekstensi = strtolower(end($dot));
        $image = md5(uniqid($nama,true).time()).'.'.$ekstensi;

        if(in_array($ekstensi, $allowed_extension)){
            $gambar_query = mysqli_query($conn, "SELECT image FROM stock WHERE idbarang = '$idb'");
            $get = mysqli_fetch_array($gambar_query);
            if(!empty($get['image']) && file_exists('uploads/'.$get['image'])){
                unlink('uploads/'.$get['image']);
            }
            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$image);
            $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi', lokasi = '$lokasi', image = '$image' WHERE idbarang = '$idb'"); // <-- DIPERBARUI
        }
    } else {
        $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi', lokasi = '$lokasi' WHERE idbarang = '$idb'"); // <-- DIPERBARUI
    }
    
    if(isset($update) && $update){
        header('location:index.php');
    }
}


// Hapus barang dari stock (dan semua riwayatnya)
if(isset($_POST['hapusbarang'])) {
    $idb = (int)$_POST['idb'];
    $gambar_query = mysqli_query($conn, "SELECT image FROM stock WHERE idbarang = '$idb'");
    if($get = mysqli_fetch_array($gambar_query)) {
        if(!empty($get['image'])) {
            $img_path = 'uploads/' . $get['image'];
            if(file_exists($img_path)){ unlink($img_path); }
        }
    }
    mysqli_query($conn, "DELETE FROM masuk WHERE idbarang = '$idb'");
    mysqli_query($conn, "DELETE FROM keluar WHERE idbarang = '$idb'");
    $hapus_stock = mysqli_query($conn, "DELETE FROM stock WHERE idbarang = '$idb'");

    if($hapus_stock){
        header('location:index.php');
    }
}

// 4. FUNGSI MANAJEMEN PENGGUNA

// Menambah pengguna baru (admin/user)
if(isset($_POST['addadmin'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = mysqli_prepare($conn, "INSERT INTO login (email, password, role) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $email, $hashed_password, $role);
    if(mysqli_stmt_execute($stmt)){
        header('location:admin.php');
    }
}


// Menghapus admin
if(isset($_POST['hapusadmin'])) {
    $id = $_POST['id'];
    $querydelete = mysqli_query($conn, "DELETE FROM login WHERE iduser = '$id'");
    if($querydelete){
        header('location:admin.php');
    }
}


// =================================================================== //
// KODE BARU UNTUK FITUR PEMINJAMAN BARANG
// =================================================================== //

// Menambah Peminjaman Baru
if(isset($_POST['pinjambarang'])){
    $idbarang = $_POST['barangnya'];
    $qty = $_POST['qty'];
    $peminjam = $_POST['peminjam'];

    // Cek ketersediaan stok
    $stok_saat_ini = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idbarang'");
    $stok_array = mysqli_fetch_array($stok_saat_ini);
    $stok = $stok_array['stock'];

    if($stok >= $qty){
        // Kurangi stok
        $stok_baru = $stok - $qty;

        // Mulai transaksi database
        mysqli_begin_transaction($conn);

        try {
            // Catat ke tabel peminjaman
            $insert_pinjam = mysqli_query($conn, "INSERT INTO peminjaman (idbarang, qty, peminjam) VALUES ('$idbarang', '$qty', '$peminjam')");
            
            // Update jumlah stok di tabel utama
            $update_stock = mysqli_query($conn, "UPDATE stock SET stock='$stok_baru' WHERE idbarang='$idbarang'");
            
            // Jika semua query berhasil, simpan perubahan
            mysqli_commit($conn);
            header('location:peminjaman.php');
        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn); // Batalkan jika ada error
            echo 'Gagal memproses peminjaman.';
        }

    } else {
        // Jika stok tidak cukup
        echo '<script>alert("Stok tidak mencukupi untuk dipinjam."); window.location.href="peminjaman.php";</script>';
    }
}

// Menyelesaikan Peminjaman (Saat Barang Dikembalikan)
if(isset($_POST['barangdikembalikan'])){
    $idpeminjaman = $_POST['idpeminjaman'];
    $idbarang = $_POST['idbarang'];
    $qty = $_POST['qty'];

    // 1. Update status di tabel peminjaman
    $update_status = mysqli_query($conn, "UPDATE peminjaman SET status='Kembali', tanggalkembali=NOW() WHERE idpeminjaman='$idpeminjaman'");

    // 2. Kembalikan jumlah stok ke tabel stock
    $stok_saat_ini = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idbarang'");
    $stok_array = mysqli_fetch_array($stok_saat_ini);
    $stok = $stok_array['stock'];
    $stok_baru = $stok + $qty;

    $update_stock = mysqli_query($conn, "UPDATE stock SET stock='$stok_baru' WHERE idbarang='$idbarang'");
    
    // Redirect jika berhasil
    if($update_status && $update_stock){
        header('location:peminjaman.php');
    }
}

// FUNGSI UNTUK MENAMBAH BARANG BERDASARKAN EVENT
if(isset($_POST['addnewevent'])){
    $nama_event = $_POST['nama_event'];
    $penanggung_jawab = $_POST['penanggung_jawab'];
    
    // Mulai transaksi
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Masukkan data ke tabel event
        $insert_event = mysqli_query($conn, "INSERT INTO event (nama_event, penanggung_jawab) VALUES ('$nama_event', '$penanggung_jawab')");
        if (!$insert_event) throw new Exception("Gagal menyimpan event.");
        
        $id_event_baru = mysqli_insert_id($conn);
        
        // Ambil data barang dari form
        $barangnya = $_POST['barangnya'];
        $qty = $_POST['qty'];
        
        // 2. Looping untuk setiap barang yang ditambahkan
        for($i = 0; $i < count($barangnya); $i++){
            $idbarang = $barangnya[$i];
            $jumlah = $qty[$i];
            
            // Masukkan ke detail_event
            $insert_detail = mysqli_query($conn, "INSERT INTO detail_event (id_event, idbarang, qty) VALUES ('$id_event_baru', '$idbarang', '$jumlah')");
            if (!$insert_detail) throw new Exception("Gagal menyimpan detail barang.");

            // Masukkan juga ke tabel 'masuk' agar history tetap tercatat
            $keterangan_masuk = "Pemasukan dari event: " . $nama_event;
            $insert_masuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$idbarang', '$keterangan_masuk', '$jumlah')");
            if (!$insert_masuk) throw new Exception("Gagal mencatat di barang masuk.");
            
            // Panggil fungsi recalculateStock untuk update stok utama
            recalculateStock($idbarang, $conn);
        }
        
        // Jika semua berhasil, commit transaksi
        mysqli_commit($conn);
        header('location:event.php');
        
    } catch (Exception $e) {
        // Jika ada yang gagal, batalkan semua
        mysqli_rollback($conn);
        echo '<script>alert("Terjadi kesalahan: ' . $e->getMessage() . '"); window.location.href="masuk_event.php";</script>';
    }
}

// FUNGSI UNTUK PENGEMBALIAN BARANG DARI EVENT
if(isset($_POST['kembalikan_barang_event'])){
    $id_detail = $_POST['id_detail'];
    $idbarang = $_POST['idbarang'];
    $id_event = $_POST['id_event'];
    $qty_kembali_sekarang = (int)$_POST['qty_kembali_sekarang'];
    $keterangan_baru = $_POST['keterangan'];

    // Ambil data detail event saat ini
    $detail_query = mysqli_query($conn, "SELECT * FROM detail_event WHERE id_detail = '$id_detail'");
    $detail_data = mysqli_fetch_array($detail_query);
    
    $qty_keluar = $detail_data['qty'];
    $qty_sudah_kembali = $detail_data['qty_kembali'];
    $keterangan_lama = $detail_data['keterangan'];

    // Hitung total yang sudah kembali
    $total_kembali = $qty_sudah_kembali + $qty_kembali_sekarang;

    // Tentukan status baru
    $status_baru = '';
    if($total_kembali >= $qty_keluar){
        $status_baru = 'Selesai';
    } else {
        $status_baru = 'Kembali Sebagian';
    }

    // Gabungkan keterangan
    $keterangan_final = $keterangan_lama;
    if(!empty($keterangan_baru)){
        $keterangan_final .= "\n[" . date("Y-m-d H:i") . "] " . $keterangan_baru;
    }

    // Mulai Transaksi
    mysqli_begin_transaction($conn);

    try {
        // 1. Update tabel detail_event
        $update_detail = mysqli_query($conn, "UPDATE detail_event SET 
            qty_kembali = '$total_kembali', 
            status_pengembalian = '$status_baru',
            keterangan = '".addslashes($keterangan_final)."'
            WHERE id_detail = '$id_detail'");

        if (!$update_detail) throw new Exception("Gagal update detail event.");

        // 2. Tambahkan stok kembali ke tabel utama (jika ada yang kembali)
        if($qty_kembali_sekarang > 0){
            $stok_query = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang = '$idbarang'");
            $stok_data = mysqli_fetch_array($stok_query);
            $stok_sekarang = $stok_data['stock'];
            $stok_baru = $stok_sekarang + $qty_kembali_sekarang;
            
            $update_stock = mysqli_query($conn, "UPDATE stock SET stock = '$stok_baru' WHERE idbarang = '$idbarang'");
            if (!$update_stock) throw new Exception("Gagal update stok barang.");
        }
        
        // Commit jika semua berhasil
        mysqli_commit($conn);
        header('location:detail_event.php?id=' . $id_event);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo '<script>alert("Terjadi kesalahan: ' . $e->getMessage() . '"); window.location.href="detail_event.php?id=' . $id_event . '";</script>';
    }
}
// FUNGSI UNTUK UPDATE DETAIL BARANG PADA EVENT
if(isset($_POST['update_detail_event'])){
    $id_detail = $_POST['id_detail'];
    $idbarang = $_POST['idbarang'];
    $id_event = $_POST['id_event'];
    $qty_baru = (int)$_POST['qty_baru'];
    $keterangan_update = $_POST['keterangan_update'];

    // Ambil data qty lama dari database untuk perbandingan
    $detail_query = mysqli_query($conn, "SELECT qty FROM detail_event WHERE id_detail = '$id_detail'");
    $detail_data = mysqli_fetch_array($detail_query);
    $qty_lama = (int)$detail_data['qty'];

    // Hitung selisih qty untuk penyesuaian stok
    $selisih = $qty_lama - $qty_baru;

    // Mulai Transaksi
    mysqli_begin_transaction($conn);

    try {
        // 1. Update tabel detail_event
        $update_detail = mysqli_query($conn, "UPDATE detail_event SET 
            qty = '$qty_baru', 
            keterangan = '".addslashes($keterangan_update)."'
            WHERE id_detail = '$id_detail'");

        if (!$update_detail) throw new Exception("Gagal update detail event.");

        // 2. Sesuaikan stok barang di tabel utama
        $stok_query = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang = '$idbarang'");
        $stok_data = mysqli_fetch_array($stok_query);
        $stok_sekarang = (int)$stok_data['stock'];
        $stok_baru = $stok_sekarang + $selisih; // Jika qty baru lebih kecil, stok bertambah. Jika lebih besar, stok berkurang.
        
        $update_stock = mysqli_query($conn, "UPDATE stock SET stock = '$stok_baru' WHERE idbarang = '$idbarang'");
        if (!$update_stock) throw new Exception("Gagal update stok barang.");
        
        // Commit jika semua berhasil
        mysqli_commit($conn);
        header('location:detail_event.php?id=' . $id_event);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo '<script>alert("Terjadi kesalahan: ' . $e->getMessage() . '"); window.location.href="detail_event.php?id=' . $id_event . '";</script>';
    }
}
?>

