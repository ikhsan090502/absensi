<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit;
}
include "koneksi.php";

$id_mk = $_GET['id_mk'];

// Hapus mata kuliah
$sql = "DELETE FROM mata_kuliah WHERE id_mk = '$id_mk'";
mysqli_query($koneksi, $sql);

header("Location: lihatmatkul.php");
exit;
?>
