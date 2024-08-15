<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$nim = $_GET['nim'];
$query = "SELECT * FROM mahasiswa WHERE nim='$nim'";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);
$semester_saat_ini = $row['semester'];

$query_semester = "SELECT * FROM semester";
$result_semester = mysqli_query($koneksi, $query_semester);

// Ambil semua mata kuliah yang tersedia
$query_mata_kuliah = "SELECT id_mk, nama_mk FROM mata_kuliah";
$result_mata_kuliah = mysqli_query($koneksi, $query_mata_kuliah);
$mataKuliahOptions = [];
while ($mk = mysqli_fetch_assoc($result_mata_kuliah)) {
    $mataKuliahOptions[$mk['id_mk']] = $mk['nama_mk'];
}

// Ambil nama mata kuliah yang sudah dipilih
$mataKuliahDipilih = [];
for ($i = 1; $i <= 6; $i++) {
    $mk_id = $row["mata_kuliah_$i"];
    if ($mk_id != NULL) {
        $mataKuliahDipilih[$i] = $mataKuliahOptions[$mk_id];
    } else {
        $mataKuliahDipilih[$i] = "";
    }
}
$daftar_semester = array("1", "2", "3", "4", "5", "6", "7", "8");
$operator_name = $_SESSION["operator_user_name"];
$operator_foto = $_SESSION["operator_user_foto"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Edit Mahasiswa | UNDHA AUB</title>

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

      <!-- Nav Item - Charts -->
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
              <span class="mr-2 d-none d-lg-inline text-gray-600 medium">Terakhir Login : <span class="text-success"><?= $operator_last_login ?></span></span>
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
          <h1 class="h3 mb-4 text-gray-800">Edit Mahasiswa</h1>

         <!-- Form Edit Mahasiswa -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Form Edit Mahasiswa</h6>
            </div>
            <div class="card-body">
                <!-- Menampilkan Semester Saat Ini -->
                <div class="alert alert-info">
                    Semester Saat Ini: <strong><?= $semester_saat_ini; ?></strong>
                </div>
            <div class="card-body">
                <form action="proses_edit_mahasiswa.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="nim" value="<?= $row['nim'] ?>">
                    <div class="form-group">
                      <label for="nim">NIM</label>
                      <input type="text" class="form-control" id="nim" name="nim" value="<?= $row['nim'] ?>" required>
                  </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= $row['nama'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="Laki-laki" <?= $row['jk'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $row['jk'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $row['email'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <input type="file" class="form-control-file" id="foto" name="foto">
                        <br>
                        <img src="img/<?= $row['foto'] ?>" width="100">
                    </div>
                    <div class="form-group">
                        <label for="tahun_akademik">Tahun Akademik</label>
                        <input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" value="<?= $row['tahun_akademik'] ?>" required>
                        <div class="form-group">
                    </div>
                    <label for="semester_id">Semester</label>
                    <input type="text" class="form-control" id="semester_id" name="semester_id" value="<?= $semester_saat_ini ?>" required>
                </div>  
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="form-group">
                            <label for="mata_kuliah_<?= $i ?>">Mata Kuliah <?= $i ?></label>
                            <select class="form-control" id="mata_kuliah_<?= $i ?>" name="mata_kuliah_<?= $i ?>">
                                <option value="">Pilih Mata Kuliah</option>
                                <?php foreach ($mataKuliahOptions as $id_mk => $nama_mk): ?>
                                    <option value="<?= $id_mk ?>" <?= isset($row["mata_kuliah_$i"]) && $row["mata_kuliah_$i"] == $id_mk ? 'selected' : '' ?>>
                                        <?= $nama_mk ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endfor; ?>
                    <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" name="password" class="form-control" id="password">
                    <small>Kosongkan jika tidak ingin mengganti password.</small>
                </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
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
            <span>Copyright &copy; UNDHA AUB Surakarta 2024</span>
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
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
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
