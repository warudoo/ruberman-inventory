<?php
session_start();

// membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","inventory_ruberman");


// FUNGSI UTAMA UNTUK SINKRONISASI STOK (PASTI AKURAT)
function recalculateStock($idbarang, $conn) {
    // 1. Hitung total semua barang yang pernah masuk
    $query_masuk = mysqli_query($conn, "SELECT SUM(qty) as total_masuk FROM masuk WHERE idbarang = '$idbarang'");
    $data_masuk = mysqli_fetch_assoc($query_masuk);
    $total_masuk = (int)$data_masuk['total_masuk'];

    // 2. Hitung total semua barang yang pernah keluar
    $query_keluar = mysqli_query($conn, "SELECT SUM(qty) as total_keluar FROM keluar WHERE idbarang = '$idbarang'");
    $data_keluar = mysqli_fetch_assoc($query_keluar);
    $total_keluar = (int)$data_keluar['total_keluar'];

    // 3. Hitung stok akhir yang seharusnya
    $stok_sebenarnya = $total_masuk - $total_keluar;

    // 4. Update tabel stock dengan angka yang benar
    mysqli_query($conn, "UPDATE stock SET stock = '$stok_sebenarnya' WHERE idbarang = '$idbarang'");
}


// Menambah barang baru (Stok awal = 0, harus diisi dari Barang Masuk)
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock_awal = 0; // Stok awal harus 0 untuk menjaga integritas data
    $image = '';

    if(isset($_FILES['file']) && $_FILES['file']['size'] > 0){
        $allowed_extension = array('png','jpg','jpeg');
        $nama = $_FILES['file']['name'];
        $dot = explode('.',$nama); 
        $ekstensi = strtolower(end($dot));
        $file_tmp = $_FILES['file']['tmp_name'];
        if(in_array($ekstensi, $allowed_extension)) {
            $image = md5(uniqid($nama,true). time()).'.'.$ekstensi;
            move_uploaded_file($file_tmp, 'uploads/'.$image);
        }
    }

    $addtotable = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, image) VALUES ('$namabarang', '$deskripsi', '$stock_awal', '$image')");
    if($addtotable){
        header('location:index.php?status=addsuccess');
    }
}


// Menambah barang masuk
if(isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$barangnya', '$penerima', '$qty')");
    if($addtomasuk){
        recalculateStock($barangnya, $conn); // Panggil rekalkulasi
        header('location:masuk.php?status=addsuccess');
    }
}

// Menambah barang keluar 
if(isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];
    
    $cekstocksekarang = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang = '$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);
    $stocksekarang = $ambildatanya['stock'];

    if($stocksekarang >= $qty) {
        $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$barangnya', '$penerima', '$qty')");
        if($addtokeluar){
            recalculateStock($barangnya, $conn); // Panggil rekalkulasi
            header('location:keluar.php?status=addsuccess');
        }
    } else {
        echo '<script>alert("Stock tidak mencukupi"); window.location.href="keluar.php";</script>';
    }
}

// Mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])) {
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $update_masuk = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan='$deskripsi' WHERE idmasuk = '$idm'");
    if($update_masuk) {
        recalculateStock($idb, $conn); // Panggil rekalkulasi
        header('location:masuk.php?status=updatesuccess');
    }
}

// Mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $update_keluar = mysqli_query($conn, "UPDATE keluar SET qty = '$qty', penerima='$penerima' WHERE idkeluar = '$idk'");
    if($update_keluar) {
        recalculateStock($idb, $conn); // Panggil rekalkulasi
        header('location:keluar.php?status=updatesuccess');
    }
}

// Menghapus data barang masuk
if(isset($_POST['hapusbarangmasuk'])) {
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];

    $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk = '$idm'");
    if($hapusdata) {
        recalculateStock($idb, $conn); // Panggil rekalkulasi
        header('location:masuk.php?status=deletesuccess');
    }
}

// Menghapus data barang keluar
if(isset($_POST['hapusbarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];

    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar = '$idk'");
    if($hapusdata) {
        recalculateStock($idb, $conn); // Panggil rekalkulasi
        header('location:keluar.php?status=deletesuccess');
    }
}
    
// Menambah pengguna baru (admin/user)
if(isset($_POST['addadmin'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Ambil data role dari form

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Gunakan prepared statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($conn, "INSERT INTO login (email, password, role) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $email, $hashed_password, $role);
    $exec = mysqli_stmt_execute($stmt);

    if($exec){
        // Jika berhasil
        header('location:admin.php?status=addsuccess');
    } else {
        // Jika gagal
        header('location:admin.php?status=addfailed');
    }
}

// menghapus admin
if(isset($_POST['hapusadmin'])) {
    $id = $_POST['id'];

    $querydelete = mysqli_query($conn, "DELETE FROM login WHERE iduser = '$id'");

    if($querydelete){
        // if berhasil
        header('location:admin.php');
    } else{
        // if gagal  
        header('location:admin.php');
    }
}
?>