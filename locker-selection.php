<?php
// locker-selection.php
require 'config/auth.php'; 
require_once 'config/connection.php';

// 1. Tangkap parameter lokasi dari URL
if (!isset($_GET['lokasi']) || empty($_GET['lokasi'])) {
    header("Location: dashboard-utama.php"); 
    exit;
}

// Proses sewa loker
date_default_timezone_set('Asia/Makassar'); // 🟢 Pastikan ini ada di atas

$durasi_sewa_jam = 1; // Diambil dari pilihan user (1 jam)

// Menghitung tanggal selesai secara presisi menggunakan format datetime MySQL
$tanggal_mulai = date('Y-m-d H:i:s');
$tanggal_selesai = date('Y-m-d H:i:s', strtotime("+$durasi_sewa_jam hour"));

$lokasi_terpilih = $_GET['lokasi'];

// 2. QUERY UTAMA: Mengambil data loker sekaligus sisa waktu pemesanan yang masih aktif
$lockers_list = [];
$query = "SELECT l.id, l.kode_loker, l.ukuran, l.status, p.tanggal_selesai 
          FROM lockers l 
          LEFT JOIN pemesanan p ON l.id = p.locker_id AND p.status = 'aktif'
          WHERE l.lokasi = ? AND (l.is_deleted = 0 OR l.is_deleted IS NULL) 
          ORDER BY l.kode_loker ASC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $lokasi_terpilih);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $lockers_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Loker - KuLocker</title>
    <link rel="stylesheet" href="css/locker-selection.css">
</head>
<body class="app-body">

    <div class="locker-container">
        
        <div class="app-header">
            <button class="btn-back" onclick="window.location.href='dashboard-utama.php'">&larr;</button>
            <div class="header-text">
                <h1 class="header-title">Locker Selection</h1>
                <p class="header-subtitle"><?= htmlspecialchars($lokasi_terpilih) ?></p>
            </div>
        </div>

        <div class="status-legend">
            <div class="legend-item">
                <div class="legend-color color-available"></div> <span>Tersedia</span>
            </div>
            <div class="legend-item">
                <div class="legend-color color-unavailable"></div> <span>Terpakai / Rusak</span>
            </div>
            <div class="legend-item">
                <div class="legend-color color-selected"></div> <span>Pilihan Kamu</span>
            </div>
        </div>

        <div class="selection-layout">
            
            <div class="grid-area">
                <div class="locker-grid">
                    
                    <?php if (empty($lockers_list)): ?>
                        <p class="empty-notice">Belum ada loker yang terdaftar di lokasi ini.</p>
                    <?php else: ?>
                        <?php 
                        $counter = 0;
                        foreach ($lockers_list as $loker): 
                            $counter++;
                            
                            // Kondisi 1: LOKER TERSEDIA
                            if ($loker['status'] == 'tersedia') {
                                $btn_class = "btn-available";
                                $disabled = "";
                                $tooltip_teks = "Loker Tersedia";
                            } 
                            // Kondisi 2: LOKER RUSAK / DALAM PERAWATAN ADMIN
                            elseif ($loker['status'] == 'rusak') {
                                $btn_class = "btn-unavailable-seat"; // Sesuai request, pakai style terpakai
                                $disabled = "disabled";
                                $tooltip_teks = "⚠️ Loker Rusak ";
                            } 
                            // Kondisi 3: LOKER SEDANG TERPAKAI MAHASISWA LAIN
                            else {
                                $btn_class = "btn-unavailable-seat";
                                $disabled = "disabled";
                                
                                if (!empty($loker['tanggal_selesai'])) {
                                    $waktu_selesai = strtotime($loker['tanggal_selesai']);
                                    $waktu_sekarang = time();
                                    $selisih_detik = $waktu_selesai - $waktu_sekarang;

                                    // 🟢 LOGIKA CERDAS: Jika waktu sewa di DB sudah lewat/habis
                                    if ($selisih_detik <= 0) {
                                        $tooltip_teks = "🔒 Terkunci (Waktu Penggunaan Habis)";
                                    } else {
                                        // Jika waktu di DB memang masih aktif berjalan di masa depan
                                        $sisa_jam = floor($selisih_detik / 3600);
                                        $sisa_menit = floor(($selisih_detik % 3600) / 60);
                                        $tooltip_teks = "Terpakai (Sisa waktu: " . $sisa_jam . " Jam " . $sisa_menit . " Menit)";
                                    }
                                } else {
                                    // Antisipasi cadangan jika kolom tanggal_selesai bernilai NULL
                                    $tooltip_teks = "Terpakai (Sisa waktu: Tidak Diketahui)";
                                }
                            }
                        ?>
                            <button type="button" 
                                    data-db-id="<?= $loker['id'] ?>" 
                                    data-kode="<?= htmlspecialchars($loker['kode_loker']) ?>" 
                                    data-price="0" 
                                    title="<?= $tooltip_teks ?>" 
                                    class="locker-btn <?= $btn_class ?>" <?= $disabled ?>>
                                <?= htmlspecialchars($loker['kode_loker']) ?>
                            </button>

                            <?php 
                            // Memberi sekat jalan tengah grid (setelah kolom ke-2)
                            if ($counter % 4 == 2) {
                                echo '<div class="grid-gap"></div>';
                            }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                </div>

                <div class="entrance-label">
                    Pintu Masuk Area Loker
                </div>
            </div>

            <div class="sidebar-right">
                <h3 class="section-title">Konfigurasi Sewa</h3>

                <div id="duration-section" class="duration-section hidden">
                    <div class="duration-flex">
                        <div>
                            <p class="duration-label">Durasi Sewa</p>
                            <p class="selected-title" id="selected-locker-title">Loker -</p>
                        </div>
                        <div class="counter-box">
                            <button type="button" id="btn-minus" class="counter-btn">-</button>
                            <span id="hours-display" class="hours-display">1 </span>
                            <p style="padding-right: 10px">Jam</p>
                            <button type="button" id="btn-plus" class="counter-btn">+</button>
                        </div>
                    </div>
                    <p class="price-note" style="color: #ef4444; font-weight: 600;">*Maksimal batas sewa 3 jam</p>
                    <div class="divider"></div>
                </div>

                <form action="api/proses-sewa-langsung.php" method="POST">
                    <input type="hidden" name="locker_id" id="form-locker-id" value="">
                    <input type="hidden" name="durasi_jam" id="form-durasi" value="1">
                    
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?? 10; ?>">

                    <div class="sidebar-action">
                        <div class="total-block">
                            <p class="total-label">STATUS SEWA</p>
                            <p id="status-display" class="total-price-empty" style="color: #10b981; font-size: 1.2rem;">GRATIS</p>
                        </div>
                        
                        <button type="submit" id="btn-continue" class="btn-continue-disabled" disabled>
                            SEWA SEKARANG
                        </button>
                    </div>
                </form>
            </div>

        </div> 
    </div> 
    <script src="js/locker-selection.js"></script>
</body>
</html>