<?php
include "koneksi.php";

$query = "SELECT id_mk, nama_mk FROM mata_kuliah";
$result = mysqli_query($koneksi, $query);

$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['courses' => $courses]);
