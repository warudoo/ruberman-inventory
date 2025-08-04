<?php
require 'includes/function.php';

// Cek login
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Menyiapkan statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM login WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Cek apakah email ditemukan
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        // Verifikasi password
        if (password_verify($password, $data['password'])) {
            // Login sukses
            $_SESSION['log'] = 'True';
            $_SESSION['role'] = $data['role']; // Pastikan Anda sudah menambahkan kolom 'role'
            header('location:index.php');
            exit();
        }
    }
    
    // Jika email atau password salah, kembali ke login
    // Anda bisa tambahkan notifikasi error di sini
    header('location:login.php');
    exit();
};

if(isset($_SESSION['log'])) {
    header('location:index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login - Inventory</title>
        <link href="assets/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary" style="background: url('assets/img/rubermang.png') no-repeat center center fixed; background-size: cover;">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header text-center">
                                        <img src="assets/img/logo.png" alt="Logo" style="max-width: 120px; margin: 10px 0;">
                                        <div class="d-flex align-items-center justify-content-center mb-3" style="position: relative;">
                                            <!-- Icon di kiri h3 -->
                                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg,#4e73df 60%,#1cc88a 100%); border-radius: 50%; box-shadow: 0 4px 20px rgba(78,115,223,0.2); display: flex; align-items: center; justify-content: center; margin-right: 16px; z-index: 1;">
                                                <i class="fas fa-box-open fa-2x text-white"></i>
                                            </div>
                                            <h3 class="text-center font-weight-bold my-5" style="color:#4e73df; z-index:2; margin-bottom:0;">
                                                Ruberman Inventory
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="card-body bg-white">
                                        <form method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="email" id="inputEmail" type="email" placeholder="name@example.com" />
                                                <label for="inputEmail">Email address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="password" id="inputPassword" type="password" placeholder="Password" />
                                                <label for="inputPassword">Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button class="btn btn-primary" name="login">
                                                    <i class="fas fa-sign-in-alt"></i> Login
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
