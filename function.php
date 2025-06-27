<?php
session_start();

// membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","inventory_ruberman");


// menambah barang baru
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    // soal gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; // nama gambar
    $dot = explode('.',$nama); 
    $ekstensi = strtolower(end($dot));
    $ukuran = $_FILES['file']['size']; 
    $file_tmp = $_FILES['file']['tmp_name']; 

    // penamaan file
    $image =  md5(uniqid($nama,true). time()).'.'.$ekstensi;

    // validasi jika sudah atau belum
    $cek = mysqli_query($conn, "SELECT * FROM stock WHERE namabarang = '$namabarang'");
    $hitung = mysqli_num_rows($cek);


    if($hitung<1) {
        // jika belum ada

        //proses upload gambar
        if(in_array($ekstensi, $allowed_extension) === true) {
            // validasi ukuran file
            if($ukuran < 15000000) {
                move_uploaded_file($file_tmp, 'images/'.$image); 

                $addtotable = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, image) VALUES ('$namabarang', '$deskripsi', '$stock', '$image')");
                if($addtotable){
                header('location:index.php');
                }else{ 
                    echo "Gagal";
                    header('location:index.php');
                }
            } else{
                // jika ukuran file lebih dari 15mb
                 echo '
                <script>
                    alert("Ukuran file terlalu besar");
                    window.location.href="index.php";
                </script>
                ';
                }
            } else{
                // jika filenya tidak png / jpg
                 echo '
                <script>
                    alert(File harus png / jpg);");
                    window.location.href="index.php";
                </script>
                ';
                }
            } else{
                // jika  sudah ada
                echo '
                <script>
                    alert("Barang sudah ada");
                    window.location.href="index.php";
                </script>
                ';
            }
    };


