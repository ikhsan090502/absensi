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
$tgl = date("d-m-Y");

// Query untuk mengambil data program studi
$query_prodi = "SELECT * FROM prodi";
$result_prodi = mysqli_query($koneksi, $query_prodi);

// Query untuk mengambil data mata kuliah
$query_matkul = "SELECT * FROM mata_kuliah";
$result_matkul = mysqli_query($koneksi, $query_matkul);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tambah Mahasiswa | UNDHA AUB</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_operator.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-university"></i>
                </div>
                <div class="sidebar-brand-text mx-3">UNDHA AUB</div>
                <li class="nav-item">
            </a>
            <hr class="sidebar-divider my-0">
                <a class="nav-link" href="dashboard_operator.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="absensi.php">
                    <i class="fas fa-fw fa-user-check"></i>
                    <span>Absensi</span>
                </a>
            </li>
            <li class="nav-item">
  <a class="nav-link" href="lihatprodi.php">
    <i class="fas fa-fw fa-user"></i>
    <span> Program Studi</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="lihatmatkul.php">
    <i class="fas fa-fw fa-user"></i>
    <span>Mata Kuliah</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="lihatmhs.php">
    <i class="fas fa-fw fa-user"></i>
    <span>Mahasiswa</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="lihatendik.php">
    <i class="fas fa-fw fa-user"></i>
    <span>Tenaga Kependidikan</span></a>
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
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $operator_name; ?></span>
                                <img class="img-profile rounded-circle" src="img/<?= $operator_foto; ?>">
                            </a>
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
                <div class="container">
                    <div class="panel">
                        <h1 class="text-center mb-4">Tambah Mahasiswa</h1>
                        <form action="proses_tambah_mahasiswa.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            <div class="form-group">
                                <label for="nim">NIM:</label>
                                <input type="text" class="form-control radius" id="nim" name="nim" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama:</label>
                                <input type="text" class="form-control radius" id="nama" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label for="tahun_akademik">Tahun Akademik:</label>
                                <input type="text" class="form-control radius" id="tahun_akademik" name="tahun_akademik" required>
                            </div>
                            <div class="form-group">
                                <label for="jk">Jenis Kelamin:</label>
                                <select class="form-control radius" id="jk" name="jk" required>
                                    <option value="Pria">Pria</option>
                                    <option value="Wanita">Wanita</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control radius" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control radius" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                            <label for="tahun_akademik">Tahun Akademik</label>
                            <input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" required>
                        </div>
                            <div class="form-group">
                                <label for="prodi">Program Studi:</label>
                                <select class="form-control radius" id="prodi" name="prodi" required>
                                    <?php while ($row = mysqli_fetch_assoc($result_prodi)) : ?>
                                        <option value="<?= $row['id_prodi']; ?>"><?= $row['nama_prodi']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="foto">Foto:</label>
                                <input type="file" class="form-control-file" id="foto" name="foto" required>
                            </div>
                            <div class="form-group">
                            <label for="semester">Semester</label>
                            <input type="text" class="form-control" id="semester" name="semester" placeholder="Masukkan Semester">
                            </div>
                            <div class="form-group">
                                <label for="mata_kuliah_1">Mata Kuliah 1:</label>
                                <select class="form-control radius" id="mata_kuliah_1" name="mata_kuliah_1" onchange="calculateTotalSKS()" required>
                                    <option value="">Pilih Mata Kuliah</option>
                                    <?php mysqli_data_seek($result_matkul, 0); // Reset pointer to beginning ?>
                                    <?php while ($row = mysqli_fetch_assoc($result_matkul)) : ?>
                                        <option value="<?= $row['id_mk']; ?>" data-sks="<?= $row['sks']; ?>"><?= $row['nama_mk']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mata_kuliah_2">Mata Kuliah 2:</label>
                                <select class="form-control radius" id="mata_kuliah_2" name="mata_kuliah_2" onchange="calculateTotalSKS()" required>
                                    <option value="">Pilih Mata Kuliah</option>
                                    <?php mysqli_data_seek($result_matkul, 0); // Reset pointer to beginning ?>
                                    <?php while ($row = mysqli_fetch_assoc($result_matkul)) : ?>
                                        <option value="<?= $row['id_mk']; ?>" data-sks="<?= $row['sks']; ?>"><?= $row['nama_mk']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mata_kuliah_3">Mata Kuliah 3:</label>
                                <select class="form-control radius" id="mata_kuliah_3" name="mata_kuliah_3" onchange="calculateTotalSKS()" required>
                                    <option value="">Pilih Mata Kuliah</option>
                                    <?php mysqli_data_seek($result_matkul, 0); // Reset pointer to beginning ?>
                                    <?php while ($row = mysqli_fetch_assoc($result_matkul)) : ?>
                                        <option value="<?= $row['id_mk']; ?>" data-sks="<?= $row['sks']; ?>"><?= $row['nama_mk']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="total_sks">Total SKS:</label>
                                <input type="text" class="form-control radius" id="total_sks" name="total_sks" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Mahasiswa</button>
                        </form>
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script>
        function calculateTotalSKS() {
            var sks1 = parseInt(document.getElementById('mata_kuliah_1').options[document.getElementById('mata_kuliah_1').selectedIndex].getAttribute('data-sks')) || 0;
            var sks2 = parseInt(document.getElementById('mata_kuliah_2').options[document.getElementById('mata_kuliah_2').selectedIndex].getAttribute('data-sks')) || 0;
            var sks3 = parseInt(document.getElementById('mata_kuliah_3').options[document.getElementById('mata_kuliah_3').selectedIndex].getAttribute('data-sks')) || 0;
            var totalSKS = sks1 + sks2 + sks3;
            document.getElementById('total_sks').value = totalSKS;
            if (totalSKS > 24) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Total SKS tidak boleh melebihi 24!',
                });
                document.getElementById('total_sks').value = '';
                return false;
            }
            return true;
        }

        function validateForm() {
            return calculateTotalSKS();
        }
    </script>
</body>

</html>
