<?php
include "koneksi.php";

$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$email = $_POST['email'];
$tahun_akademik = $_POST['tahun_akademik'];
$semester_id = $_POST['semester_id'];
$password = $_POST['password'];

$foto = $_FILES['foto']['name'];
$tmp = $_FILES['foto']['tmp_name'];
$foto_path = 'img/' . $foto;

// Periksa jika foto diupload
if ($foto) {
    $foto_baru = date('dmYHis') . $foto;
    $path = "img/" . $foto_baru;
    if (move_uploaded_file($tmp, $path)) {
        // Dapatkan foto lama
        $query_old = "SELECT foto FROM mahasiswa WHERE nim='$nim'";
        $result_old = mysqli_query($koneksi, $query_old);
        $row_old = mysqli_fetch_assoc($result_old);

        // Hapus foto lama
        if (is_file("img/" . $row_old['foto'])) {
            unlink("img/" . $row_old['foto']);
        }

        // Update data mahasiswa
        if (!empty($password)) {
            $query = "UPDATE mahasiswa SET nama = '$nama', email = '$email', jk = '$jenis_kelamin', foto = '$foto_baru', password = '$password' WHERE nim = '$nim'";
        } else {
            $query = "UPDATE mahasiswa SET nama = '$nama', email = '$email', jk = '$jenis_kelamin', foto = '$foto_baru' WHERE nim = '$nim'";
        }
    }
} else {
    // Update data mahasiswa tanpa foto
    if (!empty($password)) {
        $query = "UPDATE mahasiswa SET nama = '$nama', email = '$email', jk = '$jenis_kelamin', password = '$password' WHERE nim = '$nim'";
    } else {
        $query = "UPDATE mahasiswa SET nama = '$nama', email = '$email', jk = '$jenis_kelamin' WHERE nim = '$nim'";
    }
}

$result = mysqli_query($koneksi, $query);

if ($result) {
    header("Location: lihatmhs.php");
} else {
    echo "Gagal mengupdate data mahasiswa.";
}


?>
