<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

if (isset($_GET['id_jadwal'])) {
    $id_jadwal = $_GET['id_jadwal'];
    
    // Pastikan ID jadwal valid
    if (!empty($id_jadwal)) {
        // Query untuk menghapus jadwal
        $query = "DELETE FROM jadwal WHERE id_jadwal = ?";
        
        if ($stmt = mysqli_prepare($koneksi, $query)) {
            mysqli_stmt_bind_param($stmt, "i", $id_jadwal);
            if (mysqli_stmt_execute($stmt)) {
                // Redirect ke halaman lihat jadwal setelah berhasil menghapus
                header("Location: lihatjadwal.php?status=success");
            } else {
                echo "Error executing query: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing query: " . mysqli_error($koneksi);
        }
    } else {
        echo "Invalid ID";
    }
    mysqli_close($koneksi);
} else {
    echo "ID not set";
}
?>
