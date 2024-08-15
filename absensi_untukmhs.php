<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";
include 'vendor/phpqrcode/qrlib.php';

// Mengecek tabel absensi
$check_table_query = "SHOW TABLES LIKE 'absensi'";
$result_check_table = mysqli_query($koneksi, $check_table_query);

if (mysqli_num_rows($result_check_table) == 0) {
    die('Tabel "absensi" tidak ditemukan dalam database.');
}

// Menyimpan absensi mahasiswa
if (isset($_POST['scan_qr'])) {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $qr_code = mysqli_real_escape_string($koneksi, $_POST['qr_code']);
    $waktu_absen = date('Y-m-d H:i:s');

    // Simpan absensi mahasiswa
    $query_absen = "INSERT INTO absensi (nim, qr_code, waktu_absen) VALUES ('$nim', '$qr_code', '$waktu_absen')";
    if (!mysqli_query($koneksi, $query_absen)) {
        die('Query Error: ' . mysqli_error($koneksi));
    }
}

// Menghasilkan QR Code
$generated = false;
$qrFilePath = '';
$qrCodeData = '';
$matkul_options = '';
$jam_options = '';

if (isset($_POST['generate_qr'])) {
    $id_mk = mysqli_real_escape_string($koneksi, $_POST['id_mk']);
    $jam = mysqli_real_escape_string($koneksi, $_POST['jam']);
    $waktu_mulai = date('Y-m-d H:i:s');
    $waktu_selesai = date('Y-m-d H:i:s', strtotime($waktu_mulai . ' +45 minutes'));

    // Data untuk QR Code
    $qrCodeData = "id_mk=$id_mk&jam=$jam&mulai=$waktu_mulai&selesai=$waktu_selesai";
    $tempDir = 'temp/';
    $fileName = 'absensi_qr.png';
    $qrFilePath = $tempDir . $fileName;

    if (!file_exists($tempDir)) {
        mkdir($tempDir);
    }

    QRcode::png($qrCodeData, $qrFilePath, QR_ECLEVEL_L, 10);
    $generated = true;
}

// Mengambil data mata kuliah dan jam
$query_matkul = "SELECT id_mk, nama_mk FROM mata_kuliah";
$result_matkul = mysqli_query($koneksi, $query_matkul);

while ($row = mysqli_fetch_assoc($result_matkul)) {
    $matkul_options .= "<option value='{$row['id_mk']}'>{$row['nama_mk']}</option>";
}

// Jam (contoh jam, sesuaikan dengan kebutuhan)
$jam_options = '<option value="08:00">08:00</option>
                <option value="10:00">10:00</option>
                <option value="13:00">13:00</option>
                <option value="15:00">15:00</option>';

// Handle QR Code Download
if (isset($_POST['download_qr'])) {
    if (file_exists($qrFilePath)) {
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . basename($qrFilePath) . '"');
        readfile($qrFilePath);
        exit;
    }
}

// Query untuk menampilkan data absensi mahasiswa
$query_absensi = "
    SELECT a.waktu_absen, a.nim, m.nama, mk.nama_mk 
    FROM absensi a
    JOIN mahasiswa m ON a.nim = m.nim
    JOIN mata_kuliah mk ON a.qr_code = mk.id_mk
    ORDER BY a.waktu_absen DESC
";
$result_absensi = mysqli_query($koneksi, $query_absensi);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Absensi Mahasiswa | UNDHA AUB</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .qr-code {
      width: 200px; /* Ukuran besar QR Code */
      height: 200px;
    }
    .qr-card-body {
      text-align: center;
      padding: 20px;
    }
    .table-wrapper {
      margin-top: 20px;
    }
  </style>
  <script>
    function showConfirmModal() {
      $('#confirmModal').modal('show');
    }
  </script>
</head>

<body id="page-top">
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_dosen.php">
        <div class="sidebar-brand-icon">
          <i class="fas fa-university"></i>
        </div>
        <div class="sidebar-brand-text mx-3">UNDHA AUB</div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link" href="dashboard_dosen.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>
      <hr class="sidebar-divider">
      <li class="nav-item">
        <a class="nav-link" href="absensi_untukmhs.php">
          <i class="fas fa-fw fa-user-check"></i>
          <span>Absensi</span></a>
      </li>
      <hr class="sidebar-divider d-none d-md-block">
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
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($_SESSION['login']); ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Absensi Mahasiswa</h1>
          <p class="mb-4">Generate QR Code untuk absensi mahasiswa dan scan QR Code untuk melakukan absensi.</p>

          <!-- QR Code Section -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Generate QR Code</h6>
            </div>
            <div class="card-body qr-card-body">
              <?php if ($generated && file_exists($qrFilePath)): ?>
                <!-- Menampilkan QR Code dan tombol cetak -->
                <img src="<?= $qrFilePath; ?>" alt="QR Code" class="qr-code">
                <form method="POST" action="">
                  <button type="submit" name="download_qr" class="btn btn-success mt-2">Cetak QR Code</button>
                </form>
              <?php endif; ?>

              <!-- Selalu tampilkan tombol generate QR Code -->
              <form method="POST" action="">
                <div class="form-group">
                  <label for="id_mk">Mata Kuliah:</label>
                  <select id="id_mk" name="id_mk" class="form-control" required>
                    <?= $matkul_options; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="jam">Jam:</label>
                  <select id="jam" name="jam" class="form-control" required>
                    <?= $jam_options; ?>
                  </select>
                </div>
                <button type="submit" name="generate_qr" class="btn btn-primary">Generate QR Code</button>
              </form>
            </div>
          </div>

          <!-- Tabel Absensi Mahasiswa -->
          <div class="table-wrapper">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi Mahasiswa</h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Waktu Absensi</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Mata Kuliah</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = mysqli_fetch_assoc($result_absensi)): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['waktu_absen']); ?></td>
                          <td><?= htmlspecialchars($row['nim']); ?></td>
                          <td><?= htmlspecialchars($row['nama']); ?></td>
                          <td><?= htmlspecialchars($row['nama_mk']); ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
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

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Modal Konfirmasi -->
  <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Penghapusan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Apakah Anda yakin ingin menghapus data ini?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
