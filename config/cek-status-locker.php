<?php
// API untuk mengecek status akses locker terbaru
require_once "auth.php";
require_once "connection.php";

header('Content-Type: application/json');
date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false]);
    exit;
}

$pemesanan_id = intval($_GET['pemesanan_id'] ?? 0);

if ($pemesanan_id === 0) {
    echo json_encode(['success' => false]);
    exit;
}

// ✅ CEK AKSES LOG TERAKHIR
$query = "SELECT jenis FROM akses_log 
          WHERE pemesanan_id = $pemesanan_id 
          ORDER BY waktu_akses DESC 
          LIMIT 1";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Jika akses terakhir adalah 'buka', user sedang menggunakan locker
    $is_open = ($row['jenis'] === 'buka');
    echo json_encode(['success' => true, 'is_open' => $is_open]);
} else {
    // Belum ada akses log, berarti locker belum dibuka
    echo json_encode(['success' => true, 'is_open' => false]);
}

?>
