<?php
// Pastikan sesi sudah dimulai di function.php
if(isset($_SESSION['log']) && $_SESSION['role'] === 'admin') {
    // Biarkan, karena dia adalah admin
} else {
    // Jika bukan admin, tendang ke halaman index
    header('location:index.php');
    exit();
}
?>