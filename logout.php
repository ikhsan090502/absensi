<?php 
include('koneksi.php');
session_start();

if (!isset($_SESSION['nipy']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$nipy = $_SESSION['nipy'];
$role = $_SESSION['role'];
$berhasil = true;

// Pilih tabel dan kolom berdasarkan peran pengguna
$table = "";
$id_column = "";  // Kolom yang digunakan untuk identifikasi pengguna
switch ($role) {
    case 'operator':
        $table = "operator";
        $id_column = "nipy";
        break;
    case 'dosen':
        $table = "dosen";
        $id_column = "nipy";
        break;
    case 'tendik':
        $table = "tenaga_kependidikan";
        $id_column = "nipy";  // Pastikan ini benar, atau ganti dengan kolom yang sesuai
        break;
    case 'mahasiswa':
        $table = "mahasiswa";
        $id_column = "nim";  // Ganti dengan kolom yang benar sesuai struktur tabel mahasiswa
        $nipy = $_SESSION['nim'];  // Ambil NIM dari session
        break;
    default:
        header("Location: login.php");
        exit;
}

// Perbarui waktu login terakhir di tabel yang sesuai
if ($sql_login = mysqli_query($koneksi, "UPDATE $table SET last_login=now() WHERE $id_column='$nipy'")) {
    $_SESSION = [];
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
} else {
    echo mysqli_error($koneksi);
}
?>
