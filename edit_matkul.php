<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$nip = $_SESSION['nip'];
$operator_name = $_SESSION["operator_user_name"];
$operator_foto = $_SESSION["operator_user_foto"];
$operator_last_login = $_SESSION["operator_user_last_login"];

// Ambil ID mata kuliah dari URL
$id_mk = isset($_GET['id_mk']) ? $_GET['id_mk'] : null;

if (empty($id_mk)) {
    echo "ID Mata Kuliah tidak ditemukan.";
    exit;
}

// Ambil data mata kuliah
$query = "SELECT * FROM mata_kuliah WHERE id_mk = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_mk);
$stmt->execute();
$result = $stmt->get_result();


if (!$result_mk) {
    echo "Query Error: " . mysqli_error($koneksi);
    exit;
$result_mk = mysqli_query($koneksi, $query_mk);
}

$mata_kuliah = mysqli_fetch_assoc($result_mk);

if (!$mata_kuliah) {
    echo "Data Mata Kuliah tidak ditemukan.";
    exit;
}

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mk = $_POST['id_mk'];
    $kode_mk = $_POST['kode_mk'];
    $nama_mk = $_POST['nama_mk'];
    $sks = $_POST['sks'];

    // Update mata kuliah
    $query_update = "UPDATE mata_kuliah SET kode_mk='$kode_mk', nama_mk='$nama_mk', sks='$sks' WHERE id_mk='$id_mk'";
    if (mysqli_query($koneksi, $query_update)) {
        echo "<script> window.location.href='lihatmatkul.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data mata kuliah.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Mata Kuliah</title>
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
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $operator_name ;?></span>
                <img class="img-profile rounded-circle" src="img/<?= $operator_foto; ?>">
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
          <h1 class="h3 mb-4 text-gray-800">Edit Mata Kuliah</h1>

          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Form Edit Mata Kuliah</h6>
            </div>
            <div class="card-body">
              <form method="post">
                <input type="hidden" name="id_mk" value="<?= htmlspecialchars($mata_kuliah['id_mk'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-group">
                  <label for="kode_mk">Kode Mata Kuliah</label>
                  <input type="text" class="form-control" id="kode_mk" name="kode_mk" value="<?= htmlspecialchars($mata_kuliah['kode_mk'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                  <label for="nama_mk">Nama Mata Kuliah</label>
                  <input type="text" class="form-control" id="nama_mk" name="nama_mk" value="<?= htmlspecialchars($mata_kuliah['nama_mk'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                  <label for="sks">SKS</label>
                  <input type="number" class="form-control" id="sks" name="sks" value="<?= htmlspecialchars($mata_kuliah['sks'], ENT_QUOTES, 'UTF-8') ?>" min="1" max="6" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="lihatmatkul.php" class="btn btn-secondary">Batal</a>
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
