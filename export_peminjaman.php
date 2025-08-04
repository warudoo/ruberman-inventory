<?php
require 'includes/function.php';
require 'includes/cek.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman Barang - Ruberman Inventory</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
    <style>
        /* (Salin semua CSS dari <style> di file export.php yang sudah diperbaiki) */
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7f6; color: #333; }
        .container { margin-top: 2rem; margin-bottom: 2rem; }
        .report-header { background-color: #fff; padding: 2rem; border-radius: 0.5rem; margin-bottom: 2rem; text-align: center; border: 1px solid #ddd; }
        .report-header h2 { margin: 0; font-weight: 600; color: #FF416C; }
        .report-header p { margin: 5px 0 0; color: #777; }
        .data-tables { background-color: #fff; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        #mauexport thead th { background-color: #FF416C; color: white; font-weight: bold; }
        #mauexport tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .dt-buttons .btn { background-color: #FF4B2B !important; color: white !important; border: none !important; border-radius: 0.25rem !important; padding: 0.5rem 1rem !important; }
        .dt-buttons .btn:hover { background-color: #e83a1f !important; }
    </style>
</head>
<body>
<div class="container">
    <div class="report-header">
        <h2>Laporan Peminjaman Barang</h2>
        <p>Tanggal Ekspor: <?= date("d F Y"); ?></p>
    </div>
    <div class="data-tables">
        <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Tanggal Pinjam</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Peminjam</th>
                    <th>Status</th>
                    <th>Tanggal Kembali</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ambilsemuadata = mysqli_query($conn, "SELECT * FROM peminjaman p, stock s WHERE s.idbarang = p.idbarang");
                while($data = mysqli_fetch_array($ambilsemuadata)){
                ?>
                <tr>
                    <td><?= htmlspecialchars($data['tanggalpinjam']);?></td>
                    <td><?= htmlspecialchars($data['namabarang']);?></td>
                    <td><?= htmlspecialchars($data['qty']);?></td>
                    <td><?= htmlspecialchars($data['peminjam']);?></td>
                    <td><?= htmlspecialchars($data['status']);?></td>
                    <td><?= htmlspecialchars($data['tanggalkembali']);?></td>
                </tr>
                <?php }; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
    $('#mauexport').DataTable( {
        dom: 'Bfrtip',
        buttons: [ 'excelHtml5', 'pdfHtml5', 'print' ]
    } );
});
</script>
</body>
</html>