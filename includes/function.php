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

// Menambah barang baru (dengan pencatatan stok awal yang benar)
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock_awal = (int)$_POST['stock'];
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
    
    // Masukkan barang baru ke tabel stock dengan stok awal 0
    $addtostock = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, image) VALUES ('$namabarang', '$deskripsi', '0', '$image')");
    
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


// Update info barang (nama, deskripsi, gambar)
if(isset($_POST['updatebarang'])) {
    $idb = (int)$_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

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
            $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi', image = '$image' WHERE idbarang = '$idb'");
        }
    } else {
        $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi' WHERE idbarang = '$idb'");
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

?>