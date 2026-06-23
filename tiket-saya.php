<?php
// tiket-saya.php
require 'config/auth.php';
require_once 'config/connection.php';

// 🟢 PERBAIKAN UTAMA: Samakan zona waktu PHP di file ini dengan api/cron-pengingat.php
date_default_timezone_set('Asia/Makassar'); 

$user_id = $_SESSION['user']['id'] ?? 10;

// 🔵 PROSES ACTION: Ketika user klik tombol "Selesai"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'selesai_sewa') {
    $pemesanan_id = intval($_POST['pemesanan_id'] ?? 0);
    
    if ($pemesanan_id > 0) {
        // Mulai transaksi database agar kedua update aman berjalan bersamaan
        mysqli_begin_transaction($conn);

        try {
            // 1. Update status pemesanan menjadi selesai
            $query_update_p = "UPDATE pemesanan SET status = 'selesai' WHERE id = ? AND user_id = ?";
            $stmt_p = mysqli_prepare($conn, $query_update_p);
            mysqli_stmt_bind_param($stmt_p, "ii", $pemesanan_id, $user_id);
            mysqli_stmt_execute($stmt_p);

            // 2. (Opsional) Jika sistem Anda mengubah status locker kembali menjadi tersedia/kosong,
            // kita cari tahu locker_id nya terlebih dahulu:
            $query_get_locker = "SELECT locker_id FROM pemesanan WHERE id = ?";
            $stmt_g = mysqli_prepare($conn, $query_get_locker);
            mysqli_stmt_bind_param($stmt_g, "i", $pemesanan_id);
            mysqli_stmt_execute($stmt_g);
            $res_locker = mysqli_stmt_get_result($stmt_g);
            $data_loker = mysqli_fetch_assoc($res_locker);

            if ($data_loker) {
                $locker_id = $data_loker['locker_id'];
                // Ganti 'tersedia' jika kolom status loker di database Anda menggunakan istilah berbeda
                $query_update_l = "UPDATE lockers SET status = 'tersedia' WHERE id = ?";
                $stmt_l = mysqli_prepare($conn, $query_update_l);
                mysqli_stmt_bind_param($stmt_l, "i", $locker_id);
                mysqli_stmt_execute($stmt_l);
            }

            // Jika semua query sukses, commit perubahan
            mysqli_commit($conn);
            
            // Refresh halaman agar tiket yang sudah selesai hilang dari daftar aktif
            header("Location: tiket-saya.php?msg=sukses_selesai");
            exit;

        } catch (Exception $e) {
            // Jika ada yang gagal, batalkan semua perubahan database
            mysqli_rollback($conn);
        }
    }
}

