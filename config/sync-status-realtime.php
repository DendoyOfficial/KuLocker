<?php
// API untuk real-time sync status locker antara admin dan user
require_once "auth.php";
require_once "connection.php";

header('Content-Type: application/json');
date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user']['id'];
$pemesanan_id = intval($_GET['pemesanan_id'] ?? 0);

if ($pemesanan_id === 0) {
    echo json_encode(['success' => false]);
    exit;
}

// ✅ Ambil data pemesanan dan locker terbaru
$query = "SELECT p.id, p.status, p.locker_id, p.tanggal_selesai, l.status as locker_status
          FROM pemesanan p
          JOIN lockers l ON p.locker_id = l.id
          WHERE p.id = $pemesanan_id AND p.user_id = $user_id
          LIMIT 1";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $pemesanan = mysqli_fetch_assoc($result);
    $locker_id = $pemesanan['locker_id'];
    $locker_status = $pemesanan['locker_status'];
    $pemesanan_status = $pemesanan['status'];
    
    // ✅ CEK AKSES LOG TERAKHIR
    $query_akses = "SELECT jenis FROM akses_log 
                    WHERE pemesanan_id = $pemesanan_id 
                    ORDER BY waktu_akses DESC 
                    LIMIT 1";
    $result_akses = mysqli_query($conn, $query_akses);
    
    $is_open = false;
    if ($result_akses && mysqli_num_rows($result_akses) > 0) {
        $akses = mysqli_fetch_assoc($result_akses);
        $is_open = ($akses['jenis'] === 'buka');
    }
    
    // ✅ LOGIKA SINKRONISASI STATUS
    // Jika locker status berubah menjadi "tersedia" tapi pemesanan masih aktif = admin override
    // Dalam hal ini, pemesanan harus di-selesaikan
    if ($locker_status === 'tersedia' && $pemesanan_status === 'aktif' && !$is_open) {
        // Auto-complete pemesanan jika locker di-reset admin
        $query_complete = "UPDATE pemesanan SET status = 'selesai', tanggal_selesai = NOW() 
                          WHERE id = $pemesanan_id";
        mysqli_query($conn, $query_complete);
        
        // Refresh data
        $pemesanan_status = 'selesai';
    }
    
    // ✅ CEK APAKAH HARUS JALANKAN TIMER ATAU TIDAK
    // Timer jalan jika: locker_status = 'terpakai' OR is_open = true
    $should_run_timer = ($locker_status === 'terpakai' || $is_open);
    
    echo json_encode([
        'success' => true,
        'pemesanan_status' => $pemesanan_status,
        'locker_status' => $locker_status,
        'is_open' => $is_open,
        'should_run_timer' => $should_run_timer,
        'tanggal_selesai' => $pemesanan['tanggal_selesai']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Pemesanan tidak ditemukan']);
}

?>
