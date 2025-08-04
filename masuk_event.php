<?php
require 'includes/function.php';
require 'includes/cek.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tambah Pemasukan per Event</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .item-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .item-row select, .item-row input {
            margin-right: 10px;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-image: url('assets/img/blueback.jpg');"></nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-box-open"></i></div> Stok Barang
                        </a>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-right"></i></div> Barang Masuk
                        </a>
                        <a class="nav-link" href="keluar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-left"></i></div> Barang Keluar
                        </a>
                        <a class="nav-link" href="peminjaman.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-heart"></i></div> Peminjaman Barang
                        </a>
                        <a class="nav-link active" href="event.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div> Kelola Event
                        </a>
                        <?php if($_SESSION['role'] == 'admin') { ?>
                        <a class="nav-link" href="admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div> Manajemen Admin
                        </a>
                        <?php } ?>
                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div> Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Tambah Pemasukan Barang per Event</h1>
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label for="nama_event">Nama Event/Acara</label>
                                    <input type="text" name="nama_event" id="nama_event" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="penanggung_jawab">Penanggung Jawab</label>
                                    <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="form-control" required>
                                </div>
                                <hr>
                                <h5>Detail Barang:</h5>
                                <div id="item-container">
                                    </div>
                                <button type="button" class="btn btn-secondary mt-2" onclick="addItemRow()">
                                    <i class="fas fa-plus"></i> Tambah Barang Lain
                                </button>
                                <hr>
                                <button type="submit" name="addnewevent" class="btn btn-primary">Simpan Event</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <template id="item-row-template">
        <div class="item-row">
            <select name="barangnya[]" class="form-control" style="width: 50%;" required>
                <option value="">Pilih Barang...</option>
                <?php
                $ambilsemuadata = mysqli_query($conn, "SELECT * FROM stock");
                while($fetcharray = mysqli_fetch_array($ambilsemuadata)){
                    $idbarangnya = $fetcharray['idbarang'];
                    $namabarangnya = $fetcharray['namabarang'];
                    $stocknya = $fetcharray['stock'];
                ?>
                <option value="<?=$idbarangnya;?>"><?=$namabarangnya;?> (Stok: <?=$stocknya;?>)</option>
                <?php } ?>
            </select>
            <input type="number" name="qty[]" placeholder="Jumlah" class="form-control" style="width: 20%;" required min="1">
            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </template>

    <script>
    function addItemRow() {
        const template = document.getElementById('item-row-template');
        const clone = template.content.cloneNode(true);
        document.getElementById('item-container').appendChild(clone);
    }
    // Tambahkan satu baris saat halaman dimuat
    window.onload = addItemRow;
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>