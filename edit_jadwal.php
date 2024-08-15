<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$id_jadwal = $_GET['id_jadwal'];
$operator_name = $_SESSION["operator_user_name"];
$operator_foto = $_SESSION["operator_user_foto"];
$operator_last_login = $_SESSION["operator_user_last_login"];

// Ambil data jadwal
$query_jadwal = "SELECT * FROM jadwal WHERE id_jadwal = '$id_jadwal'";
$result_jadwal = mysqli_query($koneksi, $query_jadwal);
$jadwal = mysqli_fetch_assoc($result_jadwal);

// Ambil data dosen dan mata kuliah
$query_dosen = "SELECT * FROM dosen";
$result_dosen = mysqli_query($koneksi, $query_dosen);

$query_mk = "SELECT * FROM mata_kuliah";
$result_mk = mysqli_query($koneksi, $query_mk);

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_jadwal_baru = $_POST['id_jadwal'];
    $id_dosen = $_POST['id_dosen'];
    $id_mk = $_POST['id_mk'];
    $waktu = $_POST['waktu'];
    $ruang = $_POST['ruang'];

    // Update jadwal
    $query_update = "UPDATE jadwal SET id_dosen='$id_dosen', id_mk='$id_mk', waktu='$waktu', ruang='$ruang' WHERE id_jadwal='$id_jadwal_baru'";
    if (mysqli_query($koneksi, $query_update)) {
        echo "<script> window.location.href='lihatjadwal.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Edit Jadwal | UNDHA AUB</title>
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
            <li class="nav align-items-center">
              <span class="mr-2 d-none d-lg-inline text-gray-600 medium">Terakhir Login : <span class="text-success"><?= $operator_last_login ?></span></span>
            </li>
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $operator_name ?></span>
                <img class="img-profile rounded-circle" src="img/<?= $operator_foto ?>">
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
          <h1 class="h3 mb-4 text-gray-800">Edit Jadwal</h1>

          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Form Edit Jadwal</h6>
            </div>
            <div class="card-body">
              <form method="post">
                <div class="form-group">
                  <label for="id_jadwal">ID Jadwal</label>
                  <input type="text" class="form-control" id="id_jadwal" name="id_jadwal" value="<?= $jadwal['id_jadwal'] ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="id_dosen">Dosen</label>
                  <select class="form-control" id="id_dosen" name="id_dosen" required>
                    <option value="">-- Pilih Dosen --</option>
                    <?php while ($dosen = mysqli_fetch_assoc($result_dosen)) : ?>
                      <option value="<?= $dosen['nipy'] ?>" <?= $dosen['nipy'] == $jadwal['id_dosen'] ? 'selected' : '' ?>>
                        <?= $dosen['nama'] ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="id_mk">Mata Kuliah</label>
                  <select class="form-control" id="id_mk" name="id_mk" required>
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php while ($mk = mysqli_fetch_assoc($result_mk)) : ?>
                      <option value="<?= $mk['id_mk'] ?>" <?= $mk['id_mk'] == $jadwal['id_mk'] ? 'selected' : '' ?>>
                        <?= $mk['nama_mk'] ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="waktu">Waktu</label>
                  <input type="text" class="form-control" id="waktu" name="waktu" value="<?= $jadwal['waktu'] ?>" required>
                </div>
                <div class="form-group">
                  <label for="ruang">Ruang</label>
                  <input type="text" class="form-control" id="ruang" name="ruang" value="<?= $jadwal['ruang'] ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="lihatjadwal.php" class="btn btn-secondary">Batal</a>
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
