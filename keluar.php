<?php
require 'includes/function.php';
require 'includes/cek.php';
$currentPage = 'keluar'; // Variabel untuk menandai halaman aktif
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Inventory Barang Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        .zoomable { width: 100px; }
        .zoomable:hover { transform: scale(2.5); transition: transform 0.3s ease; }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark" style="background-image: url('assets/img/blueback.jpg'); background-size: cover; background-repeat: no-repeat;">
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
                    <div class="custom-header-keluar mb-4 mt-4 d-flex align-items-center">
                        <div class="header-shape">
                            <i class="fas fa-dolly header-icon"></i>
                        </div>
                        <div class="ml-3">
                            <h1 class="header-title mb-0">Barang Keluar</h1>
                            <span class="header-subtitle">Kelola data barang keluar dengan mudah dan cepat</span>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#myModal">
                                <i class="fas fa-dolly"></i> Tambah Barang Keluar
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="datatablesSimple" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Gambar</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM keluar k JOIN stock s ON s.idbarang = k.idbarang");
                                    while($data = mysqli_fetch_array($ambilsemuadatastock)){
                                        $idk = $data['idkeluar'];
                                        $idb = $data['idbarang'];
                                        $tanggal = $data['tanggal'];
                                        $namabarang = $data['namabarang'];
                                        $qty = $data['qty'];
                                        $keterangan = $data['penerima'];
                                        $gambar = $data['image'];

                                        $img = ($gambar == null) ? 'Tidak Ada Gambar' : '<img src="uploads/'.$gambar.'" class="zoomable">';
                                ?>
                                    <tr>
                                        <td><?=$tanggal;?></td>
                                        <td><?=$img;?></td>
                                        <td><?=$namabarang;?></td>
                                        <td><?=$qty;?></td>
                                        <td class="keterangan-full"><?=$keterangan;?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?=$idk;?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete<?=$idk;?>">Delete</button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="edit<?=$idk;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Barang Keluar</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        <input type="text" name="penerima" value="<?=$keterangan;?>" class="form-control mb-3" required>
                                                        <input type="number" name="qty" value="<?=$qty;?>" class="form-control mb-3" required>
                                                        <input type="hidden" name="idb" value="<?=$idb;?>">
                                                        <input type="hidden" name="idk" value="<?=$idk;?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary" name="updatebarangkeluar">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="delete<?=$idk;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Hapus Barang?</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        Apakah anda yakin ingin menghapus <?=$namabarang;?>?
                                                        <input type="hidden" name="idb" value="<?=$idb;?>">
                                                        <input type="hidden" name="idk" value="<?=$idk;?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-danger" name="hapusbarangkeluar">Hapus</button> 
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
                        <div class="text-muted">Copyright &copy; PT Ruhama Berkah 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Barang Keluar</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <select name="barangnya" class="form-control mb-3">
                        <?php
                            $ambilsemuadatanya = mysqli_query($conn, "SELECT * FROM stock");
                            while($fetcharray = mysqli_fetch_array($ambilsemuadatanya)){
                        ?>
                            <option value="<?=$fetcharray['idbarang'];?>"><?=$fetcharray['namabarang'];?></option>
                        <?php } ?>
                        </select>
                        <input type="number" name="qty" placeholder="Quantity" class="form-control mb-3" required>
                        <input type="text" name="keterangan" placeholder="Keterangan" class="form-control mb-3" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="addbarangkeluar">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>