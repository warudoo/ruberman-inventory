<?php
require 'includes/function.php';
require 'includes/cek.php';

$id_event = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id_event == 0) die("ID Event tidak valid.");

$event_query = mysqli_query($conn, "SELECT * FROM event WHERE id_event = '$id_event'");
$event_data = mysqli_fetch_array($event_query);
if(!$event_data) die("Event tidak ditemukan.");

$currentPage = 'event'; // Variabel untuk menandai halaman aktif di sidebar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Detail Event: <?=$event_data['nama_event'];?></title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="assets/css/responsive.css" rel="stylesheet" />
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
                    <h1 class="mt-4">Detail Event: <?=$event_data['nama_event'];?></h1>
                    <p>Penanggung Jawab: <?=$event_data['penanggung_jawab'];?></p>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-tasks"></i>
                            Daftar Barang untuk Dikembalikan
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Qty Keluar</th>
                                        <th>Qty Kembali</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $detail_query = mysqli_query($conn, "SELECT de.*, s.namabarang FROM detail_event de JOIN stock s ON de.idbarang=s.idbarang WHERE de.id_event = '$id_event'");
                                while($d = mysqli_fetch_array($detail_query)){
                                ?>
                                <tr>
                                    <td><?=$d['namabarang'];?></td>
                                    <td><?=$d['qty'];?></td>
                                    <td><?=$d['qty_kembali'];?></td>
                                    <td>
                                        <?php if($d['status_pengembalian'] == 'Selesai'): ?>
                                            <span class="badge badge-success">Selesai</span>
                                        <?php elseif($d['status_pengembalian'] == 'Kembali Sebagian'): ?>
                                            <span class="badge badge-warning">Kembali Sebagian</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Belum Kembali</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?=nl2br($d['keterangan']);?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#update<?=$d['id_detail'];?>">
                                            Update
                                        </button>
                                        
                                        <?php if($d['status_pengembalian'] != 'Selesai'): ?>
                                        <button type="button" class="btn btn-primary btn-sm mb-1" data-toggle="modal" data-target="#kembalikan<?=$d['id_detail'];?>">
                                            Pengembalian
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                
                                <div class="modal fade" id="update<?=$d['id_detail'];?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Update Pengembalian: <?=$d['namabarang'];?></h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <p>Jumlah barang yang dibawa: <strong><?=$d['qty'];?></strong></p>
                                                <hr>
                                                <div class="form-group">
                                                    <label>Qty Barang Kembali (Perbaiki jika ada salah input):</label>
                                                    <input type="number" name="qty_kembali_baru" class="form-control" value="<?=$d['qty_kembali'];?>" required min="0" max="<?=$d['qty'];?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Keterangan:</label>
                                                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: 1 unit hilang, 2 unit rusak ringan" required></textarea>
                                                </div>
                                                <input type="hidden" name="id_detail" value="<?=$d['id_detail'];?>">
                                                <input type="hidden" name="idbarang" value="<?=$d['idbarang'];?>">
                                                <input type="hidden" name="id_event" value="<?=$id_event;?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-warning" name="update_pengembalian_event">Update Data Pengembalian</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                                <div class="modal fade" id="kembalikan<?=$d['id_detail'];?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Form Pengembalian: <?=$d['namabarang'];?></h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <p>Jumlah barang yang dibawa: <strong><?=$d['qty'];?></strong></p>
                                                    <p>Jumlah sudah kembali: <strong><?=$d['qty_kembali'];?></strong></p>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label>Jumlah yang dikembalikan sekarang:</label>
                                                        <input type="number" name="qty_kembali_sekarang" class="form-control" required min="0" max="<?=$d['qty'] - $d['qty_kembali'];?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="keterangan">Keterangan (jika ada barang hilang/rusak):</label>
                                                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                                                    </div>
                                                    <input type="hidden" name="id_detail" value="<?=$d['id_detail'];?>">
                                                    <input type="hidden" name="idbarang" value="<?=$d['idbarang'];?>">
                                                    <input type="hidden" name="id_event" value="<?=$id_event;?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary" name="kembalikan_barang_event">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
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