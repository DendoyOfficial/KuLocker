<?php
require 'config/auth.php';
require_once 'config/connection.php';

// 1. Ambil ID dari session 'user' (Disamakan dengan logika di profile.php)
$id_users = is_array($_SESSION['user']) 
    ? ($_SESSION['user']['id'] ?? $_SESSION['user']['id_users'] ?? null) 
    : $_SESSION['user'];

// Proteksi keamanan: Kalau belum login, tendang ke sign-in.php
if (!$id_users) {
    header("Location: sign-in.php");
    exit;
}

// 2. Ambil data user saat ini untuk auto-fill nama dan email di form
$stmtUser = mysqli_prepare($conn, "SELECT nama, email FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmtUser, "i", $id_users);
mysqli_stmt_execute($stmtUser);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUser)) ?: ['nama' => '', 'email' => ''];

// 3. Ambil daftar riwayat pemesanan milik user untuk dihubungkan ke keluhan (Opsional di form)
$query_sewa = "SELECT p.id AS pemesanan_id, l.id AS locker_id, l.kode_loker, l.lokasi, p.status 
               FROM pemesanan p 
               JOIN lockers l ON p.locker_id = l.id 
               WHERE p.user_id = ? 
               ORDER BY p.created_at DESC";
$stmtSewa = mysqli_prepare($conn, $query_sewa);
mysqli_stmt_bind_param($stmtSewa, "i", $id_users);
mysqli_stmt_execute($stmtSewa);
$result_sewa = mysqli_stmt_get_result($stmtSewa);

$daftar_sewa = [];
while ($row = mysqli_fetch_assoc($result_sewa)) {
    $daftar_sewa[] = $row;
}

