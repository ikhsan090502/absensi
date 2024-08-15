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
      echo "<script>alert('Mahasiswa berhasil dihapus');</script>";
  } elseif ($status == 'fail') {
      echo "<script>alert('Gagal menghapus mahasiswa: $message');</script>";
  } elseif ($status == 'error') {
      echo "<script>alert('Kesalahan: $message');</script>";
  }
}
include "koneksi.php";


$operator_name = isset($_SESSION["operator_user_name"]) ? $_SESSION["operator_user_name"] : "Operator";
$operator_foto = isset($_SESSION["operator_user_foto"]) ? $_SESSION["operator_user_foto"] : "default.png";

// Query untuk menghitung jumlah mahasiswa
$query_jumlah_mahasiswa = "SELECT COUNT(*) AS jumlah_mahasiswa FROM mahasiswa";
$result_jumlah_mahasiswa = mysqli_query($koneksi, $query_jumlah_mahasiswa);
$row_jumlah_mahasiswa = mysqli_fetch_assoc($result_jumlah_mahasiswa);
$jumlah_mahasiswa = $row_jumlah_mahasiswa['jumlah_mahasiswa'];

// Query untuk mengambil data mahasiswa beserta mata kuliah yang mereka ambil
$query_mahasiswa = "
SELECT 
    m.nim, 
    m.nama, 
    m.email, 
    m.foto, 
    m.tahun_akademik, 
    m.semester, 
    CONCAT_WS(', ', mk1.nama_mk, mk2.nama_mk, mk3.nama_mk, mk4.nama_mk, mk5.nama_mk, mk6.nama_mk) AS mata_kuliah
FROM 
    mahasiswa m
LEFT JOIN 
    mata_kuliah mk1 ON m.mata_kuliah_1 = mk1.id_mk
LEFT JOIN 
    mata_kuliah mk2 ON m.mata_kuliah_2 = mk2.id_mk
LEFT JOIN 
    mata_kuliah mk3 ON m.mata_kuliah_3 = mk3.id_mk
LEFT JOIN 
    mata_kuliah mk4 ON m.mata_kuliah_4 = mk4.id_mk
LEFT JOIN 
    mata_kuliah mk5 ON m.mata_kuliah_5 = mk5.id_mk
LEFT JOIN 
    mata_kuliah mk6 ON m.mata_kuliah_6 = mk6.id_mk";

$result_mahasiswa = mysqli_query($koneksi, $query_mahasiswa);

if (!$result_mahasiswa) {
    die('Query Error: ' . mysqli_error($koneksi));
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

  <title>Lihat Mahasiswa | UNDHA AUB</title>

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
  </style>
  <script>
    function showConfirmModal(nim) {
      $('#confirmModal').modal('show');
      $('#confirmDelete').attr('href', 'hapus_mahasiswa.php?nim=' + nim);
    }
  </script>
</head>

<body id="page-top">
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_operator.php">
        <div class="sidebar-brand-icon">
          <i class="fas fa-university"></i>
        </div>
        <div class="sidebar-brand-text mx-3">UNDHA AUB</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="dashboard_operator.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Nav Item - Absensi -->
      <li class="nav-item">
        <a class="nav-link" href="absensi.php">
          <i class="fas fa-fw fa-user-check"></i>
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
            <li class="nav align-items-center">
            </li>
            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $operator_name ?></span>
                <img class="img-profile rounded-circle" src="img/<?= $operator_foto ?>">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profil
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Pengaturan
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
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
          <h1 class="h3 mb-4 text-gray-800">Lihat Mahasiswa</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3 flex-container">
              <h6 class="m-0 font-weight-bold text-primary">Data Mahasiswa</h6>
              <div class="btn-container">
                <a href="tambah_mahasiswa.php" class="btn btn-primary btn-sm">Tambah Mahasiswa</a>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>NIM</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Foto</th>
                      <th>Tahun Akademik</th>
                      <th>Semester</th>
                      <th>Mata Kuliah</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>NIM</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Foto</th>
                      <th>Tahun Akademik</th>
                      <th>Semester</th>
                      <th>Mata Kuliah</th>
                      <th>Aksi</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_mahasiswa)): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['nim']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><img src="img/<?= htmlspecialchars($row['foto']) ?>" alt="Foto Mahasiswa" width="50"></td>
                        <td><?= htmlspecialchars($row['tahun_akademik']) ?></td>
                        <td><?= htmlspecialchars($row['semester']) ?></td>
                        <td><?= htmlspecialchars($row['mata_kuliah']) ?></td>
                        <td>
                          <a href="edit_mahasiswa.php?nim=<?= htmlspecialchars($row['nim']) ?>" class="btn btn-warning btn-sm">Edit</a>
                          <button class="btn btn-danger btn-sm" onclick="showConfirmModal('<?= htmlspecialchars($row['nim']) ?>')">Hapus</button>
                        </td>
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
            <span>&copy; UNDHA AUB Surakarta 2024</span>
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

  <!-- Confirm Delete Modal-->
  <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Hapus</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Apakah Anda yakin ingin menghapus data mahasiswa ini?</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <a id="confirmDelete" class="btn btn-danger" href="#">Hapus</a>
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

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>

</body>
</html>
