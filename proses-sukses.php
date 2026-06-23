<?php
// proses-sukses.php
require 'config/auth.php';
require_once 'config/connection.php';

// Tangkap parameter POST dari file proses sewa langsung
$pembayaran_id_raw = $_POST['pembayaran_id'] ?? '';
$pemesanan_id = isset($_POST['pemesanan_id']) ? intval($_POST['pemesanan_id']) : 0;

$data_sewa = null;

if ($pemesanan_id > 0) {
    // LANGSUNG QUERY AMBIL RINCIAN: Karena status di DB sudah otomatis diset 'aktif' oleh file proses
    $query_select_sewa = "SELECT p.tanggal_mulai, p.kode_akses, l.kode_loker, l.lokasi 
                        FROM pemesanan p 
                        JOIN lockers l ON p.locker_id = l.id 
                        WHERE p.id = ?";
    $stmt_sewa = mysqli_prepare($conn, $query_select_sewa);
    mysqli_stmt_bind_param($stmt_sewa, "i", $pemesanan_id);
    mysqli_stmt_execute($stmt_sewa);
    $result_sewa = mysqli_stmt_get_result($stmt_sewa);
    $data_sewa = mysqli_fetch_assoc($result_sewa);
} else {
    header("Location: dashboard-utama.php");
    exit;
}

// Jika data gagal ditarik dari DB, kembalikan ke dashboard
if (!$data_sewa) {
    header("Location: dashboard-utama.php");
    exit;
}

// Generate URL QR Code berbasis QR Server API (Sangat stabil untuk localhost)
// 1. Ambil data PIN akses loker asli yang barusan disimpan di database
$qr_data = $data_sewa['kode_akses']; 

// 2. Bungkus ke dalam URL QR Server API (Ukuran 250x250 piksel, enkripsi URL aman)
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewa Loker Berhasil - KuLocker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .success-container { width: 100%; max-width: 450px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 30px; text-align: center; border: 1px solid #e5e7eb; }
        
        .success-icon { width: 70px; height: 70px; background: #d1fae5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2.5rem; }
        
        .title { font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 8px; }
        .subtitle { font-size: 0.95rem; color: #6b7280; margin-bottom: 24px; }
        
        /* Box QR Code */
        .qr-box { background: #ffffff; border: 2px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .qr-image { width: 180px; height: 180px; display: block; margin: 0 auto; }

        /* Box PIN */
        .pin-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .pin-label { font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .pin-number { font-size: 2.2rem; font-weight: 800; color: #0f172a; letter-spacing: 4px; }
        
        .details-box { text-align: left; background: #f9fafb; border-radius: 12px; padding: 16px; margin-bottom: 24px; border: 1px solid #f1f5f9; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #4b5563; }
        .detail-row:last-child { margin-bottom: 0; }
        .detail-label { color: #9ca3af; }
        .detail-value { font-weight: 600; color: #1f2937; }
        
        .btn-dashboard { width: 100%; padding: 14px; background-color: #0f172a; color: #ffffff; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .btn-dashboard:hover { background-color: #1e293b; }
    </style>
</head>
<body>

    <div class="success-container">
        <div class="success-icon">
            <i class="ti ti-circle-check-filled"></i>
        </div>
        
        <h1 class="title">Sewa Loker Berhasil!</h1>
        <p class="subtitle">Loker kamu telah aktif. Silakan arahkan QR Code ini ke area scanner loker fisik untuk membuka pintu.</p>
        
        <div class="qr-box">
            <img src="<?= $qr_code_url ?>" alt="QR Code Akses Loker" class="qr-image">
        </div>

        <div class="pin-box">
            <p class="pin-label">PIN Akses Alternatif</p>
            <div class="pin-number"><?= htmlspecialchars($data_sewa['kode_akses']) ?></div>
        </div>
        
        <div class="details-box">
            <div class="detail-row">
                <span class="detail-label">Nomor Pemesanan</span>
                <span class="detail-value">#<?= $pemesanan_id ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kode Loker</span>
                <span class="detail-value"><?= htmlspecialchars($data_sewa['kode_loker']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Lokasi</span>
                <span class="detail-value"><?= htmlspecialchars($data_sewa['lokasi']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Mulai</span>
                <span class="detail-value"><?= date('d M Y', strtotime($data_sewa['tanggal_mulai'])) ?></span>
            </div>
        </div>

        <a href="dashboard-utama.php" class="btn-dashboard">
            <i class="ti ti-layout-dashboard"></i> Kembali ke Dashboard
        </a>
    </div>

    <script>
        // Masukkan state baru agar tombol back terkunci
        window.history.pushState(null, "", window.location.href);
        
        // Ketika user mencoba menekan tombol kembali/undo di browser
        window.onpopstate = function () {
            // Tolak mentah-mentah dan paksa tetap di halaman sukses
            window.history.pushState(null, "", window.location.href);
            
            // Atau jika mau ditendang ke dashboard-utama silakan aktifkan baris di bawah:
            window.location.href = 'dashboard-utama.php';
        };
    </script>
</body>
</html>

</body>
</html>