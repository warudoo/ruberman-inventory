<?php
require 'includes/function.php';
require 'includes/cek.php';
$currentPage = 'index'; // Variabel untuk menandai halaman aktif
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Inventory Stok Barang - Ruberman</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="assets/css/styles.css" rel="stylesheet" /> 
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <style>
            .zoomable { width: 100px; }
            .zoomable:hover { transform: scale(2.5); transition: transform 0.3s ease; }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-image: url('assets/img/blueback.jpg'); background-size: cover; background-repeat: no-repeat;">
            <a class="navbar-brand ps-3" href="index.php">
                <img src="assets/img/ruberman.png" alt="ruberman" height="40" class="d-inline-block align-top">
            </a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        </nav>
        <div id="layoutSidenav">
            
            <?php require 'includes/sidebar.php'; ?>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="welcome-card shadow-lg rounded-lg p-4 my-4" style="background: #2563eb; color: #fff; border-left: 8px solid #1e40af;">
                            <h1 class="mt-2 mb-2" style="font-weight: 700; letter-spacing: 1px;">Selamat Datang di Sistem Inventaris Barang!</h1>
                            <p class="lead mb-0" style="font-size: 1.2rem;">Kelola dan pantau stok barang Anda dengan mudah dan efisien.</p>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                    <i class="fas fa-plus"></i> Tambah Barang
                                </button>
                                <a href="export.php" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Data
                                </a>
                            </div>
                            <div class="card-body">
                                <?php
                                $ambildatastock = mysqli_query($conn, "SELECT * FROM stock WHERE stock < 1");
                                while($fetch=mysqli_fetch_array($ambildatastock)){
                                    $barang = $fetch['namabarang'];
                                ?>
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>   
                                        <strong>Perhatian!</strong> Stok barang <?=$barang;?> telah habis.
                                    </div>
                                <?php } ?>

                                <table class="table table-bordered" id="datatablesSimple" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Gambar</th>
                                            <th>Nama Barang</th>
                                            <th>Deskripsi</th>
                                            <th>Stock</th>
                                            <th>Lokasi</th>
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
                                        $lokasi = $data['lokasi'];
                                        $idb = $data['idbarang'];
                                        $gambar = $data['image'];
                                        
                                        if($gambar == null || $gambar == ''){ 
                                            $img = 'Tidak Ada Gambar';
                                        } else {
                                            $img = '<img src="uploads/'.$gambar.'" class="zoomable">';
                                        }
                                    ?>
                                    <tr>
                                        <td><?=$i++;?></td>
                                        <td><?=$img;?></td>
                                        <td><?=$namabarang;?></td>
                                        <td><?=$deskripsi;?></td>
                                        <td><?=$stock;?></td>
                                        <td><?=$lokasi;?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit<?=$idb;?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?=$idb;?>">Delete</button>
                                        </td>
                                    </tr>
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
                                                        <input type="text" name="lokasi" value="<?=$lokasi;?>" placeholder="Lokasi Barang" class="form-control" required>
                                                        <br>
                                                        <input type="file" name="file" class="form-control">
                                                        <br>
                                                        <input type="hidden" name="idb" value="<?=$idb;?>">
                                                        <button type="submit" class="btn btn-primary" name="updatebarang">Submit</button> 
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="delete<?=$idb;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Hapus Barang</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin menghapus <?=$namabarang;?>?
                                                        <input type="hidden" name="idb" value="<?=$idb;?>">
                                                        <br><br>
                                                        <button type="submit" class="btn btn-danger" name="hapusbarang">Hapus</button> 
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }; ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Ruberman Inventory 2025</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
        <div class="modal fade" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Barang Baru</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="text" name="namabarang" placeholder="Nama Barang" class="form-control" required>
                            <br>
                            <input type="text" name="deskripsi" placeholder="Deskripsi Barang" class="form-control" required>
                            <br>
                            <input type="number" name="stock" placeholder="Stok Awal" class="form-control" required min="0">
                            <br>
                            <input type="text" name="lokasi" placeholder="Lokasi Barang (e.g. Rak A1)" class="form-control" required>
                            <br>
                            <input type="file" name="file" class="form-control">
                            <br>
                            <button type="submit" class="btn btn-primary" name="addnewbarang">Submit</button> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <script src="js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>