// 1. Ambil semua pemesanan aktif milik user ini
$daftar_tiket = [];
$query = "SELECT p.id AS pemesanan_id, p.tanggal_selesai, p.kode_akses, l.kode_loker, l.lokasi 
          FROM pemesanan p 
          JOIN lockers l ON p.locker_id = l.id 
          WHERE p.user_id = ? AND p.status = 'aktif' 
          ORDER BY p.id DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Karena timezone di atas sudah diset Asia/Makassar, strtotime sekarang menghasilkan timestamp WITA yang presisi
        $row['target_timestamp'] = strtotime($row['tanggal_selesai']) * 1000; 
        $daftar_tiket[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Saya - KuLocker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background-color: #f3f4f6; color: #1f2937; padding: 20px; min-height: 100vh; }
        
        .container { max-width: 480px; margin: 0 auto; padding-bottom: 40px; }
        
        /* Header TIX ID */
        .app-header { display: flex; align-items: center; margin-bottom: 20px; background: #ffffff; padding: 16px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .btn-back { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #0f172a; margin-right: 16px; display: flex; align-items: center; }
        .header-title { font-size: 1.3rem; font-weight: 700; color: #0f172a; }

        /* CARD PERSEGI PANJANG (GAYA TIX ID) */
        .ticket-list-card { background: #ffffff; border-radius: 14px; border: 1px solid #e5e7eb; padding: 16px; margin-bottom: 14px; display: flex; flex-direction: column; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }
        
        .ticket-main-info { display: flex; justify-content: space-between; align-items: center; }
        .ticket-info { display: flex; flex-direction: column; gap: 4px; }
        .locker-badge { background: #e0f2fe; color: #0369a1; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 0.85rem; display: inline-block; width: max-content; margin-bottom: 4px; }
        .locker-loc { font-size: 0.95rem; font-weight: 600; color: #1e293b; }
        .ticket-date { font-size: 0.8rem; color: #64748b; }

        /* Pembungkus tombol aksi di sisi kanan */
        .ticket-actions { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; }

        /* Tombol Tampilkan QR */
        .btn-view-qr { background: #0f172a; color: #ffffff; border: none; padding: 8px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 6px; width: 100%; justify-content: center; }
        .btn-view-qr:hover { background: #1e293b; }

        /* Tombol Selesai Menggunakan */
        .btn-finish-rental { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: 8px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; width: 100%; justify-content: center; }
        .btn-finish-rental:hover { background: #ffe4e6; color: #be123c; border-color: #fda4af; }

        /* Kotak Countdown Timer Real-Time */
        .timer-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; }
        .timer-label { font-size: 0.8rem; color: #475569; font-weight: 600; }
        .countdown-display { font-size: 1.15rem; font-weight: 700; color: #0f172a; font-family: 'Courier New', Courier, monospace; }

        /* MODAL POP-UP TERSEMBUNYI UNTUK QR CODE */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 999; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        
        .modal-box { background: #ffffff; padding: 30px; border-radius: 20px; width: 90%; max-width: 360px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); transform: scale(0.8); transition: transform 0.3s ease; }
        .modal-overlay.active .modal-box { transform: scale(1); }

        .modal-qr-wrapper { background: #ffffff; border: 2px solid #f1f5f9; border-radius: 12px; padding: 12px; display: inline-block; margin: 16px 0; }
        .modal-qr-img { width: 180px; height: 180px; display: block; }
        
        .modal-pin { font-size: 1.8rem; font-weight: 800; letter-spacing: 4px; color: #0f172a; margin-bottom: 4px; }
        .btn-close-modal { width: 100%; background: #ef4444; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; margin-top: 15px; cursor: pointer; }

        .empty-state { text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 16px; border: 1px solid #e5e7eb; }
        .empty-icon { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 16px; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="app-header">
            <button class="btn-back" onclick="window.location.href='dashboard-utama.php'"><i class="ti ti-arrow-left"></i></button>
            <h1 class="header-title">Tiket Aktif Saya</h1>
        </div>

        <?php if (empty($daftar_tiket)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="ti ti-ticket-off"></i></div>
                <h3 style="color: #475569; margin-bottom: 6px;">Belum Ada Tiket Aktif</h3>
                <p style="font-size: 0.85rem; color: #94a3b8;">Kamu tidak sedang menyewa loker saat ini.</p>
            </div>
        <?php else: ?>
            
            <?php foreach ($daftar_tiket as $tiket): ?>
                <div class="ticket-list-card">
                    <div class="ticket-main-info">
                        <div class="ticket-info">
                            <span class="locker-badge">Loker <?= htmlspecialchars($tiket['kode_loker']) ?></span>
                            <span class="locker-loc"><i class="ti ti-map-pin"></i> <?= htmlspecialchars($tiket['lokasi']) ?></span>
                            <span class="ticket-date">ID Peminjaman: #<?= $tiket['pemesanan_id'] ?></span>
                        </div>
                        
                        <div class="ticket-actions">
                            <button class="btn-view-qr" 
                                    onclick="openQrModal('<?= htmlspecialchars($tiket['kode_akses']) ?>', '<?= htmlspecialchars($tiket['kode_loker']) ?>')">
                                <i class="ti ti-qrcode"></i> QR Code
                            </button>
                            
                            <form action="" method="POST" onsubmit="return kofirmasiSelesai('<?= htmlspecialchars($tiket['kode_loker']) ?>')">
                                <input type="hidden" name="action" value="selesai_sewa">
                                <input type="hidden" name="pemesanan_id" value="<?= $tiket['pemesanan_id'] ?>">
                                <button type="submit" class="btn-finish-rental">
                                    <i class="ti ti-square-check"></i> Selesai
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="timer-box">
                        <span class="timer-label"><i class="ti ti-clock-hour-4"></i> Sisa Waktu Sewa:</span>
                        <div class="countdown-display" data-target-timestamp="<?= $tiket['target_timestamp'] ?>">00:00:00</div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <div class="modal-overlay" id="qrModal">
        <div class="modal-box">
            <h3 id="modalLockerTitle" style="color: #0f172a;">QR Code Loker</h3>
            <p style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">Arahkan kode ini ke kamera scanner loker</p>
            
            <div class="modal-qr-wrapper">
                <img id="modalQrImage" src="" alt="QR Code" class="modal-qr-img">
            </div>
            
            <div id="modalPinDisplay" class="modal-pin">000000</div>
            <p style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">PIN Akses Cadangan</p>
            
            <button class="btn-close-modal" onclick="closeQrModal()">Tutup</button>
        </div>
    </div>

    <script>
        const modal = document.getElementById('qrModal');
        const modalTitle = document.getElementById('modalLockerTitle');
        const modalQrImage = document.getElementById('modalQrImage');
        const modalPinDisplay = document.getElementById('modalPinDisplay');

        function openQrModal(pin, kodeLoker) {
            modalTitle.innerText = `Loker ${kodeLoker}`;
            modalPinDisplay.innerText = pin;
            
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(pin)}`;
            modalQrImage.src = qrUrl;
            
            modal.classList.add('active');
        }

        function closeQrModal() {
            modal.classList.remove('active');
            modalQrImage.src = "";
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeQrModal();
            }
        });

        // Dialog konfirmasi sebelum mengakhiri sewa loker
        function kofirmasiSelesai(kodeLoker) {
            return confirm(`Apakah Anda yakin telah mengosongkan dan selesai menggunakan Loker ${kodeLoker}? Pilihan ini tidak dapat dibatalkan.`);
        }

        // JAVASCRIPT LOGIKA COUNTDOWN TIMER MULTI-TIKET REAL-TIME
        document.addEventListener("DOMContentLoaded", function () {
            const timers = document.querySelectorAll(".countdown-display");

            timers.forEach(function (timerElement) {
                const countDownDate = parseInt(timerElement.getAttribute("data-target-timestamp"), 10);

                const timerInterval = setInterval(function () {
                    const now = new Date().getTime();
                    const distance = countDownDate - now;

                    // Kalkulasi Waktu Sisa
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Formatting 2 digit (00:00:00)
                    const fHours = hours < 10 ? "0" + hours : hours;
                    const fMinutes = minutes < 10 ? "0" + minutes : minutes;
                    const fSeconds = seconds < 10 ? "0" + seconds : seconds;

                    // Mengatasi kondisi jika hitungan mundur menghasilkan angka negatif sebelum halaman dimuat ulang
                    if (distance < 0) {
                        clearInterval(timerInterval);
                        timerElement.innerHTML = "WAKTU HABIS";
                        timerElement.style.color = "#ef4444";

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        timerElement.innerHTML = fHours + ":" + fMinutes + ":" + fSeconds;
                    }
                }, 1000);
            });
        });
    </script>
</body>
</html>