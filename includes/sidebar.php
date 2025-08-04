<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <style>
            /* Menggunakan CSS yang Anda berikan untuk konsistensi */
            #layoutSidenav_nav .sb-sidenav {
                background: linear-gradient(135deg,rgb(15, 0, 221) 0%,rgba(36, 143, 230, 0.64) 100%) !important;
            }
            #layoutSidenav_nav .sb-sidenav .nav-link,
            #layoutSidenav_nav .sb-sidenav .sb-nav-link-icon,
            #layoutSidenav_nav .sb-sidenav .sb-sidenav-menu-heading {
                color: #fff !important;
            }
            #layoutSidenav_nav .sb-sidenav .nav-link.active,
            #layoutSidenav_nav .sb-sidenav .nav-link:hover {
                background: rgba(30, 64, 175, 0.7) !important;
                color: #fff !important;
            }
            /* Menyesuaikan footer agar logout tetap di bawah */
            .sb-sidenav {
                display: flex;
                flex-direction: column;
                height: 100%;
            }
            .sb-sidenav-menu {
                flex-grow: 1;
            }
        </style>
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Menu Utama</div>
                <a class="nav-link <?php if($currentPage == 'index') echo 'active'; ?>" href="index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-box-open"></i></div>
                    Stok Barang
                </a>
                <a class="nav-link <?php if($currentPage == 'masuk') echo 'active'; ?>" href="masuk.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-right"></i></div>
                    Barang Masuk
                </a>
                <a class="nav-link <?php if($currentPage == 'keluar') echo 'active'; ?>" href="keluar.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-left"></i></div>
                    Barang Keluar
                </a>
                <a class="nav-link <?php if($currentPage == 'peminjaman') echo 'active'; ?>" href="peminjaman.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    Peminjaman Barang
                </a>
                <a class="nav-link <?php if($currentPage == 'event') echo 'active'; ?>" href="event.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Kelola Event
                </a>
                
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                <div class="sb-sidenav-menu-heading">Admin</div>
                <a class="nav-link <?php if($currentPage == 'admin') echo 'active'; ?>" href="admin.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                    Manajemen Admin
                </a>
                <?php } ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
             <a class="nav-link" href="logout.php">
                <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                Logout
            </a>
        </div>
    </nav>
</div>