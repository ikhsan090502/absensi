<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message = isset($_GET['message']) ? urldecode($_GET['message']) : '';

    if ($status == 'success') {
        echo "<script>alert('Absensi berhasil dihapus');</script>";
    } elseif ($status == 'fail') {
        echo "<script>alert('Gagal menghapus absensi: " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "');</script>";
    } elseif ($status == 'error') {
        echo "<script>alert('Kesalahan: " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "');</script>";
    }
}

include "koneksi.php";
include 'vendor/phpqrcode/qrlib.php';

$operator_name = $_SESSION["operator_user_name"] ?? "Operator";
$operator_foto = $_SESSION["operator_user_foto"] ?? "img/default.jpg";

// Cek apakah tabel absensi ada
$check_table_query = "SHOW TABLES LIKE 'absensi'";
$result_check_table = mysqli_query($koneksi, $check_table_query);

if (mysqli_num_rows($result_check_table) == 0) {
    die('Tabel "absensi" tidak ditemukan dalam database.');
}

// Query untuk menghitung jumlah absensi
$query_jumlah_absensi = "SELECT COUNT(*) AS jumlah_absensi FROM absensi";
$result_jumlah_absensi = mysqli_query($koneksi, $query_jumlah_absensi);
if (!$result_jumlah_absensi) {
    die('Query Error: ' . mysqli_error($koneksi));
}
$jumlah_absensi = mysqli_fetch_assoc($result_jumlah_absensi)['jumlah_absensi'];

// Query untuk mengambil data absensi beserta detailnya
$query_absensi = "
SELECT 
    a.waktu_absen, 
    a.nim, 
    m.nama AS nama_mahasiswa, 
    mk.nama_mk, 
    d.nama AS nama_dosen, 
    t.nipy AS nipy_tendik, 
    t.nama AS nama_tendik, 
    t.jabatan
FROM 
    absensi a
LEFT JOIN 
    mahasiswa m ON a.nim = m.nim
LEFT JOIN 
    mata_kuliah mk ON a.id_mk = mk.id_mk
LEFT JOIN 
    dosen d ON mk.id_dosen = d.id
LEFT JOIN 
    tenaga_kependidikan t ON a.nim = t.nipy";

$result_absensi = mysqli_query($koneksi, $query_absensi);
if (!$result_absensi) {
    die('Query Error: ' . mysqli_error($koneksi));
}

$generated = false;
$qrFilePath = '';

if (isset($_POST['generate_qr'])) {
    $data = 'Absensi Data';  // Data yang akan diencode ke QR code
    $tempDir = 'temp/';
    $fileName = 'absensi_qr.png';
    $qrFilePath = $tempDir . $fileName;
    
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    
    QRcode::png($data, $qrFilePath, QR_ECLEVEL_L, 10);
    $generated = true;
}

