<?php
session_start();

// 1. KONEKSI DATABASE
$conn = mysqli_connect("localhost","root","","inventory_ruberman");


// 2. FUNGSI UTAMA UNTUK SINKRONISASI STOK
function recalculateStock($idbarang, $conn) {
    $query_masuk = mysqli_query($conn, "SELECT SUM(qty) as total FROM masuk WHERE idbarang = '$idbarang'");
    $total_masuk = (int)mysqli_fetch_assoc($query_masuk)['total'];
    $query_keluar = mysqli_query($conn, "SELECT SUM(qty) as total FROM keluar WHERE idbarang = '$idbarang'");
    $total_keluar = (int)mysqli_fetch_assoc($query_keluar)['total'];
    $stok_sebenarnya = $total_masuk - $total_keluar;
    mysqli_query($conn, "UPDATE stock SET stock = '$stok_sebenarnya' WHERE idbarang = '$idbarang'");
}


// 3. PROSES FORM UNTUK MANAJEMEN BARANG

// Menambah barang baru
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock_awal = (int)$_POST['stock'];
    $lokasi = $_POST['lokasi'];
    $image = '';

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
    
    $addtostock = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, lokasi, image) VALUES ('$namabarang', '$deskripsi', '0', '$lokasi', '$image')");
    
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
    $keterangan = $_POST['keterangan']; // Dianggap sebagai penerima
    $qty = (int)$_POST['qty'];
    
    // Diubah menjadi 'penerima' di DB, tapi 'keterangan' di UI
    $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$idbarang', '$keterangan', '$qty')");
    if($addtokeluar){
        recalculateStock($idbarang, $conn);
        header('location:keluar.php');
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

    $update_keluar = mysqli_query($conn, "UPDATE keluar SET qty = '$qty_baru', penerima='$penerima' WHERE idkeluar = '$idk'");
    if($update_keluar) {
        recalculateStock($idb, $conn);
        header('location:keluar.php');
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

// Update info barang
if(isset($_POST['updatebarang'])) {
    $idb = (int)$_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $lokasi = $_POST['lokasi'];

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
            $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi', lokasi = '$lokasi', image = '$image' WHERE idbarang = '$idb'");
        }
    } else {
        $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi', lokasi = '$lokasi' WHERE idbarang = '$idb'");
    }
    
    if(isset($update) && $update){
        header('location:index.php');
    }
}

// Hapus barang dari stock
if(isset($_POST['hapusbarang'])) {
    $idb = (int)$_POST['idb'];
    $gambar_query = mysqli_query($conn, "SELECT image FROM stock WHERE idbarang = '$idb'");
    if($get = mysqli_fetch_array($gambar_query)) {
        if(!empty($get['image']) && file_exists('uploads/' . $get['image'])){
            unlink('uploads/' . $get['image']);
        }
    }
    mysqli_query($conn, "DELETE FROM masuk WHERE idbarang = '$idb'");
    mysqli_query($conn, "DELETE FROM keluar WHERE idbarang = '$idb'");
    mysqli_query($conn, "DELETE FROM peminjaman WHERE idbarang = '$idb'");
    mysqli_query($conn, "DELETE FROM detail_event WHERE idbarang = '$idb'");
    $hapus_stock = mysqli_query($conn, "DELETE FROM stock WHERE idbarang = '$idb'");

    if($hapus_stock){
        header('location:index.php');
    }
}

// 4. FUNGSI MANAJEMEN PENGGUNA

// Menambah admin
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
    mysqli_query($conn, "DELETE FROM login WHERE iduser = '$id'");
    header('location:admin.php');
}

// =================================================================== //
// FITUR PEMINJAMAN BARANG (DENGAN RIWAYAT MASUK/KELUAR)
// =================================================================== //
if(isset($_POST['pinjambarang'])){
    $idbarang = (int)$_POST['barangnya'];
    $qty = (int)$_POST['qty'];
    $peminjam = $_POST['peminjam'];

    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "INSERT INTO peminjaman (idbarang, qty, peminjam) VALUES ('$idbarang', '$qty', '$peminjam')");
        $penerima_keluar = "Dipinjam oleh: " . $peminjam;
        mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$idbarang', '$penerima_keluar', '$qty')");
        recalculateStock($idbarang, $conn);
        mysqli_commit($conn);
        header('location:peminjaman.php');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo '<script>alert("Gagal memproses peminjaman."); window.location.href="peminjaman.php";</script>';
    }
}