// ==========================================
// BAGIAN PHP: MEMPROSES INSERT DATA KELUHAN
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fullName = htmlspecialchars($_POST['fullName']);
    $email    = htmlspecialchars($_POST['email']);
    $category = htmlspecialchars($_POST['category']);
    $details  = htmlspecialchars($_POST['details']);
    
    // Menangkap pemesanan_id jika user memilih loker tertentu, pecah datanya
    $sewa_terpilih = $_POST['pemesanan_loker'] ?? '';
    $locker_id = null;
    $pemesanan_id = null;

    if (!empty($sewa_terpilih)) {
        list($p_id, $l_id) = explode('_', $sewa_terpilih);
        $pemesanan_id = (int)$p_id;
        $locker_id = (int)$l_id;
    }

    // Menerjemahkan nilai kategori untuk dijadikan JUDUL keluhan di database
    $judul_keluhan = "";
    switch($category) {
        case 'product': $judul_keluhan = "Loker rusak"; break;
        case 'delivery': $judul_keluhan = "Loker tidak bisa dibuka"; break;
        case 'service': $judul_keluhan = "Pelayanan Tidak Memuaskan"; break;
        case 'website': $judul_keluhan = "Kendala Website / Aplikasi"; break;
        case 'other': $judul_keluhan = "Lainnya"; break;
        default: $judul_keluhan = "Keluhan Umum"; break;
    }

    // Insert ke tabel `keluhan` menggunakan Prepared Statement
    $sql_insert = "INSERT INTO keluhan (user_id, locker_id, pemesanan_id, judul, deskripsi, status) VALUES (?, ?, ?, ?, ?, 'open')";
    $stmtInsert = mysqli_prepare($conn, $sql_insert);
    
    // Bind parameter (i = integer, s = string). locker_id & pemesanan_id bisa bernilai null
    mysqli_stmt_bind_param($stmtInsert, "iiiss", $id_users, $locker_id, $pemesanan_id, $judul_keluhan, $details);
    $eksekusi = mysqli_stmt_execute($stmtInsert);

    if ($eksekusi) {
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Laporan Terkirim</title>
            <link rel="stylesheet" href="css/keluhan.css" />
        </head>
        <body style="background-color: #fafafa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: 'DM Sans', sans-serif;">
            <div style="width: 100%; max-width: 600px; margin: 20px; text-align: center; background: #fff; padding: 50px 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 6px solid #c9a84c;">
                <div style="width: 80px; height: 80px; background: #c9a84c; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                    <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h1 style="color: #111; margin-bottom: 10px;">Berhasil Terkirim!</h1>
                <h3 style="color: #333; margin-bottom: 5px; font-weight: 500;">Terma kasih, <?= htmlspecialchars($fullName) ?></h3>
                <p style="color: #666; line-height: 1.5;">Laporan Anda dengan kategori <strong><?= htmlspecialchars($judul_keluhan) ?></strong> telah masuk ke database sistem dan akan segera diproses oleh admin.</p>
                <p style="font-size: 13px; color: #888; margin-bottom: 5px;">Tembusan tindak lanjut akan dikirim ke email: <b><?= htmlspecialchars($email) ?></b></p>
                <p style="font-size: 13px; color: #888; margin-bottom: 30px; font-style: italic;">"<?= htmlspecialchars($details) ?>"</p>

                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="dashboard-utama.php" style="display: inline-block; padding: 14px 30px; background: #e0e0e0; color: #111; text-decoration: none; border-radius: 30px; font-weight: bold; transition: 0.3s; border: 2px solid #d0d0d0;">Kembali ke dashboard</a>
                    <a href="keluhan.php" style="display: inline-block; padding: 14px 30px; background: #c9a84c; color: #fff; text-decoration: none; border-radius: 30px; font-weight: bold; transition: 0.3s;">Kirim Laporan Baru</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        echo "<script>alert('Gagal mengirim keluhan. Silakan coba lagi.'); window.location.href='keluhan.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Pengaduan Pelanggan</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/keluhan.css" />
</head>
<body>

    <a href="dashboard-utama.php" class="back-btn" title="Kembali">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
    </a>

    <div class="background-container">
        <div class="main-container">
            
            <div class="card-left">
                <div>
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    <h1>Sampaikan<br>Suara Anda</h1>
                    <p>Kami sangat menghargai setiap masukan dan keluhan Anda. Beritahu kami agar kami bisa memberikan layanan yang lebih baik ke depannya.</p>
                </div>
                <div class="overlay-text">
                    Layanan Pelanggan 24/7
                </div>
            </div>

            <div class="card-right">
                <h2 class="form-title">Form Keluhan</h2>
                
                <form action="" method="POST">
                    <div class="flex flex-col sm:flex-row gap-0 sm:gap-4">
                        <div class="input-group w-full">
                            <label for="fullName">Nama Lengkap</label>
                            <input type="text" id="fullName" name="fullName" required value="<?= htmlspecialchars($user_data['nama']) ?>" placeholder="Budi Santoso">
                        </div>
                        <div class="input-group w-full">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user_data['email']) ?>" placeholder="budi@email.com">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="category">Kategori Keluhan</label>
                        <div class="select-wrapper">
                            <select id="category" name="category" required>
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <option value="product">Loker rusak</option>
                                <option value="delivery">Loker tidak bisa dibuka</option>
                                <option value="service">Pelayanan Tidak Memuaskan</option>
                                <option value="website">Kendala Website / Aplikasi</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="pemesanan_loker">Loker yang Bermasalah (Opsional)</label>
                        <div class="select-wrapper">
                            <select id="pemesanan_loker" name="pemesanan_loker">
                                <option value="">Tidak Terkait Transaksi Loker / Umum</option>
                                <?php foreach ($daftar_sewa as $sewa): ?>
                                    <option value="<?= $sewa['pemesanan_id'] . '_' . $sewa['locker_id'] ?>">
                                        Loker <?= htmlspecialchars($sewa['kode_loker']) ?> - <?= htmlspecialchars($sewa['lokasi']) ?> (Status Sewa: <?= ucfirst($sewa['status']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="details">Detail Keluhan</label>
                        <textarea id="details" name="details" required placeholder="Jelaskan secara detail kendala yang Anda alami beserta nomor loker atau kendala sistem..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary">
                        Kirim Keluhan
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>