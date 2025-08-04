<?php
require 'includes/function.php';
require 'includes/cek.php';
?>
<html>
<head>
    <title>Laporan Stok Barang - Ruberman Inventory</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
        }
        .container {
            margin-top: 30px;
        }
        .header-section {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .header-section img {
            height: 50px;
        }
        .header-section h2 {
            margin: 0;
            font-weight: 700;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            font-size: 1.2rem;
            padding: 15px 20px;
        }
        #mauexport thead th {
            background-color: #f2f4f6;
            color: #333;
            font-weight: 600;
        }
        .dt-buttons .btn {
            background-color: #28a745 !important;
            color: white !important;
            border-radius: 8px !important;
            border: none !important;
            padding: 10px 20px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }
        .dt-buttons .btn:hover {
            background-color: #218838 !important;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="header-section">
        <div>
            <h2><img src="assets/img/ruberman.png" alt="Logo"> Laporan Stok Barang</h2>
            <p class="mb-0">Tanggal Ekspor: <?= date("d M Y"); ?></p>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Data Inventaris Barang
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="mauexport" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Nama Barang</th>
                        <th>Deskripsi</th>
                        <th>Stock</th>
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
                    ?>
                    <tr>
                        <td><?=$i++;?></td>
                        <td><?= htmlspecialchars($namabarang);?></td>
                        <td><?= htmlspecialchars($deskripsi);?></td>
                        <td><?= htmlspecialchars($stock);?></td>
                    </tr>
                    <?php }; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
	
<script>
$(document).ready(function() {
    $('#mauexport').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Laporan Stok Barang - Ruberman Inventory',
                text: '<i class="fas fa-file-excel"></i> Export ke Excel'
            },
            {
                extend: 'pdfHtml5',
                title: 'Laporan Stok Barang - Ruberman Inventory',
                text: '<i class="fas fa-file-pdf"></i> Export ke PDF'
            }
        ]
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

</body>
</html>