if(isset($_POST['barangdikembalikan'])){
    $idpeminjaman = (int)$_POST['idpeminjaman'];
    $idbarang = (int)$_POST['idbarang'];
    $qty = (int)$_POST['qty'];

    $peminjam_query = mysqli_query($conn, "SELECT peminjam FROM peminjaman WHERE idpeminjaman='$idpeminjaman'");
    $peminjam_data = mysqli_fetch_array($peminjam_query);
    $peminjam = $peminjam_data['peminjam'];

    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "UPDATE peminjaman SET status='Kembali', tanggalkembali=NOW() WHERE idpeminjaman='$idpeminjaman'");
        $keterangan_masuk = "Pengembalian dari: " . $peminjam;
        mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$idbarang', '$keterangan_masuk', '$qty')");
        recalculateStock($idbarang, $conn);
        mysqli_commit($conn);
        header('location:peminjaman.php');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo '<script>alert("Gagal memproses pengembalian."); window.location.href="peminjaman.php";</script>';
    }
}

// =================================================================== //
// FITUR EVENT
// =================================================================== //
if(isset($_POST['addnewevent'])){
    $nama_event = $_POST['nama_event'];
    $penanggung_jawab = $_POST['penanggung_jawab'];
    
    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "INSERT INTO event (nama_event, penanggung_jawab) VALUES ('$nama_event', '$penanggung_jawab')");
        $id_event_baru = mysqli_insert_id($conn);
        
        $barangnya = $_POST['barangnya'];
        $qty = $_POST['qty'];
        
        for($i = 0; $i < count($barangnya); $i++){
            $idbarang = $barangnya[$i];
            $jumlah = $qty[$i];
            
            // Masukkan ke detail_event
            mysqli_query($conn, "INSERT INTO detail_event (id_event, idbarang, qty) VALUES ('$id_event_baru', '$idbarang', '$jumlah')");

            // --- PERUBAHAN DI SINI ---
            // Catat sebagai BARANG KELUAR, bukan barang masuk
            $penerima_keluar = "Untuk Event: " . $nama_event;
            mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$idbarang', '$penerima_keluar', '$jumlah')");
            // --- AKHIR PERUBAHAN ---
            
            recalculateStock($idbarang, $conn);
        }
        
        mysqli_commit($conn);
        header('location:event.php');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo '<script>alert("Terjadi kesalahan: ' . $e->getMessage() . '"); window.location.href="masuk_event.php";</script>';
    }
}

if(isset($_POST['kembalikan_barang_event'])){
    $id_detail = (int)$_POST['id_detail'];
    $idbarang = (int)$_POST['idbarang'];
    $id_event = (int)$_POST['id_event'];
    $qty_kembali_sekarang = (int)$_POST['qty_kembali_sekarang'];
    $keterangan_baru = $_POST['keterangan'];

    $detail_query = mysqli_query($conn, "SELECT * FROM detail_event de JOIN event e ON de.id_event = e.id_event WHERE de.id_detail = '$id_detail'");
    $detail_data = mysqli_fetch_array($detail_query);
    
    $qty_keluar = (int)$detail_data['qty'];
    $qty_sudah_kembali = (int)$detail_data['qty_kembali'];
    
    // --- PERBAIKAN LOGIKA DI SINI ---
    // Validasi agar jumlah kembali tidak melebihi jumlah keluar
    if (($qty_sudah_kembali + $qty_kembali_sekarang) > $qty_keluar) {
        echo '<script>
                alert("Error: Jumlah barang yang dikembalikan melebihi jumlah yang dibawa untuk event!");
                window.location.href="detail_event.php?id=' . $id_event . '";
              </script>';
        exit;
    }
    // --- AKHIR PERBAIKAN ---

    $keterangan_lama = $detail_data['keterangan'];
    $nama_event = $detail_data['nama_event'];
    $total_kembali = $qty_sudah_kembali + $qty_kembali_sekarang;
    $status_baru = ($total_kembali >= $qty_keluar) ? 'Selesai' : 'Kembali Sebagian';

    $keterangan_final = $keterangan_lama;
    if(!empty($keterangan_baru)){
        $keterangan_final .= "\n[" . date("Y-m-d H:i") . "] " . $keterangan_baru;
    }

    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "UPDATE detail_event SET qty_kembali = '$total_kembali', status_pengembalian = '$status_baru', keterangan = '".addslashes($keterangan_final)."' WHERE id_detail = '$id_detail'");

        if($qty_kembali_sekarang > 0){
            $keterangan_masuk = "Event: " . $nama_event;
            mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$idbarang', '$keterangan_masuk', '$qty_kembali_sekarang')");
            recalculateStock($idbarang, $conn);
        }
        
        mysqli_commit($conn);
        header('location:detail_event.php?id=' . $id_event);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo '<script>alert("Terjadi kesalahan: ' . $e->getMessage() . '"); window.location.href="detail_event.php?id=' . $id_event . '";</script>';
    }
}

