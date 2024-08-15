<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $tahun_akademik = $_POST['tahun_akademik'];
    $jk = $_POST['jk'];
    $email = $_POST['email'];
    $id_prodi = $_POST['id_prodi'];
    $semester = $_POST['semester'];
    $mata_kuliah_1 = $_POST['mata_kuliah_1'];
    $mata_kuliah_2 = $_POST['mata_kuliah_2'];
    $mata_kuliah_3 = $_POST['mata_kuliah_3'];

    // Upload foto
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $path = "img/" . $foto;
    if (move_uploaded_file($tmp, $path)) {
        // Query untuk menambahkan data mahasiswa
        $query = "INSERT INTO mahasiswa (nim, nama, tahun_akademik, jk, email, id_prodi, foto, semester, mata_kuliah_1, mata_kuliah_2, mata_kuliah_3) 
                  VALUES ('$nim', '$nama', '$tahun_akademik', '$jk', '$email', '$id_prodi', '$foto', '$semester', '$mata_kuliah_1', '$mata_kuliah_2', '$mata_kuliah_3')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data mahasiswa berhasil ditambahkan!'); window.location.href='lihatmhs.php';</script>";
        } else {
            echo "<script>alert('Data mahasiswa gagal ditambahkan!'); window.location.href='tambah_mahasiswa.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal mengunggah foto!'); window.location.href='tambah_mahasiswa.php';</script>";
    }
}
?>
