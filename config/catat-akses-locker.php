<?php
// File untuk mencatat pembukaan & penutupan locker
require_once "auth.php";
require_once "connection.php";

header('Content-Type: application/json');
date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$action = $_POST['action'] ?? '';
$pemesanan_id = intval($_POST['pemesanan_id'] ?? 0);

if (empty($action) || $pemesanan_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// ✅ CATAT PEMBUKAAN LOCKER
if ($action === 'buka') {
    $query = "INSERT INTO akses_log (pemesanan_id, user_id, jenis, status, keterangan) 
              VALUES ($pemesanan_id, $user_id, 'buka', 'berhasil', 'Loker dibuka oleh user')";
    
    if (mysqli_query($conn, $query)) {
        // Update pemesanan menjadi aktif jika masih pending
        mysqli_query($conn, "UPDATE pemesanan SET status = 'aktif' WHERE id = $pemesanan_id AND status = 'pending'");
        
        echo json_encode(['success' => true, 'message' => 'Locker opened successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record access']);
    }
}
// ✅ CATAT PENUTUPAN LOCKER
elseif ($action === 'tutup') {
    // Ambil pemesanan dan locker untuk update
    $query_ambil = "SELECT p.tanggal_selesai, p.locker_id FROM pemesanan p WHERE id = $pemesanan_id LIMIT 1";
    $result = mysqli_query($conn, $query_ambil);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $locker_id = $row['locker_id'];
        
        // Catat penutupan
        $query_tutup = "INSERT INTO akses_log (pemesanan_id, user_id, jenis, status, keterangan) 
                       VALUES ($pemesanan_id, $user_id, 'tutup', 'berhasil', 'Loker ditutup oleh user')";
        
        if (mysqli_query($conn, $query_tutup)) {
            // Update status pemesanan menjadi selesai dengan waktu terkini
            $query_selesai = "UPDATE pemesanan SET status = 'selesai', tanggal_selesai = NOW() WHERE id = $pemesanan_id";
            mysqli_query($conn, $query_selesai);
            
            // ✅ PENTING: Update status locker menjadi "tersedia" otomatis
            $query_update_locker = "UPDATE lockers SET status = 'tersedia' WHERE id = $locker_id";
            mysqli_query($conn, $query_update_locker);
            
            echo json_encode(['success' => true, 'message' => 'Locker closed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to record closing']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Pemesanan tidak ditemukan']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
}

?>
