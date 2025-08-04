<?php
require 'includes/function.php';
require 'includes/cek.php';

// Ambil ID Event dari URL
$id_event = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id_event == 0) die("ID Event tidak valid.");

// Ambil detail event
$event_query = mysqli_query($conn, "SELECT * FROM event WHERE id_event = '$id_event'");
$event_data = mysqli_fetch_array($event_query);
if(!$event_data) die("Event tidak ditemukan.");

$nama_event = $event_data['nama_event'];
$penanggung_jawab = $event_data['penanggung_jawab'];
$tanggal_event = date("d F Y", strtotime($event_data['tanggal_event']));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export Event: <?=$nama_event;?></title>
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
        .dt-buttons .btn:hover { background-color: #1c3899 !important; }
    </style>
</head>
<body>
<div class="container">
    <div class="report-header">
        <h2>Laporan Serah Terima Barang</h2>
        <p>
            <strong>Event:</strong> <?=$nama_event;?> | 
            <strong>Penanggung Jawab:</strong> <?=$penanggung_jawab;?> | 
            <strong>Tanggal:</strong> <?=$tanggal_event;?>
        </p>
    </div>
    <div class="data-tables">
        <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Deskripsi</th>
                    <th>Qty Barang</th>
                    <th>Lokasi</th>
                    <th>Keterangan</th>
                    <th>Checkin</th>
                    <th>Checkout</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ambil_detail = mysqli_query($conn, "SELECT s.namabarang, s.deskripsi, s.lokasi, de.qty FROM detail_event de JOIN stock s ON de.idbarang = s.idbarang WHERE de.id_event = '$id_event'");
                $i = 1;
                while($data = mysqli_fetch_array($ambil_detail)){
                ?>
                <tr>
                    <td><?=$i++;?></td>
                    <td><?=$data['namabarang'];?></td>
                    <td><?=$data['deskripsi'];?></td>
                    <td><?=$data['qty'];?></td>
                    <td><?=$data['lokasi'];?></td>
                    <td></td> <td></td> <td></td> </tr>
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
    $('#mauexport').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            { 
                extend: 'excelHtml5', 
                title: 'Laporan Event - <?=addslashes($nama_event);?>'
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'Laporan Event - <?=addslashes($nama_event);?>',
                customize: function (doc) {
                    doc.styles.title = {
                        color: '#2563eb', fontSize: '18', bold: true, alignment: 'center', margin: [0, 0, 0, 10]
                    };
                    doc.styles.tableHeader = {
                        bold: true, fontSize: 10, color: 'white', fillColor: '#2563eb', alignment: 'center'
                    };
                    
                    // ======== PERUBAHAN DI SINI ========
                    // Menambahkan layout dengan garis tabel
                    doc.content[1].layout = {
                        hLineWidth: function (i, node) {
                            return 1;
                        },
                        vLineWidth: function (i, node) {
                            return 1;
                        },
                        hLineColor: function (i, node) {
                            return '#aaa';
                        },
                        vLineColor: function (i, node) {
                            return '#aaa';
                        }
                    };
                    // =====================================

                    doc.content[0].style = 'title';
                    let tableHeader = doc.content[1].table.body[0];
                    for (var i = 0; i < tableHeader.length; i++) {
                        tableHeader[i].style = 'tableHeader';
                    }
                    doc.content[1].table.widths = ['auto', '*', '25%', 'auto', '15%', '20%', '10%', '10%'];
                    doc.content.push({
                        text: '\n\n\nDiserahkan oleh,\n\n\n___________________\n<?=addslashes($penanggung_jawab);?>',
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