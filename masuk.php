<?php
require 'includes/function.php';
require 'includes/cek.php';
$currentPage = 'masuk'; // Variabel untuk menandai halaman aktif
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Inventory Barang Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
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
                    <div class="custom-header-masuk mb-4 mt-4 d-flex align-items-center">
                        <div class="header-shape">
                            <i class="fas fa-box-open header-icon"></i>
                        </div>
                        <div class="ml-3">
                            <h1 class="header-title mb-0">Barang Masuk</h1>
                            <span class="header-subtitle">Kelola data barang masuk dengan mudah dan cepat</span>
                        </div>
                    </div>
                    <div class="card mb-4">
                    <style>
                        .custom-header-masuk {
                            position: relative;
                            background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%);
                            border-radius: 1.5rem;
                            padding: 1.5rem 2rem;
                            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                            margin-top: 2rem;
                            margin-bottom: 2rem;
                            z-index: 1;
                        }
                        .header-shape {
                            width: 70px;
                            height: 70px;
                            background: #fff;
                            border-radius: 50% 40% 60% 50% / 60% 50% 40% 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
                        }
                        .header-icon {
                            font-size: 2.5rem;
                            color: #007bff;
                        }
                        .header-title {
                            color: #fff;
                            font-weight: 700;
                            font-size: 2.2rem;
                            letter-spacing: 1px;
                        }
                        .header-subtitle {
                            color: #e3f2fd;
                            font-size: 1rem;
                            opacity: 0.85;
                        }
                        @media (max-width: 576px) {
                            .custom-header-masuk {
                                flex-direction: column;
                                align-items: flex-start;
                                padding: 1rem;
                            }
                            .header-shape {
                                width: 50px;
                                height: 50px;
                            }
                            .header-title {
                                font-size: 1.3rem;
                            }
                        }
                    </style>
                        <div class="card-header">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                        <i class="fas fa-box-open"></i> Tambah Barang Masuk
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
                                    $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM masuk m, stock s WHERE s.idbarang = m.idbarang");
                                    while($data=mysqli_fetch_array($ambilsemuadatastock)){
                                        $idb = $data['idbarang'];
                                        $idm = $data['idmasuk'];
                                        $tanggal = $data['tanggal'];
                                        $namabarang = $data['namabarang'];
                                        $qty = $data['qty'];
                                        $keterangan = $data['keterangan'];

                                         // cek ada gambar atay tidak
                                        $gambar = $data['image']; // ambil gambar
                                        if($gambar == null){ 
                                            // jika tidak ada gambar
                                            $img = '-';
                                        } else {
                                            // jika ada gambar
                                            $img = '<img src="uploads/'.$gambar.'" class="zoomable">';
                                        }
                                ?>
                                    <tr>
                                        <td><?=$tanggal;?></td>
                                        <td><?=$img;?></td>
                                        <td><?=$namabarang;?></td>
                                        <td><?=$qty;?></td>
                                        <td><?=$keterangan;?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit<?=$idm;?>">
                                            Edit
                                            </button>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?=$idm;?>">
                                            Delete
                                        </button>
                                        </td>
                                    </tr>
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
    </html>