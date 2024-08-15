<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION["login"]) || $_SESSION['role'] !== 'tenaga_kependidikan') {
    header("Location: login.php");
    exit;
}

$nip = $_SESSION['nipy'];
$data = $_POST['data'];

// Misalkan data QR Code berisi ID mata kuliah dan NIM mahasiswa
list($id_mk, $nim) = explode('-', $data);

// Periksa apakah mahasiswa valid
$result = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nim = '$nim'");
if (mysqli_num_rows($result) === 0) {
    echo 'NIM tidak valid';
    exit;
}

// Tambahkan entri absensi
$waktu_absen = date('Y-m-d H:i:s');
$query = "INSERT INTO absensi (nim, id_mk, nipy, waktu_absen) VALUES ('$nim', '$id_mk', '$nip', '$waktu_absen')";
if (mysqli_query($koneksi, $query)) {
    echo 'Absensi berhasil dilakukan';
} else {
    echo 'Terjadi kesalahan: ' . mysqli_error($koneksi);
}
?>
