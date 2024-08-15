<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Ambil data dari formulir
$id_mk = $_POST['id_mk'];
$id_dosen = $_POST['id_dosen'];
$waktu_mulai = $_POST['waktu_mulai'];
$waktu_selesai = $_POST['waktu_selesai'];

// Validasi data (opsional, tergantung kebutuhan)

// Query untuk menyimpan data
$query = "
    INSERT INTO jadwal (id_mk, id_dosen, waktu_mulai, waktu_selesai) 
    VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'iiss', $id_mk, $id_dosen, $waktu_mulai, $waktu_selesai);

if (mysqli_stmt_execute($stmt)) {
    // Redirect ke halaman lihatjadwal.php setelah berhasil
    header("Location: lihatjadwal.php");
    exit;
} else {
    // Tampilkan pesan error jika ada masalah
    echo "Error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
