<?php
// api/cron-pengingat.php

require_once '../config/connection.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 

// Set zona waktu ke WITA (Mataram)
date_default_timezone_set('Asia/Makassar');

// 🟢 PERBAIKAN 1: Paksa MySQL menyamakan zona waktu ke WITA agar strtotime() akurat
mysqli_query($conn, "SET time_zone = '+08:00'");

// 1. QUERY UTAMA: Tarik semua pemesanan yang berstatus 'aktif'
$query = "SELECT p.id AS pemesanan_id, p.tanggal_selesai, p.notifikasi_step, 
                  l.id AS locker_id, l.kode_loker, l.lokasi, u.nama, u.email 
          FROM pemesanan p
          JOIN lockers l ON p.locker_id = l.id
          JOIN users u ON p.user_id = u.id
          WHERE p.status = 'aktif'";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $pemesanan_id = $row['pemesanan_id'];
        $locker_id = $row['locker_id'];
        $email_user = $row['email'];
        $nama_user = $row['nama'];
        $kode_loker = $row['kode_loker'];
        $lokasi_loker = $row['lokasi'];
        $step_sekarang = $row['notifikasi_step'];

        // Hitung selisih waktu (Sisa waktu dalam satuan MENIT menggunakan pembulatan ke atas)
        $waktu_sekarang = time();
        $waktu_selesai = strtotime($row['tanggal_selesai']);
        $sisa_menit = ceil(($waktu_selesai - $waktu_sekarang) / 60);

        // Variabel penentu eksekusi
        $kirim_email = false;
        $subject = "";
        $body_content = "";
        $next_step = "";
        $update_status_order = false; 

        // 🟢 PERBAIKAN 2: Melebarkan rentang filter '<=' agar toleran terhadap jeda eksekusi menit cron
        
        // Kasus 4: Waktu Habis -> Loker Terkunci Paksa!
        if ($sisa_menit <= 0 && $step_sekarang !== 'terkunci') {
            $kirim_email = true;
            $next_step = 'terkunci';
            $update_status_order = true; 
            
            $subject = "⚠️ PENTING: Waktu Habis, Loker {$kode_loker} Telah Terkunci!";
            $body_content = "
                <h2 style='color: #ef4444;'>Waktu Penggunaan Habis!</h2>
                <p>Halo <b>{$nama_user}</b>, masa sewa kamu untuk loker <b>{$kode_loker}</b> di {$lokasi_loker} telah berakhir.</p>
                <div style='background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin: 15px 0;'>
                    <p style='margin: 0; font-weight: bold; color: #991b1b;'>Status: TERKUNCI KARENA TELAT</p>
                    <p style='margin: 5px 0 0 0; font-size: 0.9rem;'>Sistem telah mengunci akses loker kamu secara otomatis. Silakan hubungi <b>Bagian Admin Utama atau Satpam Penjaga Gedung</b> untuk melakukan pembukaan manual dan pengambilan barang kamu.</p>
                </div>
            ";
        } 
        // Kasus 3: Sisa 1 Menit (Aman menangkap durasi antara 0.1 sampai 1.9 menit)
        elseif ($sisa_menit <= 1 && $sisa_menit > 0 && $step_sekarang === '5_menit') {
            $kirim_email = true;
            $next_step = '1_menit';
            $subject = "🚨 KRITIS: 1 Menit Lagi Loker {$kode_loker} Terkunci!";
            $body_content = "
                <h2 style='color: #dc2626;'>Peringatan Terakhir (1 Menit Lagi)!</h2>
                <p>Halo <b>{$nama_user}</b>, waktu kamu tersisa kurang dari <b>1 menit</b> lagi. Tolong segera kosongkan loker <b>{$kode_loker}</b> sekarang juga sebelum sistem mengunci barang-barang kamu secara permanen!</p>
            ";
        } 
        // Kasus 2: Sisa 5 Menit (Aman menangkap durasi antara 2 sampai 5.9 menit)
        elseif ($sisa_menit <= 5 && $sisa_menit > 1 && $step_sekarang === '15_menit') {
            $kirim_email = true;
            $next_step = '5_menit';
            $subject = "⚠️ Peringatan: 5 Menit Lagi Loker {$kode_loker} Terkunci";
            $body_content = "
                <h2 style='color: #f59e0b;'>Waktu Tersisa 5 Menit!</h2>
                <p>Halo <b>{$nama_user}</b>, diingatkan kembali bahwa sisa waktu penggunaan loker <b>{$kode_loker}</b> tersisa <b>5 menit</b> lagi. Mohon segera menuju lokasi loker untuk mengosongkan tempat.</p>
            ";
        } 
        // Kasus 1: Sisa 15 Menit (Aman menangkap durasi antara 6 sampai 15.9 menit)
        elseif ($sisa_menit <= 15 && $sisa_menit > 5 && $step_sekarang === 'belum') {
            $kirim_email = true;
            $next_step = '15_menit';
            $subject = "Pemberitahuan: 15 Menit Sebelum Waktu Sewa Loker {$kode_loker} Habis";
            $body_content = "
                <h2 style='color: #eab308;'>Pengingat Waktu (15 Menit)</h2>
                <p>Halo <b>{$nama_user}</b>, kami ingin menginformasikan bahwa durasi penggunaan loker <b>{$kode_loker}</b> di {$lokasi_loker} tersisa <b>15 menit</b> lagi.</p>
                <p>Harap bersiap-siap untuk mengosongkan loker tepat waktu karena sistem kami tidak mendukung perpanjangan otomatis.</p>
            ";
        }

        // 3. PROSES EKSEKUSI KIRIM EMAIL VIA PHPMAILER
        if ($kirim_email) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'websitekulocker@gmail.com';     
                $mail->Password   = 'btco iwhs uxft zhlg';     
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('kulocker.unram@gmail.com', 'KuLocker Universitas Mataram');
                $mail->addAddress($email_user, $nama_user);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px;'>
                        {$body_content}
                        <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                        <p style='font-size: 0.85rem; color: #94a3b8;'>© 2026 KuLocker Unram. Sistem Otomatis Pemberitahuan Kampus.</p>
                    </div>
                ";

                $mail->send();

                // 4. UPDATE DATABASE AGAR TIDAK SPAM BERULANG
                if ($update_status_order) {
                    mysqli_query($conn, "UPDATE pemesanan SET status = 'selesai', notifikasi_step = '$next_step' WHERE id = $pemesanan_id");
                    mysqli_query($conn, "UPDATE lockers SET status = 'rusak' WHERE id = $locker_id");
                    
                    echo "Loker {$kode_loker} SUDAH HABIS WAKTUNYA. Status diubah menjadi RUSAK (Terkunci Sistem) & Email Kunci Terkirim.<br>";
                } else {
                    mysqli_query($conn, "UPDATE pemesanan SET notifikasi_step = '$next_step' WHERE id = $pemesanan_id");
                    echo "Email pengingat Tahap [{$next_step}] sukses dikirim ke {$email_user}. Sisa: {$sisa_menit} Menit.<br>";
                }

            } catch (Exception $e) {
                echo "Gagal memproses email ke {$email_user}. Error: {$mail->ErrorInfo}<br>";
            }
        } else {
            // Tampilan log untuk memudahkan pelacakan sisa waktu riil mahasiswa saat ditest di browser
            echo "User {$nama_user} (Loker {$kode_loker}) dilewati. Sisa: {$sisa_menit} Menit. Status step saat ini: [{$step_sekarang}].<br>";
        }
    }
} else {
    echo "Tidak ada loker aktif yang perlu diproses saat ini.";
}
?>