<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$nip = $_SESSION['nip'];
$operator_name = isset($_SESSION["operator_user_name"]) ? $_SESSION["operator_user_name"] : "Operator";
$operator_foto = isset($_SESSION["operator_user_foto"]) ? $_SESSION["operator_user_foto"] : "default.png";

// Ambil data mata kuliah
$query_mk = "SELECT * FROM mata_kuliah";
$result_mk = mysqli_query($koneksi, $query_mk);

// Simpan data mata kuliah dalam array
$mata_kuliah_options = [];
while ($row = mysqli_fetch_assoc($result_mk)) {
    $mata_kuliah_options[] = $row;
}

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nipy = $_POST['nipy'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $email = $_POST['email'];
    $foto = $_FILES['foto']['name'];
    $mata_kuliah_1 = $_POST['mata_kuliah_1'];
    $mata_kuliah_2 = $_POST['mata_kuliah_2'];

    // Cek apakah NIPY sudah ada di tabel dosen
    $cek_query = "SELECT * FROM dosen WHERE nipy = '$nipy'";
    $cek_result = mysqli_query($koneksi, $cek_query);

    if (mysqli_num_rows($cek_result) > 0) {
        echo "<script>alert('NIPY sudah ada di database');</script>";
    } else {
        // Insert dosen
        $query_dosen = "INSERT INTO dosen (nipy, nama, jenis_kelamin, email, foto) VALUES ('$nipy', '$nama', '$jenis_kelamin', '$email', '$foto')";
        if (mysqli_query($koneksi, $query_dosen)) {
            // Insert mata kuliah yang diajar
            if ($mata_kuliah_1) {
                $query_mengajar_1 = "INSERT INTO mengajar (nipy, id_mk) VALUES ('$nipy', '$mata_kuliah_1')";
                mysqli_query($koneksi, $query_mengajar_1);
            }
            if ($mata_kuliah_2) {
                $query_mengajar_2 = "INSERT INTO mengajar (nipy, id_mk) VALUES ('$nipy', '$mata_kuliah_2')";
                mysqli_query($koneksi, $query_mengajar_2);
            }

            // Redirect to lihatdosen.php
            echo "<script>window.location.href='lihatdosen.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan dosen: " . mysqli_error($koneksi) . "');</script>";
        }
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
  <title>Tambah Dosen | UNDHA AUB</title>
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
          <h1 class="h3 mb-4 text-gray-800">Tambah Dosen</h1>

          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Form Tambah Dosen</h6>
            </div>
            <div class="card-body">
              <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="nipy">NIPY</label>
                  <input type="text" class="form-control" id="nipy" name="nipy" required>
                </div>
                <div class="form-group">
                  <label for="nama">Nama</label>
                  <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                  <label for="jenis_kelamin">Jenis Kelamin</label>
                  <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                  <label for="foto">Foto</label>
                  <input type="file" class="form-control-file" id="foto" name="foto">
                </div>
                <div class="form-group">
                  <label for="mata_kuliah_1">Mata Kuliah 1</label>
                  <select class="form-control" id="mata_kuliah_1" name="mata_kuliah_1">
                    <option value="">Pilih Mata Kuliah</option>
                    <?php foreach ($mata_kuliah_options as $mk): ?>
                      <option value="<?= $mk['id_mk']; ?>"><?= $mk['nama_mk']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="mata_kuliah_2">Mata Kuliah 2</label>
                  <select class="form-control" id="mata_kuliah_2" name="mata_kuliah_2">
                    <option value="">Pilih Mata Kuliah</option>
                    <?php foreach ($mata_kuliah_options as $mk): ?>
                      <option value="<?= $mk['id_mk']; ?>"><?= $mk['nama_mk']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <button type="submit" class="btn btn-primary">Tambah Dosen</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; UNDHA AUB Surakarta 2024</span>
          </div>
        </div>
      </footer>
    </div>
  </div>

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

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

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
