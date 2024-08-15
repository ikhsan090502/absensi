<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Ambil data dari form
$nipy_lama = $_POST['nipy'];
$nipy_baru = $_POST['nipy'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$email = $_POST['email'];
$password_baru = $_POST['password_baru'];
$konfirmasi_password = $_POST['konfirmasi_password'];
$foto_lama = $_POST['foto_lama'];
$foto_baru = $_FILES['foto']['name'];

// Jika ada foto baru diupload
if ($foto_baru) {
    $target = "img/" . basename($foto_baru);
    move_uploaded_file($_FILES['foto']['tmp_name'], $target);
    $foto = $foto_baru;
} else {
    $foto = $foto_lama;
}

// Validasi dan update password jika diisi (tanpa hashing)
if (!empty($password_baru) && $password_baru === $konfirmasi_password) {
    // Update password tanpa hashing
    $query_update = "UPDATE dosen SET nipy='$nipy_baru', nama='$nama', jenis_kelamin='$jenis_kelamin', email='$email', foto='$foto', password='$password_baru' WHERE nipy='$nipy_lama'";
} else {
    $query_update = "UPDATE dosen SET nipy='$nipy_baru', nama='$nama', jenis_kelamin='$jenis_kelamin', email='$email', foto='$foto' WHERE nipy='$nipy_lama'";
}

// Eksekusi query
if (mysqli_query($koneksi, $query_update)) {
    echo "<script>alert('Data dosen berhasil diupdate'); window.location.href='lihatdosen.php';</script>";
} else {
    echo "Gagal mengupdate data dosen: " . mysqli_error($koneksi);
}

?>
