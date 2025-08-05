<?php
require 'includes/function.php';
require 'includes/cek.php';
$tanggal_hari_ini = date("d-m-Y");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Barang - Ruberman Inventory</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7f6; color: #333; }
        .container { margin-top: 2rem; margin-bottom: 2rem; }
        .report-header { background-color: #fff; padding: 2rem; border-radius: 0.5rem; margin-bottom: 2rem; text-align: center; border: 1px solid #ddd; }
        .report-header h2 { margin: 0; font-weight: 600; color: #2563eb; }
        .report-header p { margin: 5px 0 0; color: #777; }
        .data-tables { background-color: #fff; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        #mauexport thead th { background-color: #2563eb; color: white; font-weight: bold; }
        #mauexport tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .dt-buttons .btn { background-color: #1e40af !important; color: white !important; border: none !important; border-radius: 0.25rem !important; padding: 0.5rem 1rem !important; }
    </style>
</head>
<body>
<div class="container">
    <div class="report-header">
        <h2>Laporan Stok Barang Keseluruhan</h2>
        <p>Ruberman Inventory | Dicetak pada: <?= date("d F Y"); ?></p>
    </div>
    <div class="data-tables">
        <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Nama Barang</th>
                    <th>Deskripsi</th>
                    <th>Lokasi</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM stock");
                $i = 1;
                while($data = mysqli_fetch_array($ambilsemuadatastock)){
                ?>
                <tr>
                    <td><?=$i++;?></td>
                    <td><?= htmlspecialchars($data['namabarang']);?></td>
                    <td><?= htmlspecialchars($data['deskripsi']);?></td>
                    <td><?= htmlspecialchars($data['lokasi']);?></td>
                    <td><?= htmlspecialchars($data['stock']);?></td>
                </tr>
                <?php }; ?>
            </tbody>
        </table>
    </div>
</div>
    
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
    // Membuat nama file dinamis dengan tanggal
    var tanggal = '<?=$tanggal_hari_ini;?>';
    var namaFile = 'Laporan Inventory Ruberman - ' + tanggal;

    $('#mauexport').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: namaFile
            },
            {
                extend: 'pdfHtml5',
                title: 'Laporan Stok Barang Keseluruhan',
                filename: namaFile, // Menetapkan nama file PDF
                orientation: 'portrait',
                pageSize: 'A4',
                customize: function (doc) {
                    // --- PERBAIKAN UTAMA ADA DI SINI ---
                    // 1. Membuat tabel menjadi full width
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    
                    // 2. Kustomisasi Desain (seperti export_event.php)
                    doc.styles.title = {
                        color: '#2563eb', fontSize: '18', bold: true, alignment: 'center', margin: [0, 0, 0, 10]
                    };
                    doc.styles.tableHeader = {
                        bold: true, fontSize: 10, color: 'white', fillColor: '#2563eb', alignment: 'center'
                    };
                    
                    // 3. Menambahkan tanggal cetak di bawah judul
                    doc.content.splice(1, 0, {
                        text: 'Dicetak pada: <?= date("d F Y"); ?>',
                        alignment: 'center',
                        fontSize: 10,
                        margin: [0, 0, 0, 15]
                    });

                    // Menerapkan style ke header tabel
                    let tableHeader = doc.content[2].table.body[0];
                    for (var i = 0; i < tableHeader.length; i++) {
                        tableHeader[i].style = 'tableHeader';
                    }
                    
                    // Mengatur layout tabel agar memiliki garis
                    doc.content[2].layout = {
                        hLineWidth: function (i, node) { return 1; },
                        vLineWidth: function (i, node) { return 1; },
                        hLineColor: function (i, node) { return '#aaa'; },
                        vLineColor: function (i, node) { return '#aaa'; }
                    };

                    // 4. Menambahkan tanda tangan penanggung jawab di bagian bawah
                    doc.content.push({
                        text: '\n\n\nPenanggung Jawab Storage,\n\n\n___________________\n( Warud )',
                        alignment: 'right',
                        style: { fontSize: 10 }
                    });
                }
            },
            'print'
        ]
    });
});
</script>
</body>
</html>