if (isset($_POST['download_qr'])) {
    if (file_exists($qrFilePath)) {
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="qr_code.png"');
        readfile($qrFilePath);
        exit;
    } else {
        echo "<script>alert('QR Code tidak ditemukan.');</script>";
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

  <title>Lihat Absensi | UNDHA AUB</title>

  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <style>
    .flex-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .flex-container .btn-container {
      flex-shrink: 0;
    }
    .qr-code {
      width: 200px; /* Ukuran besar QR Code */
      height: 200px;
    }
    .qr-card-body {
      text-align: center;
      padding: 20px;
    }
  </style>
  <script>
    function showConfirmModal(id_absensi) {
      $('#confirmModal').modal('show');
      $('#confirmDelete').attr('href', 'hapus_absensi.php?id_absensi=' + id_absensi);
    }

    function checkQRCodeValidity() {
      const now = new Date();
      const startHour = 8; // 08:00 AM
      const endHour = 16; // 04:00 PM
      const dayOfWeek = now.getDay(); // 0: Sunday, 1: Monday, ..., 6: Saturday

      if (now.getHours() < startHour || now.getHours() >= endHour || dayOfWeek === 0 || dayOfWeek === 6) {
        document.getElementById('qr-code-container').style.display = 'none';
      } else {
        document.getElementById('qr-code-container').style.display = 'block';
      }
    }

    window.onload = function() {
      checkQRCodeValidity();
    }
  </script>
</head>

<body id="page-top">
  <div id="wrapper">

    <!-- Sidebar -->
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
          <span>Dashboard</span></a>
      </li>
      <hr class="sidebar-divider">
      <li class="nav-item">
        <a class="nav-link" href="absensi.php">
          <i class="fas fa-fw fa-user-check"></i>
          <span>Absensi</span></a>
      </li>
      <hr class="sidebar-divider d-none d-md-block">
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($operator_name, ENT_QUOTES, 'UTF-8'); ?></span>
                <img class="img-profile rounded-circle" src="<?= htmlspecialchars($operator_foto, ENT_QUOTES, 'UTF-8'); ?>">
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profil_operator.php">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout_operator.php" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <h1 class="h3 mb-2 text-gray-800">Absensi</h1>
          <p class="mb-4">Daftar absensi mahasiswa yang telah dilakukan.</p>

          <!-- QR Code Generator Form -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Generate QR Code</h6>
            </div>
            <div class="card-body">
              <form action="" method="post">
                <button type="submit" name="generate_qr" class="btn btn-primary">Generate QR Code</button>
              </form>
              <?php if ($generated): ?>
                <div class="qr-code">
                  <img src="<?= htmlspecialchars($qrFilePath, ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code" class="img-fluid">
                </div>
                <form action="" method="post">
                  <input type="hidden" name="download_qr" value="true">
                  <button type="submit" class="btn btn-success mt-2">Download QR Code</button>
                </form>
              <?php endif; ?>
            </div>
          </div>

          <!-- Absensi Table -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Data Absensi</h6>
            </div>
            <div class="btn-container">
  <button class="btn btn-secondary" data-toggle="modal" data-target="#rekapModal">Rekap</button>
</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Waktu Absen</th>
                      <th>NIM</th>
                      <th>Nama Mahasiswa</th>
                      <th>Mata Kuliah</th>
                      <th>Nama Dosen</th>
                      <th>NIPY Tendik</th>
                      <th>Nama Tendik</th>
                      <th>Jabatan Tendik</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_absensi)): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['waktu_absen'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nim'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nama_mahasiswa'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nama_mk'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nama_dosen'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nipy_tendik'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nama_tendik'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['jabatan'], ENT_QUOTES, 'UTF-8'); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>© 2024 UNDHA AUB. All Rights Reserved.</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Logout Modal -->
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
          <a class="btn btn-primary" href="logout_operator.php">Logout</a>
        </div>
      </div>
    </div>
  </div>
<!-- Rekap Modal -->
<div class="modal fade" id="rekapModal" tabindex="-1" role="dialog" aria-labelledby="rekapModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rekapModalLabel">Pilih Rekap</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="rekap_absensi.php">
          <div class="form-group">
            <label for="rekapType">Jenis Rekap:</label>
            <select name="rekapType" id="rekapType" class="form-control">
              <option value="mahasiswa">Mahasiswa</option>
              <option value="tendik">Tenaga Kependidikan</option>
            </select>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Rekap dan Download</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Rekap Modal -->
<div class="modal fade" id="rekapModal" tabindex="-1" role="dialog" aria-labelledby="rekapModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rekapModalLabel">Pilih Rekap</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="rekap_absensi.php">
          <div class="form-group">
            <label for="rekapType">Jenis Rekap:</label>
            <select name="rekapType" id="rekapType" class="form-control">
              <option value="mahasiswa">Mahasiswa</option>
              <option value="tendik">Tenaga Kependidikan</option>
            </select>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Rekap dan Download</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>
</body>
</html>
