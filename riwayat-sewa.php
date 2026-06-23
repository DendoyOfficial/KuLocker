<?php
// 1. Proteksi Session & Koneksi Database
require 'config/auth.php'; 
require_once 'config/connection.php';

$user = $_SESSION['user'];
$user_id = $user['id'];

// 2. Query mengambil seluruh riwayat transaksi sewa yang SUDAH TIDAK AKTIF
// 🟢 Pastikan query di file riwayat-sewa.php kamu pas seperti ini:
$query_riwayat = "SELECT p.*, l.kode_loker, l.lokasi, l.ukuran 
                  FROM pemesanan p
                  JOIN lockers l ON p.locker_id = l.id
                  WHERE p.user_id = '$user_id' 
                    AND p.status IN ('selesai', 'dibatalkan')
                  ORDER BY p.id DESC";

$result = mysqli_query($conn, $query_riwayat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sewa Loker - KuLocker</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <link rel="stylesheet" href="css/riwayat.css">
</head>
<body>

<div class="container">
    
    <div class="header-section">
        <a href="dashboard-utama.php" class="btn-back">
            <i class="ti ti-arrow-left"></i>
        </a>
        <h1 class="page-title">Riwayat Penggunaan</h1>
    </div>

    <div class="history-grid">
        <?php 
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $status_sewa = strtolower($row['status']);
                $tgl_pinjam = date('d M Y', strtotime($row['tanggal_mulai']));
        ?>
                <div class="history-card">
                    <div class="card-left">
                        <div class="locker-icon-box">
                            <i class="ti ti-lock-square-rounded"></i>
                        </div>
                        <div class="locker-info-meta">
                            <div class="locker-code"><?= htmlspecialchars($row['kode_loker']); ?> <span style="font-weight: normal; font-size: 12px; color:#94a3b8;">(<?= htmlspecialchars($row['ukuran']); ?>)</span></div>
                            <div class="locker-location">
                                <i class="ti ti-building" style="font-size: 14px;"></i> 
                                <?= htmlspecialchars($row['lokasi']); ?>
                            </div>
                            <div class="locker-date">
                                <i class="ti ti-calendar-event"></i> Digunakan pada: <?= $tgl_pinjam; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-right">
                        <span class="status-badge badge-<?= $status_sewa; ?>">
                            <?= $status_sewa; ?>
                        </span>
                    </div>
                </div>
        <?php 
            }
        } else { 
        ?>
            <div class="empty-history">
                <i class="ti ti-history-off"></i>
                <p>Kamu belum memiliki riwayat penyewaan loker.</p>
            </div>
        <?php 
        } 
        ?>
    </div>

</div>

<script src="js/riwayat.js"></script>
</body>
</html>