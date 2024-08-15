<?php
include "koneksi.php";

// Mengambil data dari form
$nipy_baru = $_POST['nipy'];
$nama = $_POST['nama'];
$foto_lama = $_POST['foto_lama'];
$password_baru = $_POST['password_baru'];
$konfirmasi_password = $_POST['konfirmasi_password'];

// Jika ada upload foto baru, gunakan foto baru; jika tidak, gunakan foto lama
if ($_FILES['foto']['name']) {
    $foto = $_FILES['foto']['name'];
    $target = "img/" . basename($foto);
    move_uploaded_file($_FILES['foto']['tmp_name'], $target);
} else {
    $foto = $foto_lama;
}

// Jika password baru diisi dan konfirmasi password cocok, update password
if (!empty($password_baru) && $password_baru === $konfirmasi_password) {
    // Simpan password tanpa hashing
    $query_update = "UPDATE tenaga_kependidikan SET nipy='$nipy_baru', nama='$nama', foto='$foto', password='$password_baru' WHERE nipy='$nipy_baru'";
} else {
    // Jika tidak ada perubahan password, update data tanpa mengubah password
    $query_update = "UPDATE tenaga_kependidikan SET nipy='$nipy_baru', nama='$nama', foto='$foto' WHERE nipy='$nipy_baru'";
}

if (mysqli_query($koneksi, $query_update)) {
    echo "<script>alert('Data berhasil diupdate!'); window.location.href='lihatendik.php';</script>";
} else {
    echo "Gagal mengupdate data!";
}
?>
