<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Pastikan bahwa variabel session 'operator_user_name' dan 'operator_user_foto' sudah diset saat login
if (isset($_SESSION["operator_user_name"]) && isset($_SESSION["operator_user_foto"])) {
    $operator_name = $_SESSION["operator_user_name"];
    $operator_foto = $_SESSION["operator_user_foto"];
} else {
    // Jika session tidak diset, beri nilai default atau arahkan kembali ke halaman login
    $operator_name = "Operator";
    $operator_foto = "default.jpg"; // Pastikan ada file default.jpg di folder img
}

// Query untuk menghitung jumlah program studi
$query_jumlah_prodi = "SELECT COUNT(*) AS jumlah_prodi FROM prodi";
$result_jumlah_prodi = mysqli_query($koneksi, $query_jumlah_prodi);
if (!$result_jumlah_prodi) {
    die('Error: ' . mysqli_error($koneksi));
}
$row_jumlah_prodi = mysqli_fetch_assoc($result_jumlah_prodi);
$jumlah_prodi = $row_jumlah_prodi['jumlah_prodi'];

// Query untuk menghitung jumlah mata kuliah
$query_jumlah_matkul = "SELECT COUNT(*) AS jumlah_matkul FROM mata_kuliah";
$result_jumlah_matkul = mysqli_query($koneksi, $query_jumlah_matkul);
if (!$result_jumlah_matkul) {
    die('Error: ' . mysqli_error($koneksi));
}
$row_jumlah_matkul = mysqli_fetch_assoc($result_jumlah_matkul);
$jumlah_matkul = $row_jumlah_matkul['jumlah_matkul'];

// Query untuk menghitung jumlah dosen
$query_jumlah_dosen = "SELECT COUNT(*) AS jumlah_dosen FROM dosen";
$result_jumlah_dosen = mysqli_query($koneksi, $query_jumlah_dosen);
if (!$result_jumlah_dosen) {
    die('Error: ' . mysqli_error($koneksi));
}
$row_jumlah_dosen = mysqli_fetch_assoc($result_jumlah_dosen);
$jumlah_dosen = $row_jumlah_dosen['jumlah_dosen'];

// Query untuk menghitung jumlah mahasiswa
$query_jumlah_mahasiswa = "SELECT COUNT(*) AS jumlah_mahasiswa FROM mahasiswa";
$result_jumlah_mahasiswa = mysqli_query($koneksi, $query_jumlah_mahasiswa);
if (!$result_jumlah_mahasiswa) {
    die('Error: ' . mysqli_error($koneksi));
}
$row_jumlah_mahasiswa = mysqli_fetch_assoc($result_jumlah_mahasiswa);
$jumlah_mahasiswa = $row_jumlah_mahasiswa['jumlah_mahasiswa'];

// Query untuk menghitung jumlah tenaga kependidikan
$query_jumlah_tenaga = "SELECT COUNT(*) AS jumlah_tenaga FROM tenaga_kependidikan";
$result_jumlah_tenaga = mysqli_query($koneksi, $query_jumlah_tenaga);
if (!$result_jumlah_tenaga) {
    die('Error: ' . mysqli_error($koneksi));
}
$row_jumlah_tenaga = mysqli_fetch_assoc($result_jumlah_tenaga);
$jumlah_tenaga = $row_jumlah_tenaga['jumlah_tenaga'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Dashboard | UNDHA AUB</title>

  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

  <div id="wrapper">

    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_operator.php">
        <div class="sidebar-brand-icon">
          <i class="fas fa-university"></i>
        </div>
        <div class="sidebar-brand-text mx-3">UNDHA AUB</div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link" href="dashboard_operator.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <hr class="sidebar-divider">
      <li class="nav-item">
        <a class="nav-link" href="lihatabsensi.php">
          <i class="fas fa-fw fa-user-check"></i>
          <span>Absensi</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="lihatprodi.php">
          <i class="fas fa-fw fa-user"></i>
          <span> Program Studi</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="lihatmatkul.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Mata Kuliah</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="lihatdosen.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Dosen</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="lihatmhs.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Mahasiswa</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="lihatjadwal.php">
          <i class="fas fa-fw fa-user-check"></i>
          <span>Jadwal</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="lihatendik.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Tenaga Kependidikan</span>
        </a>
      </li>
      <hr class="sidebar-divider d-none d-md-block">
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $operator_name ?></span>
                <img class="img-profile rounded-circle" src="img/<?= $operator_foto ?>">
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>
        <div class="container-fluid">
          <h1 class="h3 mb-2 text-gray-800">Dashboard</h1>
          <div class="row">
            <!-- Card untuk Program Studi -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Program Studi</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_prodi ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Card untuk Mata Kuliah -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Mata Kuliah</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_matkul ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Card untuk Dosen -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dosen</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_dosen ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Card untuk Mahasiswa -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Mahasiswa</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_mahasiswa ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Card untuk Tenaga Kependidikan -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tenaga Kependidikan</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_tenaga ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>UNDHA AUB</span>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
