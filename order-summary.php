<?php
// 1. Inisialisasi Auth dan Koneksi Database
require 'config/auth.php';
require_once 'config/connection.php';

// Validasi jika akses langsung tanpa submit form dari locker selection
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['locker_id']) || empty($_POST['locker_id'])) {
    header("Location: dashboard-utama.php");
    exit;
}

// 2. Tangkap Data POST dari halaman pemesanan sebelumnya
$locker_id = intval($_POST['locker_id']);
$durasi_jam = isset($_POST['durasi_jam']) ? intval($_POST['durasi_jam']) : 1;

// 3. Ambil data spesifik loker dari tabel `lockers` berdasarkan ID
$query = "SELECT kode_loker, lokasi, ukuran FROM lockers WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $locker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$loker = mysqli_fetch_assoc($result);

// Jika data loker tidak ditemukan di database, kembalikan ke dashboard
if (!$loker) {
    header("Location: dashboard-utama.php");
    exit;
}

// 4. Perhitungan Nominal Transaksi (Kalkulasi Tarif)
$tarif_per_jam = 5000; 
$biaya_sewa = $tarif_per_jam * $durasi_jam;
$biaya_layanan = 2000; // Convenience fee / biaya platform aplikasi
$total_pembayaran = $biaya_sewa + $biaya_layanan;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary & Payment - KuLocker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <link rel="stylesheet" href="css/order-summary.css">
</head>
<body class="app-body">

    <div class="summary-container">
        
        <div class="app-header">
            <button class="btn-back" onclick="window.history.back();">
                <i class="ti ti-chevron-left"></i>
            </button>
            <h1 class="header-title">Order Summary</h1>
        </div>

        <form id="paymentForm" action="bayar-qris.php" method="POST">
            <input type="hidden" name="locker_id" value="<?= $locker_id ?>">
            <input type="hidden" name="durasi_jam" value="<?= $durasi_jam ?>">
            <input type="hidden" name="total_bayar" value="<?= $total_pembayaran ?>">

            <div class="summary-layout">
                
                <div class="content-left">
                    
                    <div class="main-card">
                        <div class="locker-badge">
                            <i class="ti ti-lock-square"></i>
                        </div>
                        <div class="locker-info">
                            <span class="brand-tag">KULOCKER INDONESIA</span>
                            <h2 class="locker-title">LOKER <?= htmlspecialchars($loker['kode_loker']) ?></h2>
                            <p class="locker-location"><i class="ti ti-map-pin"></i> <?= htmlspecialchars($loker['lokasi']) ?></p>
                            <div class="meta-row">
                                <span class="meta-pill"><i class="ti ti-arrows-maximize"></i> Ukuran <?= htmlspecialchars($loker['ukuran']) ?></span>
                                <span class="meta-pill"><i class="ti ti-hourglass-high"></i> <?= $durasi_jam ?> Jam Sewa</span>
                            </div>
                        </div>
                    </div>

                    <div class="payment-card">
                        <h4 class="card-subtitle">Pilih Metode Pembayaran</h4>
                        <div class="payment-grid">
    
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="qris" checked required onclick="updateAction('bayar-qris.php')">
                                <div class="payment-logo-wrapper">
                                    <img src="Qris application icon design on white background_.jpg" alt="QRIS">
                                </div>
                                <div class="payment-text">
                                    <span class="method-name">QRIS</span>
                                    <span class="method-desc">Gopay, OVO, ShopeePay, Dana, M-Banking</span>
                                </div>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="dana" required onclick="updateAction('bayar-dana.php')">
                                <div class="payment-logo-wrapper">
                                    <img src="Dana Logo (payment).jpg" alt="DANA">
                                </div>
                                <div class="payment-text">
                                    <span class="method-name">DANA Balance</span>
                                    <span class="method-desc">Bayar langsung via akun dompet digital DANA</span>
                                </div>
                            </label>

                        </div>
                    </div>

                    <div class="policy-card">
                        <h4 class="card-subtitle">Kebijakan & Ketentuan</h4>
                        <div class="policy-box">
                            <p class="policy-item"><i class="ti ti-info-circle"></i> Pembatalan sewa tidak dapat mengembalikan dana yang telah disetor.</p>
                            <p class="policy-item"><i class="ti ti-shield-check"></i> Kode PIN / Akses fisik QR loker otomatis aktif & dikirim via WhatsApp setelah status invoice Lunas.</p>
                            <p class="policy-item"><i class="ti ti-alert-triangle"></i> Pastikan mengosongkan loker sebelum durasi sewa berakhir untuk menghindari denda penalti.</p>
                        </div>
                    </div>

                </div>

                <div class="sidebar-right">
                    <h3 class="section-title">Detail Transaksi</h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">1 Tiket Loker (<?= htmlspecialchars($loker['kode_loker']) ?>)</span>
                        <span class="detail-value">Rp<?= number_format($biaya_sewa, 0, ',', '.') ?></span>
                    </div>
                    
                    <div class="detail-row mb-sub">
                        <span class="detail-sublabel">Rp<?= number_format($tarif_per_jam, 0, ',', '.') ?> × <?= $durasi_jam ?> jam</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Biaya Layanan Aplikasi</span>
                        <span class="detail-value">Rp<?= number_format($biaya_layanan, 0, ',', '.') ?></span>
                    </div>

                    <div class="divider"></div>

                    <div class="sidebar-action">
                        <div class="total-block">
                            <span class="total-label">TOTAL BAYAR</span>
                            <span class="actual-pay">Rp<?= number_format($total_pembayaran, 0, ',', '.') ?></span>
                        </div>
                        <button type="submit" class="btn-payment">
                            KONFIRMASI & BAYAR <i class="ti ti-arrow-right"></i>
                        </button>
                    </div>
                </div>

            </div> 
        </form>
    </div> 

    <script>
        function updateAction(targetPage) {
            // Mengubah atribut action pada form berdasarkan parameter targetPage
            document.getElementById('paymentForm').action = targetPage;
        }
    </script>
</body>
</html>