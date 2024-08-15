<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Cek apakah parameter nim ada
if (isset($_GET['nim'])) {
    $nim = mysqli_real_escape_string($koneksi, $_GET['nim']);

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Hapus data mahasiswa dari tabel mahasiswa
        $query_delete_mahasiswa = "DELETE FROM mahasiswa WHERE nim = '$nim'";
        if (!mysqli_query($koneksi, $query_delete_mahasiswa)) {
            throw new Exception("Gagal menghapus mahasiswa: " . mysqli_error($koneksi));
        }

        // Commit transaksi
        mysqli_commit($koneksi);

        // Redirect dengan popup
        echo "<script>
                window.location.href='lihatmhs.php';
              </script>";
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($koneksi);
        echo "<script>
                alert('Gagal menghapus mahasiswa: " . $e->getMessage() . "');
                window.location.href='lihatmhs.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('Parameter nim tidak ditemukan');
            window.location.href='lihatmhs.php';
          </script>";
    exit();
}
?>