if(isset($_POST['update_pengembalian_event'])){
    $id_detail = (int)$_POST['id_detail'];
    $idbarang = (int)$_POST['idbarang'];
    $id_event = (int)$_POST['id_event'];
    $qty_kembali_baru = (int)$_POST['qty_kembali_baru'];
    $keterangan_update = $_POST['keterangan_update'];

    $detail_query = mysqli_query($conn, "SELECT * FROM detail_event de JOIN event e ON de.id_event = e.id_event WHERE de.id_detail = '$id_detail'");
    $detail_data = mysqli_fetch_array($detail_query);
    $qty_keluar = (int)$detail_data['qty'];
    $qty_kembali_lama = (int)$detail_data['qty_kembali'];
    $nama_event = $detail_data['nama_event'];

    $selisih = $qty_kembali_baru - $qty_kembali_lama;

    $status_baru = '';
    if ($qty_kembali_baru >= $qty_keluar) { $status_baru = 'Selesai'; } 
    elseif ($qty_kembali_baru > 0) { $status_baru = 'Kembali Sebagian'; } 
    else { $status_baru = 'Belum Kembali'; }

   mysqli_begin_transaction($conn);
    try {
        // Update detail event
        mysqli_query($conn, "UPDATE detail_event SET qty_kembali = '$qty_kembali_baru', /* ... */ WHERE id_detail = '$id_detail'");

        // Jika ada selisih, buat transaksi penyesuaian
        if($selisih > 0){ // Berarti jumlah kembali ditambah
            $keterangan_masuk = "Koreksi Event: " . $nama_event;
            mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$idbarang', '$keterangan_masuk', '$selisih')");
        } elseif($selisih < 0) { // Berarti jumlah kembali dikurangi
            $penerima_keluar = "Koreksi Event: " . $nama_event;
            mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$idbarang', '$penerima_keluar', '".abs($selisih)."')");
        }

        if($selisih != 0){
            recalculateStock($idbarang, $conn);
        }
        
        mysqli_commit($conn);
        header('location:detail_event.php?id=' . $id_event);
    } catch (Exception $e) {
        // ... (error handling) ...
    }
}

if(isset($_POST['hapusevent'])){
    $id_event = (int)$_POST['id_event_to_delete'];

    mysqli_begin_transaction($conn);
    try {
        // 1. Ambil semua detail barang yang terkait dengan event ini
        $query_detail = mysqli_query($conn, "SELECT * FROM detail_event WHERE id_event = '$id_event'");
        
        while($detail = mysqli_fetch_array($query_detail)){
            $idbarang = $detail['idbarang'];
            $qty_keluar_event = (int)$detail['qty'];
            $qty_sudah_kembali = (int)$detail['qty_kembali'];

            // 2. Hitung jumlah bersih yang harus dikembalikan ke stok
            $qty_untuk_dikembalikan = $qty_keluar_event - $qty_sudah_kembali;

            // 3. Jika ada barang yang belum kembali, buat transaksi "masuk" untuk membatalkan pengeluaran
            if ($qty_untuk_dikembalikan > 0) {
                $keterangan_batal = "Pembatalan Event (ID: $id_event)";
                mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$idbarang', '$keterangan_batal', '$qty_untuk_dikembalikan')");
            }

            // 4. Perbarui stok untuk barang ini
            recalculateStock($idbarang, $conn);
        }

        // 5. Setelah semua stok dikembalikan, hapus detail event
        mysqli_query($conn, "DELETE FROM detail_event WHERE id_event = '$id_event'");

        // 6. Terakhir, hapus event utamanya
        mysqli_query($conn, "DELETE FROM event WHERE id_event = '$id_event'");

        mysqli_commit($conn);
        header('location:event.php');

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo '<script>alert("Gagal menghapus event: ' . $e->getMessage() . '"); window.location.href="event.php";</script>';
    }
}
?>