// menambah barang masuk
if(isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

    $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$barangnya', '$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock = '$tambahkanstocksekarangdenganquantity' WHERE idbarang = '$barangnya'");
    if($addtomasuk&&$updatestockmasuk){
        
    header('location:index.php');
    }else{ 
        echo "Gagal menambah data";
        header('location:index.php');
 
    }
 
    };

    // menambah barang keluar 

    if(isset($_POST['addbarangkeluar'])) {
        $barangnya = $_POST['barangnya'];
        $penerima = $_POST['penerima'];
        $qty = $_POST['qty'];
    
        $cekstocksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$barangnya'");
        $ambildatanya = mysqli_fetch_array($cekstocksekarang);
    
        $stocksekarang = $ambildatanya['stock'];
        if($stocksekarang >= $qty) {
            // kalau barangnya cukup
        $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;
    
        $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$barangnya', '$penerima', '$qty')");
        $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock = '$tambahkanstocksekarangdenganquantity' WHERE idbarang = '$barangnya'");
        if($addtokeluar&&$updatestockmasuk){
            
        }else{ 
            echo "Gagal menambah data";
            header('location:keluar.php');
        }

        }else {
            // kalau barangnya tidak cukup
            echo '
            <script>
                alert("Stock saat ini tidak mencukupi");
                window.location.href="keluar.php";
            </script>
            ';
        }
    };
    
    // update info barang
    if(isset($_POST['updatebarang'])) {
        $idb = $_POST['idb'];
        $namabarang = $_POST['namabarang'];
        $deskripsi = $_POST['deskripsi'];

        // soal gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; // nama gambar
    $dot = explode('.',$nama); 
    $ekstensi = strtolower(end($dot));
    $ukuran = $_FILES['file']['size']; 
    $file_tmp = $_FILES['file']['tmp_name']; 

    // penamaan file
    $image =  md5(uniqid($nama,true). time()).'.'.$ekstensi;

    if($ukuran==0) {
        // jika tidak ingin upload
          $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi' WHERE idbarang = '$idb'");
        if($update){
            header('location:index.php');
        }else{
            echo 'Gagal';
            header('location:index.php');
        }
        
    } else {
        // jika ingin upload
        move_uploaded_file($file_tmp, 'images/'.$image);
          $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi', image = '$image' WHERE idbarang = '$idb'");
        if($update){
            header('location:index.php');
        }else{
            echo 'Gagal';
            header('location:index.php');
        }

    }
        
 };

    // hapus barang dari stock
    if(isset($_POST['hapusbarang'])) {
        $idb = $_POST['idb'];

        $gambar = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
        $get = mysqli_fetch_array($gambar);
        $img = 'images/'.$get['image'];
        unlink($img);

        $hapus = mysqli_query($conn, "DELETE FROM stock WHERE idbarang = '$idb'");
        if($hapus){
            header('location:index.php');
        }else{
            echo 'Gagal';
            header('location:index.php');
        }
    };


    // mengubah data barang masuk
    if(isset($_POST['updatebarangmasuk'])) {
        $idb = $_POST['idb'];
        $idm = $_POST['idm'];
        $deskripsi = $_POST['keterangan'];
        $qty = $_POST['qty'];

        $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
        $stocknya = mysqli_fetch_array($lihatstock);
        $stockskrg= $stocknya['stock'];

        $qtyskrg = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk = '$idm'");
        $qtynya = mysqli_fetch_array($qtyskrg);
        $qtyskrg = $qtynya['qty'];

        if($qty > $qtyskrng) {
            $selisih = $qty-$qtyskrg;
            $kurangin = $stockskrg+$selisih;
            $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' where idbarang = '$idb'"); 
            $updatenya = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan='$deskripsi' WHERE idmasuk = '$idm'");
                if($kurangistocknya && $updatenya) {
                    header('location:masuk.php');
                }else{ 
                    echo 'Gagal';
                    header('location:masuk.php');
                } 
             
        }else{
            $selisih = $qtyskrg-$qty;
            $kurangin = $stockskrg-$selisih;
            $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' where idbarang = '$idb'");
            $updatenya = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan='$deskripsi' WHERE idmasuk = '$idm'");
                if($kurangistocknya&&$updatenya) {
                    header('location:masuk.php');
                }else{ 
                    echo 'Gagal';
                    header('location:masuk.php');   
                }
        }

    }


    // menghapus barang masuk

    if(isset($_POST['hapusbarangmasuk'])) {
        $idb = $_POST['idb'];
        $qty = $_POST['kty'];
        $idm = $_POST['idm'];

        $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
        $data = mysqli_fetch_array($getdatastock);
        $stock = $data['stock'];

        $selisih = $stock - $qty;

        $update = mysqli_query($conn, "UPDATE stock SET stock = '$selisih' WHERE idbarang = '$idb'");
        $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk = '$idm'");

        if($update && $hapusdata) {
            header('location:masuk.php');
        
    } else {
        header('location:masuk.php');

    }

}

// mengubah data barang keluar

if(isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg= $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "SELECT * FROM keluar WHERE idkeluar = '$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty > $qtyskrng) {
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' where idbarang = '$idb'"); 
        $updatenya = mysqli_query($conn, "UPDATE keluar SET qty = '$qty', penerima='$penerima' WHERE idkeluar = '$idk'");
            if($kurangistocknya && $updatenya) {
                header('location:keluar.php');
            }else{ 
                echo 'Gagal';
                header('location:keluar.php');
            } 
         
    }else{
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "UPDATE keluar SET qty = '$qty', penerima='$deskripsi' WHERE idkeluar = '$idk'");
            if($kurangistocknya && $updatenya) {
                header('location:keluar.php');
            }else{ 
                echo 'Gagal';
                header('location:keluar.php');
            }
    }

}

// menghapus barang keluar

 if(isset($_POST['hapusbarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idk'];

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock + $qty;

    $update = mysqli_query($conn, "UPDATE stock SET stock = '$selisih' WHERE idbarang = '$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar = '$idk'");

    if($update && $hapusdata) {
        header('location:keluar.php');
    
} else {
    header('location:keluar.php');

}

}
    

// menambah admin baru

if(isset($_POST['addadmin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn, "INSERT INTO login (email, password) VALUES ('$email', '$password')");

    if($queryinsert){
        // if berhasil
        header('location:admin.php');
    } else{
        // if gagal  
        header('location:admin.php');
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


