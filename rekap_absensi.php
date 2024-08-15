<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$rekap_type = $_POST['rekapType'] ?? '';

if ($rekap_type == 'mahasiswa') {
    $query_rekap = "
    SELECT 
        a.waktu_absen, 
        a.nim, 
        m.nama AS nama_mahasiswa, 
        mk.nama_mk, 
        d.nama AS nama_dosen 
    FROM 
        absensi a
    LEFT JOIN 
        mahasiswa m ON a.nim = m.nim
    LEFT JOIN 
        mata_kuliah mk ON a.id_mk = mk.id_mk
    LEFT JOIN 
        dosen d ON mk.id_dosen = d.id
    WHERE
        a.nim IS NOT NULL";  // Sesuaikan query dengan kebutuhan Anda

    $result_rekap = mysqli_query($koneksi, $query_rekap);
    if (!$result_rekap) {
        die('Query Error: ' . mysqli_error($koneksi));
    }

    // Output data atau proses rekap sesuai dengan kebutuhan Anda
    // Misalnya, membuat file Excel atau PDF, menampilkan data, dll.
} elseif ($rekap_type == 'tendik') {
    $query_rekap = "
    SELECT 
        a.waktu_absen, 
        a.nim, 
        m.nama AS nama_mahasiswa, 
        mk.nama_mk, 
        t.nama AS nama_tendik 
    FROM 
        absensi a
    LEFT JOIN 
        mahasiswa m ON a.nim = m.nim
    LEFT JOIN 
        mata_kuliah mk ON a.id_mk = mk.id_mk
    LEFT JOIN 
        tenaga_kependidikan t ON a.nim = t.nipy
    WHERE
        t.nipy IS NOT NULL";  // Sesuaikan query dengan kebutuhan Anda

    $result_rekap = mysqli_query($koneksi, $query_rekap);
    if (!$result_rekap) {
        die('Query Error: ' . mysqli_error($koneksi));
    }

    // Output data atau proses rekap sesuai dengan kebutuhan Anda
    // Misalnya, membuat file Excel atau PDF, menampilkan data, dll.
} else {
    die('Jenis rekap tidak valid.');
}

$result_rekap = mysqli_query($koneksi, $query_rekap);
if (!$result_rekap) {
    die('Query Error: ' . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_operator.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Rekap Absensi</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="dashboard_operator.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Rekap Data
            </div>

            <!-- Nav Item - Rekap Mahasiswa -->
            <li class="nav-item">
                <a class="nav-link" href="rekap_absensi.php?rekap_type=mahasiswa">
                    <i class="fas fa-fw fa-user-graduate"></i>
                    <span>Rekap Mahasiswa</span></a>
            </li>

            <!-- Nav Item - Rekap Tendik -->
            <li class="nav-item">
                <a class="nav-link" href="rekap_absensi.php?rekap_type=tendik">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Rekap Tendik</span></a>
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

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Operator</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Rekap Absensi <?= ucfirst($rekap_type) ?></h1>

                    <!-- Tabel Rekap -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Rekap Absensi <?= ucfirst($rekap_type) ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Waktu Absen</th>
                                            <?php if ($rekap_type == 'mahasiswa'): ?>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Mata Kuliah</th>
                                                <th>Nama Dosen</th>
                                            <?php elseif ($rekap_type == 'tendik'): ?>
                                                <th>NIPY</th>
                                                <th>Nama Tendik</th>
                                                <th>Jabatan</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result_rekap)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['waktu_absen'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <?php if ($rekap_type == 'mahasiswa'): ?>
                                                    <td><?= htmlspecialchars($row['nim'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_mahasiswa'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_mk'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_dosen'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <?php elseif ($rekap_type == 'tendik'): ?>
                                                    <td><?= htmlspecialchars($row['nipy_tendik'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_tendik'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row['jabatan'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Download Rekap -->
                    <a href="download_rekap.php?rekap_type=<?= htmlspecialchars($rekap_type, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success">
                        <i class="fas fa-download"></i> Download Rekap
                    </a>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; UNDHA AUB 2024</span>
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
