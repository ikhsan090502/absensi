<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";


$operator_name = isset($_SESSION["operator_user_name"]) ? $_SESSION["operator_user_name"] : "Operator";
$operator_foto = isset($_SESSION["operator_user_foto"]) ? $_SESSION["operator_user_foto"] : "default.png";

$query_jumlah_dosen = "SELECT COUNT(*) AS jumlah_dosen FROM dosen";
$result_jumlah_dosen = mysqli_query($koneksi, $query_jumlah_dosen);
$row_jumlah_dosen = mysqli_fetch_assoc($result_jumlah_dosen);
$jumlah_dosen = $row_jumlah_dosen['jumlah_dosen'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Lihat Dosen | UNDHA AUB</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
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
    .modal-backdrop {
      display: none !important;
    }
  </style>
  <script>
function showConfirmModal(nipy) {
  $('#confirmModal').modal('show');
  $('#confirmDelete').attr('href', 'hapus_dosen.php?nipy=' + nipy);
}
  </script>
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
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $operator_name ?></span>
                <img class="img-profile rounded-circle" src="img/<?= $operator_foto ?>">
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
          <h1 class="h3 mb-4 text-gray-800">Lihat Dosen</h1>

          <div class="card shadow mb-0">
            <div class="card-header py-3 flex-container">
              <h6 class="m-0 font-weight-bold text-primary">Daftar Dosen</h6>
              <div class="btn-container">
                <a href="tambah_dosen.php" class="btn btn-primary"><i class="fas fa-plus-circle mr-2"></i>Tambah Dosen</a>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>NIPY</th>
                      <th>Nama</th>
                      <th>Jenis Kelamin</th>
                      <th>Email</th>
                      <th>Foto</th>
                      <th>Mata Kuliah yang Diajarkan</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $query_dosen = "
                      SELECT d.nipy, d.nama, d.jenis_kelamin, d.email, d.foto, 
                             GROUP_CONCAT(mk.nama_mk SEPARATOR ', ') AS mata_kuliah 
                      FROM dosen d 
                      LEFT JOIN mengajar mj ON d.nipy = mj.nipy 
                      LEFT JOIN mata_kuliah mk ON mj.id_mk = mk.id_mk 
                      GROUP BY d.nipy, d.nama, d.jenis_kelamin, d.email, d.foto";
                      $result_dosen = mysqli_query($koneksi, $query_dosen);

                      if (mysqli_num_rows($result_dosen) > 0) {
                        while ($data = mysqli_fetch_assoc($result_dosen)) {
                          echo "<tr>";
                          echo "<td>" . htmlspecialchars($data['nipy']) . "</td>";
                          echo "<td>" . htmlspecialchars($data['nama']) . "</td>";
                          echo "<td>" . htmlspecialchars($data['jenis_kelamin']) . "</td>";
                          echo "<td>" . htmlspecialchars($data['email']) . "</td>";
                          echo "<td><img src='img/" . htmlspecialchars($data['foto']) . "' width='50' height='50'></td>";
                          echo "<td>" . htmlspecialchars($data['mata_kuliah']) . "</td>";
                          echo "<td>";
                          echo "<a href='edit_dosen.php?nipy=" . urlencode($data['nipy']) . "' class='btn btn-info btn-circle btn-sm'><i class='fas fa-info-circle'></i></a>";
                          echo "<button class='btn btn-danger btn-circle btn-sm' onclick=\"showConfirmModal('" . htmlspecialchars($data['nipy']) . "')\"><i class='fas fa-trash'></i></button>";
                          echo "</td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='7'>Tidak ada data</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>&copy;UNDHA AUB Surakarta 2024</span>
          </div>
        </div>
      </footer>
    </div>
  </div>

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

  <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Hapus Dosen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">Apakah Anda yakin ingin menghapus dosen ini?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <a id="confirmDelete" href="#" class="btn btn-primary">Hapus</a>
      </div>
    </div>
  </div>
</div>


  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>
</body>
</html>
