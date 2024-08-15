<?php
// login.php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    switch ($role) {
        case 'operator':
            $query = "SELECT * FROM operator WHERE nama = ? AND password = ?";
            $redirectUrl = 'dashboard_operator.php';
            break;
        case 'tenaga_kependidikan':
            $query = "SELECT * FROM tenaga_kependidikan WHERE nipy = ? AND password = ?";
            $redirectUrl = 'dashboard_tendik.php';
            break;
        case 'dosen':
            $query = "SELECT * FROM dosen WHERE nipy = ? AND password = ?";
            $redirectUrl = 'dashboard_dosen.php';
            break;
        case 'mahasiswa':
            $query = "SELECT * FROM mahasiswa WHERE nim = ? AND password = ?";
            $redirectUrl = 'dashboard_mahasiswa.php';
            break;
        default:
            $query = "";
            $redirectUrl = 'login.php';
            break;
    }

    if ($query) {
        if ($stmt = $koneksi->prepare($query)) {
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row) {
                $_SESSION['login'] = true;
                $_SESSION['role'] = $role;
                $_SESSION['user'] = $row;

                if ($role == 'mahasiswa') {
                    $_SESSION['nim'] = $row['nim'];
                } else if ($role == 'dosen' || $role == 'tenaga_kependidikan') {
                    $_SESSION['nipy'] = $row['nipy'];
                }

                header("Location: $redirectUrl");
                exit;
            } else {
                $error = "Username atau password salah";
            }
            $stmt->close();
        } else {
            $error = "Gagal mempersiapkan query";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | UNDHA AUB</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger">
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    <?php endif; ?>
                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" placeholder="Enter Username..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="role" required>
                                                <option value="operator">Operator</option>
                                                <option value="tenaga_kependidikan">Tenaga Kependidikan</option>
                                                <option value="dosen">Dosen</option>
                                                <option value="mahasiswa">Mahasiswa</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="login" class="btn btn-primary btn-user btn-block">Login</button>
                                    </form>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
