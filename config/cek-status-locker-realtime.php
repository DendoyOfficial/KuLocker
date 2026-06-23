<?php
// API untuk mendapatkan status locker real-time berdasarkan akses log
require_once "auth.php";
require_once "connection.php";

header('Content-Type: application/json');
date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false]);
    exit;
}

$locker_id = intval($_GET['locker_id'] ?? 0);

if ($locker_id === 0) {
    echo json_encode(['success' => false]);
    exit;
}

// ✅ CEK AKSES LOG TERBARU UNTUK LOCKER INI
// Cari pemesanan aktif untuk locker ini, lalu cek akses log terakhirnya
$query = "SELECT al.jenis FROM akses_log al
          WHERE al.pemesanan_id IN (
              SELECT p.id FROM pemesanan p 
              WHERE p.locker_id = $locker_id AND p.status = 'aktif'
          )
          ORDER BY al.waktu_akses DESC
          LIMIT 1";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Jika akses terakhir adalah 'buka', locker sedang dipakai
    $is_in_use = ($row['jenis'] === 'buka');
    echo json_encode(['success' => true, 'is_in_use' => $is_in_use, 'status_display' => $is_in_use ? 'Sedang Digunakan' : 'Tidak Digunakan']);
} else {
    // Tidak ada akses log, berarti locker kosong atau belum dipakai
    echo json_encode(['success' => true, 'is_in_use' => false, 'status_display' => 'Tidak Digunakan']);
}

?>
