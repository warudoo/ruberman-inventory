<?php
require 'includes/function.php';
require 'includes/cek.php';
$currentPage = 'event'; // Variabel untuk menandai halaman aktif
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Daftar Event - Ruberman Inventory</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
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
                    <h1 class="mt-4">Kelola Event Pemasukan Barang</h1>
                    <div class="card my-4">
                        <div class="card-header">
                            <a href="masuk_event.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Event Baru
                            </a>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Event</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $ambil_event = mysqli_query($conn, "
                                    SELECT e.*, 
                                    (SELECT COUNT(id_detail) FROM detail_event de WHERE de.id_event = e.id_event AND de.status_pengembalian != 'Selesai') as belum_kembali
                                    FROM event e ORDER BY e.tanggal_event DESC
                                ");
                                while($data = mysqli_fetch_array($ambil_event)){
                                    $idevent = $data['id_event'];
                                ?>
                                <tr>
                                    <td><?=$data['tanggal_event'];?></td>
                                    <td><?=$data['nama_event'];?></td>
                                    <td><?=$data['penanggung_jawab'];?></td>
                                    <td>
                                        <?php if($data['belum_kembali'] == 0): ?>
                                            <span class="badge badge-success">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Berlangsung</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="detail_event.php?id=<?=$idevent;?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="export_event.php?id=<?=$idevent;?>" class="btn btn-success btn-sm" target="_blank">
                                            <i class="fas fa-file-excel"></i> Export
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?=$idevent;?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                <div class="modal fade" id="delete<?=$idevent;?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Hapus Event?</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus event <strong><?=$data['nama_event'];?></strong>? <br><br>
                                                    <strong class="text-danger">Peringatan:</strong> Semua data barang keluar dan riwayat pengembalian terkait event ini akan dihapus secara permanen. Stok barang tidak akan dikembalikan secara otomatis.
                                                    <input type="hidden" name="id_event_to_delete" value="<?=$idevent;?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger" name="hapusevent">Ya, Hapus Event</button>
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
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>