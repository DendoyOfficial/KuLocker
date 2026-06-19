<?php
// Masukkan library QR Code
require_once 'qrlib.php';

// Ambil nominal dari parameter URL
$nominal = isset($_GET['amount']) ? intval($_GET['amount']) : 0;

// Ini adalah format string dasar (Payload) QRIS Statis dummy untuk simulasi.
// Di dunia nyata, ini adalah kode merchant QRIS Anda.
$qris_base = "00020101021126570011ID.CO.QRIS.WWW011893600000000001111102061234560303UMI51440014ID.CO.QRIS.WWW0215ID10203040506070303UMI52045999530336054";

// Fungsi sederhana untuk menyisipkan nominal ke dalam string QRIS (Simulasi parsing standar EMVCo)
// Catatan: Ini untuk keperluan visual & simulasi lokal agar dosen melihat kodenya berubah sesuai nominal.
$qris_dinamis = $qris_base . "5502015802ID5911KULOCKER_ID6007MATARAM61058311162070703A01"; 
if ($nominal > 0) {
    // Menyisipkan panjang karakter nominal dan nilai nominalnya
    $len = sprintf("%02d", strlen($nominal));
    $qris_dinamis .= "54" . $len . $nominal;
}

// Set header agar browser tahu bahwa file ini mengeluarkan gambar, bukan text/HTML
header('Content-Type: image/png');

// Generate QR Code langsung ke browser (Level cetak: L, Ukuran pixel: 6)
QRcode::png($qris_dinamis, null, QR_ECLEVEL_L, 6, 2);
exit;