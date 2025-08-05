<?php
require 'includes/function.php';
require 'includes/admin_cek.php'; 
$currentPage = 'admin'; // Variabel untuk menandai halaman aktif
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Manajemen Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
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
                    <div class="custom-header-admin mb-4 mt-4 d-flex align-items-center">
                        <div class="header-shape-admin">
                            <i class="fas fa-user-cog header-icon-admin"></i>
                        </div>
                        <div class="ml-3">
                            <h1 class="header-title-admin mb-0">Kelola Admin</h1>
                            <span class="header-subtitle-admin">Kelola data admin dengan mudah dan cepat</span>
                        </div>
                    </div>
                    <div class="card mb-4">
                    <style>
                        .custom-header-admin {
                            position: relative;
                            background: linear-gradient(90deg, #2193b0 0%, #6dd5ed 100%);
                            border-radius: 1.5rem;
                            padding: 1.5rem 2rem;
                            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                            margin-top: 2rem;
                            margin-bottom: 2rem;
                            z-index: 1;
                        }
                        .header-shape-admin {
                            width: 70px;
                            height: 70px;
                            background: #fff;
                            border-radius: 50% 40% 60% 50% / 60% 50% 40% 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
                        }
                        .header-icon-admin {
                            font-size: 2.5rem;
                            color: #2193b0;
                        }
                        .header-title-admin {
                            color: #fff;
                            font-weight: 700;
                            font-size: 2.2rem;
                            letter-spacing: 1px;
                        }
                        .header-subtitle-admin {
                            color: #e3f6ff;
                            font-size: 1rem;
                            opacity: 0.85;
                        }
                        @media (max-width: 576px) {
                            .custom-header-admin {
                                flex-direction: column;
                                align-items: flex-start;
                                padding: 1rem;
                            }
                            .header-shape-admin {
                                width: 50px;
                                height: 50px;
                            }
                            .header-title-admin {
                                font-size: 1.3rem;
                            }
                        }
                    </style>
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fas fa-plus"></i> <i class="fas fa-user"></i> Tambah Admin
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="datatablesSimple" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Email Admin</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $ambilsemuadataadmin = mysqli_query($conn, "SELECT * FROM login");
                                    $i=1;
                                    while($data=mysqli_fetch_array($ambilsemuadataadmin)){
                                        $em = $data['email'];
                                        $iduser = $data['iduser'];
                                    ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=$em;?></td>
                                            <td>
                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?=$iduser;?>">
                                                Delete
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="delete<?=$iduser;?>">
                                            </div>
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
    
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>

    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Admin</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="email" name="email" placeholder="Email" class="form-control" required>
                        <br>
                        <input type="password" name="password" placeholder="Password" class="form-control" required>
                        <br>
                        <select name="role" class="form-control" required>
                            <option value="" disabled selected>Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        <br>
                        <button type="submit" class="btn btn-primary" name="addadmin">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>