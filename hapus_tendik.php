<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

if (isset($_GET["nipy"])) {
    $nipy = $_GET["nipy"];
    $query = "DELETE FROM tenaga_kependidikan WHERE nipy = '$nipy'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script> window.location.href='lihatendik.php';</script>";
    } else {
        echo "<script> alert('Gagal menghapus data.'); </script>";
    }
}
?>
