<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$id_prodi = $_GET["id_prodi"];
$query = "DELETE FROM prodi WHERE id_prodi = '$id_prodi'";
mysqli_query($koneksi, $query);

header("Location: lihatprodi.php");
exit;
?>
