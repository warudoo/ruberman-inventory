<?php
require 'includes/function.php';
require 'includes/cek.php';
$currentPage = 'event'; // Menandai menu Event sebagai halaman aktif
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tambah Event Barang Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .item-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .item-row select { flex: 3; }
        .item-row input { flex: 1; }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-image: url('assets/img/blueback.jpg');">
        <a class="navbar-brand ps-3" href="index.php"><img src="assets/img/ruberman.png" alt="ruberman" height="40"></a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        <?php require 'includes/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Tambah Event Barang Keluar</h1>
                    <div class="card my-4">
                        <div class="card-header">
                            <a href="event.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label for="nama_event" class="form-label">Nama Event/Acara</label>
                                    <input type="text" name="nama_event" id="nama_event" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="penanggung_jawab" class="form-label">Penanggung Jawab</label>
                                    <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="form-control" required>
                                </div>
                                <hr>
                                <h5>Detail Barang:</h5>
                                <div id="item-container"></div>
                                <button type="button" class="btn btn-info mt-2" onclick="addItemRow()">
                                    <i class="fas fa-plus"></i> Tambah Barang Lain
                                </button>
                                <hr>
                                <button type="submit" name="addnewevent" class="btn btn-primary">Simpan Event</button>
                            </form>
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

    <!-- Template item row -->
    <template id="item-row-template">
        <div class="item-row">
            <select name="barangnya[]" class="form-select" required>
                <option value="" selected disabled>Pilih Barang...</option>
                <?php
                $ambilsemuadata = mysqli_query($conn, "SELECT * FROM stock WHERE stock > 0 ORDER BY namabarang ASC");
                while($fetcharray = mysqli_fetch_array($ambilsemuadata)){
                    $idbarangnya = $fetcharray['idbarang'];
                    $namabarangnya = $fetcharray['namabarang'];
                    $stocknya = $fetcharray['stock'];
                ?>
                <option value="<?=$idbarangnya;?>" data-stock="<?=$stocknya;?>">
                    <?=$namabarangnya;?> (Stok: <?=$stocknya;?>)
                </option>
                <?php } ?>
            </select>
            <input type="number" name="qty[]" placeholder="Jumlah" class="form-control" required min="1">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </template>

    <!-- Script untuk tambah item dan validasi stok -->
    <script>
    function addItemRow() {
        const template = document.getElementById('item-row-template');
        const clone = template.content.cloneNode(true);
        document.getElementById('item-container').appendChild(clone);
    }

    window.onload = addItemRow;

    // Validasi stok saat user isi qty
    document.addEventListener('input', function(e) {
        if (e.target.name === 'qty[]') {
            const qtyInput = e.target;
            const row = qtyInput.closest('.item-row');
            const select = row.querySelector('select');
            const selectedOption = select.options[select.selectedIndex];
            const stok = parseInt(selectedOption.getAttribute('data-stock')) || 0;
            const jumlah = parseInt(qtyInput.value) || 0;

            if (jumlah > stok) {
                alert(`Jumlah melebihi stok tersedia (${stok}) untuk barang ini.`);
                qtyInput.value = '';
                qtyInput.focus();
            }
        }
    });
    </script>

    <!-- Vendor Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>
