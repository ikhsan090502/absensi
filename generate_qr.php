<?php
require_once 'vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jadwal = $_POST['jadwal'];

    // Buat QR code
    $qrCode = new QrCode($jadwal);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Simpan QR code ke file
    $filePath = 'qr_codes/' . uniqid() . '.png';
    $result->saveToFile($filePath);

    // Redirect ke halaman yang menampilkan QR code atau kirim email ke dosen
    header('Location: lihat_qr.php?file=' . urlencode($filePath));
    exit;
}
?>
