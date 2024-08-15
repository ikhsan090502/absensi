<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$nipy = $_GET['nipy'];
$operator_name = $_SESSION["operator_user_name"];
$operator_foto = $_SESSION["operator_user_foto"];
$operator_last_login = $_SESSION["operator_user_last_login"];

// Ambil data dosen berdasarkan NIPY
$query_dosen = "SELECT * FROM dosen WHERE nipy = '$nipy'";
$result_dosen = mysqli_query($koneksi, $query_dosen);
$dosen = mysqli_fetch_assoc($result_dosen);

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nipy_baru = $_POST['nipy'];
    $nama = $_POST['nama'];
    $foto = $_FILES['foto']['name'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($foto) {
        $target = "img/" . basename($foto);
        move_uploaded_file($_FILES['foto']['tmp_name'], $target);
    } else {
        $foto = $dosen['foto'];
    }

    // Validasi dan update password jika diisi (tanpa hashing)
if (!empty($password_baru) && $password_baru === $konfirmasi_password) {
  // Update password tanpa hashing
  $query_update = "UPDATE dosen SET nipy='$nipy_baru', nama='$nama', foto='$foto', password='$password_baru' WHERE nipy='$nipy'";
} else {
  $query_update = "UPDATE dosen SET nipy='$nipy_baru', nama='$nama', foto='$foto' WHERE nipy='$nipy'";
}

    if (mysqli_query($koneksi, $query_update)) {
        echo "<script> window.location.href='lihatdosen.php';</script>";
    } else {
        echo "Gagal mengupdate data!";
    }
}

$query_mk = "SELECT * FROM mata_kuliah";
$result_mk = mysqli_query($koneksi, $query_mk);

// Variabel untuk menyimpan mata kuliah yang sudah dipilih
$selected_mk1 = isset($mk_dosen[0]) ? $mk_dosen[0] : '';
$selected_mk2 = isset($mk_dosen[1]) ? $mk_dosen[1] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Edit Dosen | UNDHA AUB</title>
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
          <h1 class="h3 mb-4 text-gray-800">Edit Dosen</h1>

          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Form Edit Dosen</h6>
            </div>
            <div class="card-body">
            <form method="post" action="proses_edit_dosen.php" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="nipy">NIPY</label>
                  <input type="text" class="form-control" id="nipy" name="nipy" value="<?= $dosen['nipy'] ?>" required>
                </div>
                <div class="form-group">
                  <label for="nama">Nama</label>
                  <input type="text" class="form-control" id="nama" name="nama" value="<?= $dosen['nama'] ?>" required>
                </div>
                <div class="form-group">
                  <label for="jenis_kelamin">Jenis Kelamin</label>
                  <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="L" <?= $dosen['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= $dosen['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?= $dosen['email'] ?>" required>
                </div>
                <div class="form-group">
                  <label for="foto">Foto</label>
                  <input type="file" class="form-control-file" id="foto" name="foto">
                  <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                  <img src="img/<?= $dosen['foto']; ?>" alt="Foto Dosen" class="img-thumbnail mt-2" style="width: 150px;">
                </div>
                <div class="form-group">
  <label for="mata_kuliah_1">Mata Kuliah 1</label>
  <select class="form-control" id="mata_kuliah_1" name="mata_kuliah_1">
    <option value="">-- Pilih Mata Kuliah --</option>
    <?php while ($mk = mysqli_fetch_assoc($result_mk)) : ?>
      <option value="<?= $mk['id_mk'] ?>" <?= $mk['id_mk'] == $selected_mk1 ? 'selected' : '' ?>>
        <?= $mk['nama_mk'] ?>
      </option>
    <?php endwhile; ?>
  </select>
</div>

<?php
// Reload data mata kuliah untuk dropdown Mata Kuliah 2
mysqli_data_seek($result_mk, 0); // Reset pointer hasil query ke awal
?>

<!-- Dropdown Mata Kuliah 2 -->
<div class="form-group">
  <label for="mata_kuliah_2">Mata Kuliah 2</label>
  <select class="form-control" id="mata_kuliah_2" name="mata_kuliah_2">
    <option value="">-- Pilih Mata Kuliah --</option>
    <?php while ($mk = mysqli_fetch_assoc($result_mk)) : ?>
      <!-- Pastikan mata kuliah 2 tidak menampilkan mata kuliah yang dipilih di Mata Kuliah 1 -->
      <?php if ($mk['id_mk'] != $selected_mk1) : ?>
        <option value="<?= $mk['id_mk'] ?>" <?= $mk['id_mk'] == $selected_mk2 ? 'selected' : '' ?>>
          <?= $mk['nama_mk'] ?>
        </option>
      <?php endif; ?>
    <?php endwhile; ?>
  </select>
</div>

<!-- Tambahkan kolom untuk mengganti password -->
<div class="form-group">
                  <label for="password_baru">Password Baru</label>
                  <input type="password" class="form-control" id="password_baru" name="password_baru" placeholder="Masukkan password baru">
                </div>
                <div class="form-group">
                  <label for="konfirmasi_password">Konfirmasi Password</label>
                  <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" placeholder="Konfirmasi password baru">
                </div>
                <input type="hidden" name="nipy" value="<?= $dosen['nipy'] ?>">
    <input type="hidden" name="foto_lama" value="<?= $dosen['foto'] ?>">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="proses_edit_dosen.php" class="btn btn-secondary">Batal</a>
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
