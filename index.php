<?php
require 'includes/function.php';
require 'includes/cek.php';

// Pastikan koneksi database sudah ada di function.php dan variabel $conn tersedia

// Proses tambah barang
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    // Proses upload gambar
    $allowed_ext = array('png', 'jpg', 'jpeg', 'gif');
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '.' . $ext;

    if (in_array($ext, $allowed_ext)) {
        $upload_path = 'upload/' . $new_file_name;
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $addtotable = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, image) VALUES ('$namabarang', '$deskripsi', '$stock', '$new_file_name')");
            if ($addtotable) {
                echo "<script>alert('Barang berhasil ditambahkan!');window.location='index.php';</script>";
            } else {
                echo "<script>alert('Gagal menambah barang ke database!');</script>";
            }
        } else {
            echo "<script>alert('Gagal upload gambar!');</script>";
        }
    } else {
        echo "<script>alert('Ekstensi file tidak didukung!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Inventory</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            .zoomable {
                width: 100px;
            }

            .zoomable:hover {
                transform: scale(2.5);
                transition: transform 0.3s ease;
            }

        </style>
        
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-image: url('assets/img/blueback.jpg'); background-size: cover; background-repeat: no-repeat;">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">
            <img src="assets/img/ruberman.png" alt="ruberman" height="40" class="d-inline-block align-top">
            </a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            </form>
            <!-- Navbar--> 
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #fff;">
                    <style>
                        #layoutSidenav_nav .sb-sidenav {
                            background: linear-gradient(135deg,rgb(15, 0, 221) 0%,rgba(32, 134, 218, 0.64) 100%) !important;
                        }
                        #layoutSidenav_nav .sb-sidenav .nav-link,
                        #layoutSidenav_nav .sb-sidenav .sb-nav-link-icon,
                        #layoutSidenav_nav .sb-sidenav .sb-sidenav-menu-heading {
                            color: #fff !important;
                        }
                        #layoutSidenav_nav .sb-sidenav .nav-link.active,
                        #layoutSidenav_nav .sb-sidenav .nav-link:hover {
                            background: rgba(30, 64, 175, 0.7) !important;
                            color: #fff !important;
                        }
                    </style>
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Jumlah Barang
                            </a>
                            <a class="nav-link" href="masuk.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Barang Masuk
                            </a>

                            <a class="nav-link" href="keluar.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Barang Keluar
                            </a> 
                            
                            <?php if($_SESSION['role'] == 'admin') { ?>
                            <a class="nav-link" href="admin.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                                Manajemen Admin
                            </a>
                            <?php } ?>
                            
                            <a class="nav-link" href="logout.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-door-open"></i></div>
                                Logout
                            </a>      
                            
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 mt-4">
                        <div class="welcome-card shadow-lg rounded-lg p-4 mb-4" style="background: #2563eb; color: #fff; border-left: 8px solid #1e40af;">
                            <h1 class="mt-2 mb-2" style="font-weight: 700; letter-spacing: 1px;">Selamat Datang di Sistem Inventaris Barang PT Ruhama Berkah!</h1>
                            <p class="lead mb-0" style="font-size: 1.2rem;">Kelola dan pantau stok barang Anda dengan mudah dan efisien.</p>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fas fa-plus"></i> Tambah Barang
                            </button>
                            <a href="export.php" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Data Table
                            </a>
                        </div>
                        <div class="card-body">

                            <?php
                            $ambildatastock = mysqli_query($conn, "SELECT * FROM stock WHERE stock <= 1");
                            while($fetch=mysqli_fetch_array($ambildatastock)){
                                $barang = $fetch['namabarang'];
                            ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>   
                                    <strong>Catatan : </strong> Stock Barang <?=$barang;?> Telah Habis
                                </div>
                            <?php
                                }   
                            ?>

                            <table class="table table-bordered" id="datatablesSimple" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Gambar</th>
                                        <th>Nama Barang</th>
                                        <th>Deskripsi</th>
                                        <th>Stock</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM stock");
                                    $i=1;
                                    while($data=mysqli_fetch_array($ambilsemuadatastock)){
                                        $namabarang = $data['namabarang'];
                                        $deskripsi = $data['deskripsi'];
                                        $stock = $data['stock'];
                                        $idb = $data['idbarang'];
                                        $gambar = $data['image'];
                                        if($gambar == null){ 
                                            $img = '-';
                                        } else {
                                            $img = '<img src="upload/'.$gambar.'" class="zoomable">';
                                        }
                                    ?>
                                    <tr>
                                        <td><?=$i++;?></td>
                                        <td><?=$img;?></td>
                                        <td><?=$namabarang;?></td>
                                        <td><?=$deskripsi;?></td>
                                        <td><?=$stock;?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit<?=$idb;?>">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?=$idb;?>">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <!--  Edit Modal -->
                                    <div class="modal fade" id="edit<?=$idb;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Barang</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" enctype="multipart/form-data">
                                                    <div class="modal-body">
                                                        <input type="text" name="namabarang" value="<?=$namabarang;?>" class="form-control" required>
                                                        <br>
                                                        <input type="text" name="deskripsi" value="<?=$deskripsi;?>" class="form-control" required>
                                                        <br>
                                                        <input type="file" name="file" class="form-control">
                                                        <br>
                                                        <input type="hidden" name="idb" value="<?=$idb;?>" class="form-control" required>
                                                        <button type="submit" class="btn btn-primary" name="updatebarang">Submit</button> 
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  Delete Modal -->
                                    <div class="modal fade" id="delete<?=$idb;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Hapus Barang</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        Apakah anda yakin ingin menghapus <?=$namabarang;?>?
                                                        <input type="hidden" name="idb" value="<?=$idb;?>">
                                                        <br>
                                                        <br>
                                                        <input type="text" name="deskripsi" value="<?=$deskripsi;?>" class="form-control" required>
                                                        <br>
                                                        <button type="submit" class="btn btn-primary" name="hapusbarang">Hapus</button> 
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    };
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; PT Ruhama Berkah 2025</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
     <!-- The Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Tambah Barang</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
         <form method="post" enctype="multipart/form-data">
        <div class="modal-body">
        <input type="text" name="namabarang" placeholder="Nama Barang" class="form-control" required>
        <br>
        <input type="text" name="deskripsi" placeholder="Deskripsi Barang" class="form-control" required>
        <br>
        <input type="number" name="stock" placeholder="Stock" class="form-control" required>
        <br>
        <input type="file" name="file" class="form-control" required>
        <br>
        <button type="submit" class="btn btn-primary" name="addnewbarang">submit</button> 
        </div>
        </form>
      </div>
    </div>
  </div>
</html>