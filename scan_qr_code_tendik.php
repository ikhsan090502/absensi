<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION['role'] !== 'tenaga_kependidikan') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$nip = $_SESSION['nipy'];

// Query untuk mendapatkan data tenaga kependidikan
$result = mysqli_query($koneksi, "SELECT * FROM tenaga_kependidikan WHERE nipy = '$nip'");
$row = mysqli_fetch_assoc($result);

$tenaga_name = $row["nama"];
$tenaga_foto = $row["foto"];

// Ambil riwayat absensi berdasarkan nipy
$absensi_result = mysqli_query($koneksi, "SELECT * FROM absensi WHERE nipy = '$nip' ORDER BY waktu_absen DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Scan QR Code | UNDHA AUB</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .qr-card-body {
      text-align: center;
      padding: 20px;
    }
    .card-header {
      background-color: #4e73df;
      color: white;
    }
    .card-body {
      background-color: #f8f9fc;
    }
  </style>
</head>
<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
          <i class="fas fa-university"></i>
        </div>
        <div class="sidebar-brand-text mx-3">UNDHA AUB</div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link" href="index.php">
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
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($tenaga_name); ?></span>
                <img class="img-profile rounded-circle" src="<?= htmlspecialchars($tenaga_foto); ?>">
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
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

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <h1 class="h3 mb-2 text-gray-800">Scan QR Code</h1>
          <p class="mb-4">Gunakan fitur ini untuk memindai QR Code yang ditampilkan pada jadwal mata kuliah.</p>

          <!-- QR Code Scanner -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Scan QR Code</h6>
            </div>
            <div class="card-body qr-card-body">
              <video id="video" width="100%" height="auto" style="border: 1px solid #ddd;"></video>
              <button id="startButton" class="btn btn-primary mt-2">Mulai Scan QR Code</button>
              <p id="statusMessage" class="mt-2"></p>
            </div>
          </div>

          <!-- Riwayat Absensi -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Riwayat Absensi</h6>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($absensi = mysqli_fetch_assoc($absensi_result)): ?>
                    <tr>
                      <td><?= htmlspecialchars(date('d-m-Y', strtotime($absensi['waktu_absen']))); ?></td>
                      <td><?= htmlspecialchars(date('H:i:s', strtotime($absensi['waktu_absen']))); ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white sticky-footer">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>© 2024 UNDHA AUB</span>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
  <script src="https://unpkg.com/jsqr/dist/jsQR.js"></script>
  <script>
    // QR Code Scanner JavaScript
    let video = document.getElementById('video');
    let startButton = document.getElementById('startButton');
    let statusMessage = document.getElementById('statusMessage');

    function startScanner() {
      navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(function (stream) {
          video.srcObject = stream;
          video.setAttribute('playsinline', true);
          video.play();
          scanQRCode();
        })
        .catch(function (error) {
          statusMessage.textContent = 'Gagal mengakses kamera: ' + error.message;
        });
    }

    function scanQRCode() {
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');
      const qrCodeReader = () => {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
          canvas.height = video.videoHeight;
          canvas.width = video.videoWidth;
          context.drawImage(video, 0, 0, canvas.width, canvas.height);
          const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
          const code = jsQR(imageData.data, canvas.width, canvas.height, {
            inversionAttempts: 'dontInvert',
          });
          if (code) {
            handleQRCodeResult(code.data);
          } else {
            requestAnimationFrame(qrCodeReader);
          }
        } else {
          requestAnimationFrame(qrCodeReader);
        }
      };
      qrCodeReader();
    }

    function handleQRCodeResult(data) {
      statusMessage.textContent = 'Hasil QR Code: ' + data;
      // Kirim data ke server untuk diproses
      fetch('proses_scan_qr_code_tendik.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'data=' + encodeURIComponent(data),
      })
      .then(response => response.text())
      .then(result => {
        statusMessage.textContent = result;
      })
      .catch(error => {
        statusMessage.textContent = 'Error: ' + error.message;
      });
    }

    startButton.addEventListener('click', startScanner);
  </script>
</body>
</html>
