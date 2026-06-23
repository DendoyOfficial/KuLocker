<?php
require_once "config/auth.php";
require_once "config/connection.php";

// only admin
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: sign-in.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_loker'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $ukuran = trim($_POST['ukuran'] ?? '');

    if ($kode !== '' && $lokasi !== '' && in_array($ukuran, ['S','M','L'], true)) {
        $kode_s = mysqli_real_escape_string($conn, $kode);
        $lokasi_s = mysqli_real_escape_string($conn, $lokasi);
        $ukuran_s = mysqli_real_escape_string($conn, $ukuran);

        $cek_aktif = mysqli_query($conn, "SELECT id FROM lockers WHERE kode_loker = '$kode_s' AND (is_deleted = 0 OR is_deleted IS NULL)");
        if (mysqli_num_rows($cek_aktif) === 0) {
            $cek_hapus = mysqli_query($conn, "SELECT id FROM lockers WHERE kode_loker = '$kode_s' AND is_deleted = 1 ORDER BY id DESC LIMIT 1");
            if (mysqli_num_rows($cek_hapus) > 0) {
                $hapus_row = mysqli_fetch_assoc($cek_hapus);
                $restore_id = intval($hapus_row['id']);
                mysqli_query($conn, "UPDATE lockers SET lokasi = '$lokasi_s', ukuran = '$ukuran_s', status = 'Tersedia', is_deleted = 0 WHERE id = $restore_id");
            } else {
                mysqli_query($conn, "INSERT INTO lockers (kode_loker, lokasi, ukuran, status, is_deleted, created_at) VALUES ('$kode_s', '$lokasi_s', '$ukuran_s', 'Tersedia', 0, NOW())");
            }
            header('Location: admin.php?page=locker&msg=tambah_berhasil');
            exit;
        } else {
            $msg = 'exists';
        }
    } else {
        $msg = 'invalid';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tambah Unit Locker - KuLocker</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div style="max-width:900px;margin:40px auto;padding:20px;">
        <a href="admin.php?page=locker" style="text-decoration:none;color:#0f766e;font-weight:700;">← Kembali ke Daftar Locker</a>
        <h2 style="margin-top:18px;margin-bottom:8px;">Tambah Unit Locker</h2>

        <?php if ($msg === 'exists'): ?>
            <div style="background:#fff7ed;color:#92400e;padding:12px;border-radius:8px;margin-bottom:12px;">Kode locker sudah ada.</div>
        <?php elseif ($msg === 'invalid'): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:12px;">Mohon isi semua kolom dengan benar.</div>
        <?php endif; ?>

        <form method="POST" action="tambah-locker.php" style="background:#ffffff;padding:18px;border-radius:10px;border:1px solid #e6e6e6;">
            <div style="display:grid;gap:12px;">
                <label style="font-weight:600;">Kode Locker</label>
                <input type="text" name="kode_loker" placeholder="Contoh: LKR-001" required style="padding:10px;border-radius:8px;border:1px solid #d1d5db;">

                <label style="font-weight:600;">Lokasi Cluster</label>
                <input type="text" name="lokasi" placeholder="Contoh: Cluster A" required style="padding:10px;border-radius:8px;border:1px solid #d1d5db;">

                <label style="font-weight:600;">Ukuran</label>
                <select name="ukuran" required style="padding:10px;border-radius:8px;border:1px solid #d1d5db;">
                    <option value="">Pilih ukuran</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                </select>

                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:6px;">
                    <a href="admin.php?page=locker" style="background:#e5e7eb;padding:10px 16px;border-radius:8px;color:#111827;text-decoration:none;font-weight:600;">Batal</a>
                    <button type="submit" style="background:#0f766e;color:white;padding:10px 16px;border-radius:8px;border:none;font-weight:700;">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>