<?php
// api/proses-sewa-langsung.php
date_default_timezone_set('Asia/Makassar'); 
require_once '../config/connection.php';
require_once '../config/auth.php'; // 🟢 Aktifkan ini agar proteksi login & session user berjalan lurus

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../locker-selection.php");
    exit;
}

// Ambil data dari form locker-selection.php
$locker_id = isset($_POST['locker_id']) ? intval($_POST['locker_id']) : 0;
$durasi_jam = isset($_POST['durasi_jam']) ? intval($_POST['durasi_jam']) : 0;

// Ambil ID pengguna dari array session yang sekarang sudah aktif
if (isset($_SESSION['user']['id'])) {
    $user_id = intval($_SESSION['user']['id']);
} else {
    $user_id = 10; // Fallback aman jika digunakan saat pengujian tanpa login
}

// Validasi limit maksimal 3 jam
if ($durasi_jam < 1 || $durasi_jam > 3 || $locker_id === 0) {
    header("Location: ../locker-selection.php?error=durasi_tidak_valid");
    exit;
}

// --- LOGIKA DATABASE KULOCKER BARU (MENGGUNAKAN JAM RIIL) ---
$sekarang = time(); // Ambil timestamp detik saat ini
$tanggal_mulai = date('Y-m-d H:i:s', $sekarang); 

// Hitung waktu selesai: Waktu sekarang + (Durasi Jam * 3600 detik)
$waktu_selesai_timestamp = $sekarang + ($durasi_jam * 3600);
$tanggal_selesai = date('Y-m-d H:i:s', $waktu_selesai_timestamp); 

$kode_akses_dummy = strval(rand(100000, 999999));

// 1. Insert ke tabel pemesanan dengan status langsung 'aktif'
$query_order = "INSERT INTO pemesanan (user_id, locker_id, tanggal_mulai, tanggal_selesai, status, kode_akses) VALUES (?, ?, ?, ?, 'aktif', ?)";
$stmt_order = mysqli_prepare($conn, $query_order);
mysqli_stmt_bind_param($stmt_order, "iisss", $user_id, $locker_id, $tanggal_mulai, $tanggal_selesai, $kode_akses_dummy);
mysqli_stmt_execute($stmt_order);
$pemesanan_id = mysqli_insert_id($conn);

// 2. Insert ke tabel pembayaran (status lunas agar history database tidak crash)
$query_pay = "INSERT INTO pembayaran (pemesanan_id, jumlah, metode, status, bukti) VALUES (?, 0, 'gratis', 'lunas', NULL)";
$stmt_pay = mysqli_prepare($conn, $query_pay);
mysqli_stmt_bind_param($stmt_pay, "i", $pemesanan_id);
mysqli_stmt_execute($stmt_pay);

// 3. Update status loker di tabel lockers menjadi 'terpakai'
$query_update_locker = "UPDATE lockers SET status = 'terpakai' WHERE id = ?";
$stmt_lkr = mysqli_prepare($conn, $query_update_locker);
mysqli_stmt_bind_param($stmt_lkr, "i", $locker_id);
mysqli_stmt_execute($stmt_lkr);

// --- REDIRECT BAWA DATA KE PROSES-SUKSES.PHP MENGGUNAKAN FORM AUTO-SUBMIT HTML ---
$pembayaran_id_dummy = "INV-" . $pemesanan_id . "-" . time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menyiapkan Loker Anda...</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        
        .loading-container { width: 100%; max-width: 400px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 40px 30px; text-align: center; border: 1px solid #e5e7eb; }
        
        /* Animasi Spinner Lingkaran Putar Minimalis */
        .spinner { width: 55px; height: 55px; border: 5px solid #f3f3f3; border-top: 5px solid #0f172a; border-radius: 50%; margin: 0 auto 24px; animation: spin 1s linear infinite; }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .title { font-size: 1.3rem; font-weight: 700; color: #111827; margin-bottom: 10px; }
        .subtitle { font-size: 0.9rem; color: #6b7280; line-height: 1.5; }
    </style>
</head>
<body>

    <div class="loading-container">
        <div class="spinner"></div>
        
        <h1 class="title">Menyiapkan Akses Loker</h1>
        <p class="subtitle">Sistem sedang memproses hak akses IoT Anda. Mohon tunggu sejenak...</p>
    </div>

    <form id="redirectForm" action="../proses-sukses.php" method="POST">
        <input type="hidden" name="pembayaran_id" value="<?= $pembayaran_id_dummy ?>">
        <input type="hidden" name="pemesanan_id" value="<?= $pemesanan_id ?>">
    </form>

    <script>
        // Jalankan langsung fungsi loading saat halaman terbuka
        const form = document.getElementById('redirectForm');

        // Mengarahkan ke halaman sukses setelah menahan spinner selama 2 detik
        setTimeout(() => {
            form.submit(); 
        }, 2000);
    </script>
</body>
</html>