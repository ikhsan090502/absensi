<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Mengambil informasi operator dari session
$operator_user_name = isset($_SESSION["operator_user_name"]) ? $_SESSION["operator_user_name"] : 'Operator';
$operator_user_foto = isset($_SESSION["operator_user_foto"]) ? $_SESSION["operator_user_foto"] : 'default.jpg';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari = $_POST["hari"];
    $jam_mulai = $_POST["jam_mulai"];
    $jam_selesai = $_POST["jam_selesai"];
    $mata_kuliah = $_POST["mata_kuliah"];
    $dosen = $_POST["dosen"];

    $query = "INSERT INTO jadwal (hari, jam_mulai, jam_selesai, id_mk, nipy) VALUES ('$hari', '$jam_mulai', '$jam_selesai', '$mata_kuliah', '$dosen')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Jadwal berhasil ditambahkan'); window.location.href='lihatjadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jadwal');</script>";
    }
}

// Mengambil data mata kuliah dan dosen
$mata_kuliah_result = mysqli_query($koneksi, "SELECT id_mk, nama_mk FROM mata_kuliah");
$dosen_result = mysqli_query($koneksi, "SELECT nipy, nama FROM dosen");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tambah Jadwal | UNDHA AUB</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_operator.php">
                <div class="sidebar-brand-icon"><i class="fas fa-university"></i></div>
                <div class="sidebar-brand-text mx-3">UNDHA AUB</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="dashboard_operator.php"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="absensi.php"><i class="fas fa-fw fa-user-check"></i><span>Absensi</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav align-items-center"></li>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($operator_user_name) ?></span>
                            <img class="img-profile rounded-circle" src="img/<?= htmlspecialchars($operator_user_foto) ?>">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profil</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Pengaturan</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Tambah Jadwal</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Jadwal</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="hari">Hari</label>
                                    <select class="form-control" id="hari" name="hari" required>
                                        <option value="">Pilih Hari</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jumat">Jumat</option>
                                        <option value="Sabtu">Sabtu</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jam_mulai">Jam Mulai</label>
                                    <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                                </div>
                                <div class="form-group">
                                    <label for="jam_selesai">Jam Selesai</label>
                                    <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required>
                                </div>
                                <div class="form-group">
                                    <label for="mata_kuliah">Mata Kuliah</label>
                                    <select class="form-control" id="mata_kuliah" name="mata_kuliah" required>
                                        <option value="">Pilih Mata Kuliah</option>
                                        <?php while ($row = mysqli_fetch_assoc($mata_kuliah_result)): ?>
                                            <option value="<?= $row['id_mk'] ?>"><?= $row['nama_mk'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dosen">Dosen</label>
                                    <select class="form-control" id="dosen" name="dosen" required>
                                        <option value="">Pilih Dosen</option>
                                        <?php while ($row = mysqli_fetch_assoc($dosen_result)): ?>
                                            <option value="<?= $row['nipy'] ?>"><?= $row['nama'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah Jadwal</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; UNDHA AUB Surakarta 2024</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
