<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Cek apakah parameter nipy ada
if (isset($_GET['nipy'])) {
    $nipy = mysqli_real_escape_string($koneksi, $_GET['nipy']);

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Hapus mata kuliah yang diambil oleh dosen
        $query_delete_mk = "DELETE FROM mengajar WHERE nipy = '$nipy'";
        if (!mysqli_query($koneksi, $query_delete_mk)) {
            throw new Exception("Gagal menghapus mata kuliah");
        }

        // Hapus data dosen dari tabel dosen
        $query_delete_dosen = "DELETE FROM dosen WHERE nipy = '$nipy'";
        if (!mysqli_query($koneksi, $query_delete_dosen)) {
            throw new Exception("Gagal menghapus dosen");
        }

        // Commit transaksi
        mysqli_commit($koneksi);

        // Redirect dengan popup
        echo "<script>
                window.location.href='lihatdosen.php';
              </script>";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($koneksi);
        echo "<script>
                alert('Gagal menghapus dosen: " . $e->getMessage() . "');
                window.location.href='lihatdosen.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Parameter nipy tidak ditemukan');
            window.location.href='lihatdosen.php';
          </script>";
}
?>
