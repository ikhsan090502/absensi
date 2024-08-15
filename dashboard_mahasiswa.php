<?php
session_start();

if (!isset($_SESSION["login"]) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

if (!isset($_SESSION['nim'])) {
    echo "NIM tidak ditemukan dalam sesi.";
    exit;
}

$nim = $_SESSION['nim'];

// Query untuk mendapatkan data mahasiswa
$query_mahasiswa = "SELECT * FROM mahasiswa WHERE nim = '$nim'";
$result_mahasiswa = mysqli_query($koneksi, $query_mahasiswa);

if (!$result_mahasiswa) {
    echo "Query gagal: " . mysqli_error($koneksi);
    exit;
}

$row_mahasiswa = mysqli_fetch_assoc($result_mahasiswa);

if (!$row_mahasiswa) {
    echo "Data mahasiswa tidak ditemukan.";
    exit;
}

$mahasiswa_name = $row_mahasiswa["nama"];
$mahasiswa_foto = $row_mahasiswa["foto"];

// Query untuk mendapatkan mata kuliah yang diambil oleh mahasiswa
$mata_kuliah_ids = [
    $row_mahasiswa['mata_kuliah_1'],
    $row_mahasiswa['mata_kuliah_2'],
    $row_mahasiswa['mata_kuliah_3'],
    $row_mahasiswa['mata_kuliah_4'],
    $row_mahasiswa['mata_kuliah_5'],
    $row_mahasiswa['mata_kuliah_6']
];

$mata_kuliah_ids = array_filter($mata_kuliah_ids); // Hapus nilai NULL
$mata_kuliah_ids = implode(',', $mata_kuliah_ids);

if ($mata_kuliah_ids) {
    $query_mata_kuliah = "SELECT * FROM mata_kuliah WHERE id_mk IN ($mata_kuliah_ids)";
    $result_mata_kuliah = mysqli_query($koneksi, $query_mata_kuliah);

    if (!$result_mata_kuliah) {
        echo "Query gagal: " . mysqli_error($koneksi);
        exit;
    }
} else {
    $result_mata_kuliah = [];
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

  <title>Dashboard | Mahasiswa</title>

  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_mahasiswa.php">
        <div class="sidebar-brand-icon">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Mahasiswa</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="dashboard_mahasiswa.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Nav Item - Absensi -->
      <li class="nav-item">
        <a class="nav-link" href="scan_qr_code_mahasiswa.php">
          <i class="fas fa-fw fa-calendar-check"></i>
          <span>Absensi</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 medium"><?=$mahasiswa_name?></span>
                <img class="img-profile rounded-circle" src="img/<?=$mahasiswa_foto?>">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profil
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Setting
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Dashboard Mahasiswa</h1>

          <div class="row">

            <!-- Mata Kuliah yang Diambil -->
            <div class="col-xl-12 col-lg-12">
              <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseCardMataKuliah" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardMataKuliah">
                  <h6 class="m-0 font-weight-bold text-primary">Mata Kuliah yang Diambil</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardMataKuliah">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                          <tr>
                            <th>Kode Mata Kuliah</th>
                            <th>Nama Mata Kuliah</th>
                            <th>Jam</th>
                            <th>Hari</th>
                            <th>SKS</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (mysqli_num_rows($result_mata_kuliah) > 0): ?>
                            <?php while ($row_mk = mysqli_fetch_assoc($result_mata_kuliah)): ?>
                              <tr>
                                <td><?= $row_mk['kode_mk'] ?></td>
                                <td><?= $row_mk['nama_mk'] ?></td>
                                <td><?= $row_mk['jam'] ?></td>
                                <td><?= $row_mk['hari'] ?></td>
                                <td><?= $row_mk['sks'] ?></td>
                              </tr>
                            <?php endwhile; ?>
                          <?php else: ?>
                            <tr>
                              <td colspan="5" class="text-center">Tidak ada mata kuliah yang diambil.</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <!-- End of Page Content -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="footer">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2024</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
