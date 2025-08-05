<?php
require 'includes/function.php';
require 'includes/cek.php';
$currentPage = 'peminjaman'; // Variabel untuk menandai halaman aktif
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Peminjaman Barang - Ruberman</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="assets/css/responsive.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-image: url('assets/img/blueback.jpg');">
        <a class="navbar-brand ps-3" href="index.php"><img src="assets/img/ruberman.png" alt="ruberman" height="40"></a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        
        <?php require 'includes/sidebar.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Peminjaman Barang</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                                <i class="fas fa-plus"></i> Tambah Peminjaman
                            </button>
                            <a href="export_peminjaman.php" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Data Peminjaman
                            </a>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal Pinjam</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Peminjam</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $ambilsemuadata = mysqli_query($conn, "SELECT * FROM peminjaman p, stock s WHERE s.idbarang = p.idbarang ORDER BY p.idpeminjaman DESC");
                                while($data = mysqli_fetch_array($ambilsemuadata)){
                                    $idp = $data['idpeminjaman'];
                                    $idb = $data['idbarang'];
                                    $tanggal = $data['tanggalpinjam'];
                                    $namabarang = $data['namabarang'];
                                    $qty = $data['qty'];
                                    $peminjam = $data['peminjam'];
                                    $status = $data['status'];
                                ?>
                                <tr>
                                    <td><?=$tanggal;?></td>
                                    <td><?=$namabarang;?></td>
                                    <td><?=$qty;?></td>
                                    <td><?=$peminjam;?></td>
                                    <td>
                                        <?php if($status == 'Dipinjam'): ?>
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Kembali</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                    <?php if($status == 'Dipinjam'): ?>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#kembali<?=$idp;?>">
                                            Selesaikan
                                        </button>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                                
                                <div class="modal fade" id="kembali<?=$idp;?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Selesaikan Peminjaman</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    Apakah barang <strong><?=$namabarang;?> (<?=$qty;?>)</strong> oleh <strong><?=$peminjam;?></strong> sudah dikembalikan?
                                                    <input type="hidden" name="idpeminjaman" value="<?=$idp;?>">
                                                    <input type="hidden" name="idbarang" value="<?=$idb;?>">
                                                    <input type="hidden" name="qty" value="<?=$qty;?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success" name="barangdikembalikan">Ya, Sudah Kembali</button>
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
                    <h4 class="modal-title">Tambah Peminjaman</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <select name="barangnya" class="form-control mb-3">
                            <?php
                            $ambilsemuadata = mysqli_query($conn, "SELECT * FROM stock WHERE stock > 0");
                            while($fetcharray = mysqli_fetch_array($ambilsemuadata)){
                                $namabarangnya = $fetcharray['namabarang'];
                                $idbarangnya = $fetcharray['idbarang'];
                                $stocknya = $fetcharray['stock'];
                            ?>
                            <option value="<?=$idbarangnya;?>"><?=$namabarangnya;?> (Stok: <?=$stocknya;?>)</option>
                            <?php } ?>
                        </select>
                        <input type="number" name="qty" placeholder="Jumlah" class="form-control mb-3" required min="1">
                        <input type="text" name="peminjam" placeholder="Nama Peminjam" class="form-control mb-3" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="pinjambarang">Pinjam</button>
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