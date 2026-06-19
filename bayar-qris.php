<?php
// 1. Inisialisasi Auth dan Koneksi Database
require 'config/auth.php';
require_once 'config/connection.php';

// Validasi data transaksi, jika tidak ada lempar kembali ke dashboard
if (!isset($_POST['locker_id']) || !isset($_POST['total_bayar'])) {
    // Jika diakses langsung tanpa POST, coba cek dari session atau redirect
    header("Location: dashboard-utama.php");
    exit;
}

$locker_id = intval($_POST['locker_id']);
$durasi_jam = intval($_POST['durasi_jam']);
$total_bayar = intval($_POST['total_bayar']);

// Ambil data kode loker untuk keperluan struk teks
$query = "SELECT kode_loker FROM lockers WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $locker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$loker = mysqli_fetch_assoc($result);

// Generate nomor invoice dummy untuk estetika aplikasi
$no_invoice = "INV-" . time() . "-" . $locker_id;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS - KuLocker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .qris-container { width: 100%; max-width: 450px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 24px; border: 1px solid #e5e7eb; text-align: center; }
        .app-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; text-align: left; }
        .btn-back { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #1f2937; display: flex; align-items: center; }
        .header-title { font-size: 1.25rem; font-weight: 700; color: #1f2937; }
        .invoice-box { background-color: #f9fafb; border: 1px dashed #d1d5db; border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: left; }
        .inv-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #4b5563; }
        .inv-row:last-child { margin-bottom: 0; }
        .inv-bold { font-weight: 700; color: #111827; }
        .total-amount { font-size: 1.4rem; font-weight: 800; color: #059669; }
        .qris-logo { width: 140px; margin: 12px auto; display: block; }
        .qris-frame { background: #ffffff; border: 2px solid #e5e7eb; border-radius: 16px; padding: 16px; display: inline-block; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .qris-code { width: 220px; height: 220px; display: block; object-fit: contain; }
        .instruction-card { text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
        .ins-title { font-size: 0.95rem; font-weight: 700; color: #166534; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .ins-list { font-size: 0.85rem; color: #14532d; padding-left: 18px; line-height: 1.5; }
        
        /* Status real-time waiting */
        .waiting-box { margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 10px; color: #64748b; font-weight: 600; font-size: 0.95rem; border: 1px solid #e2e8f0; }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; color: #3b82f6; }
    </style>
</head>
<body>

    <div class="qris-container">
        
        <div class="app-header">
            <button class="btn-back" onclick="window.location.href='dashboard-utama.php'">
                <i class="ti ti-arrow-left"></i>
            </button>
            <h1 class="header-title">Pembayaran QRIS</h1>
        </div>

        <div class="invoice-box">
            <div class="inv-row">
                <span>No. Invoice</span>
                <span class="inv-bold"><?= $no_invoice ?></span>
            </div>
            <div class="inv-row">
                <span>Item Layanan</span>
                <span class="inv-bold">Sewa Loker <?= htmlspecialchars($loker['kode_loker']) ?> (<?= $durasi_jam ?> Jam)</span>
            </div>
            <div class="divider" style="border-top: 1px solid #e5e7eb; margin: 12px 0;"></div>
            <div class="inv-row" style="align-items: center;">
                <span>Total Tagihan</span>
                <span class="total-amount">Rp<?= number_format($total_payar = $total_bayar, 0, ',', '.') ?></span>
            </div>
        </div>

        <img src="img/Qris application icon design on white background_.jpg" alt="Logo QRIS" class="qris-logo" style="width: 50px;">       
        
        <div class="qris-frame">
            <img src="config/generate-qris.php?amount=<?= $total_bayar ?>" alt="QRIS Code KuLocker" class="qris-code">
        </div>

        <div class="instruction-card">
            <h4 class="ins-title"><i class="ti ti-info-circle"></i> Cara Membayar:</h4>
            <ol class="ins-list">
                <li>Simpan atau ambil tangkapan layar (screenshot) kode QRIS di atas.</li>
                <li>Buka aplikasi e-wallet pilihan Anda (Dana, OVO, GoPay, ShopeePay) atau M-Banking.</li>
                <li>Pilih opsi <strong>Scan / Bayar</strong> lalu unggah gambar QRIS dari galeri ponsel Anda.</li>
                <li>Periksa nominal tagihan dan konfirmasi pembayaran Anda.</li>
            </ol>
        </div>

        <div class="waiting-box">
            <i class="ti ti-loader-2 animate-spin"></i>
            <span>Menunggu pembayaran dideteksi...</span>
        </div>

    </div>

    <script>
        const invoiceNum = "<?= $no_invoice ?>";
        const lockerId = "<?= $locker_id ?>";
        const durasi = "<?= $durasi_jam ?>";

        // Fungsi mengecek status ke database via backend setiap 3 detik secara berkala
        const intervalCek = setInterval(function() {
            // Jalur disesuaikan mengarah ke folder config/
            fetch(`config/cek-status-pembayaran.php?invoice=${invoiceNum}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'lunas') {
                        // Stop hit ke server jika status sudah lunas
                        clearInterval(intervalCek);
                        
                        // Membuat form virtual untuk mengirimkan data POST ke proses-sukses.php
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'proses-sukses.php';

                        const inputs = {
                            no_invoice: invoiceNum,
                            locker_id: lockerId,
                            durasi_jam: durasi
                        };

                        for (const key in inputs) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = inputs[key];
                            form.appendChild(input);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    }
                })
                .catch(error => console.error('Gagal mengecek status pembayaran:', error));
        }, 3000); // 3 detik sekali
    </script>
</body>
</html>