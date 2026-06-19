<?php
session_start(); // Tetap dinyalakan untuk membaca siapa yang baru saja login

// 1. KONEKSI KE DATABASE KULOCKER
$koneksi = mysqli_connect("localhost", "root", "", "kulocker");

if (!$koneksi) {
    die("<p style='color:red; text-align:center;'>Koneksi gagal: " . mysqli_connect_error() . "</p>");
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if (isset($_SESSION['user']) && $_SESSION['role'] === 'admin') {
    $admin_nama = $_SESSION['nama'];
} else {
    $query_admin = mysqli_query($koneksi, "SELECT nama FROM users WHERE email = 'admin@kulocker.ac.id' AND role = 'admin' LIMIT 1");
    $data_admin  = mysqli_fetch_assoc($query_admin);
    $admin_nama  = $data_admin ? $data_admin['nama'] : "Admin KuLocker";
}

// Generate inisial huruf untuk avatar secara otomatis
$kata         = explode(" ", $admin_nama);
$admin_avatar = strtoupper(substr($kata[0], 0, 1)) . (isset($kata[1]) ? strtoupper(substr($kata[1], 0, 1)) : "");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - KuLocker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        /* ================= KUNCI LAYOUT ALA VS CODE ================= */
        html, body {
            margin: 0;
            padding: 0;
            height: 100vh; /* Memaksa tinggi halaman pas seukuran layar browser */
            overflow: hidden; /* Mematikan scroll bar global browser */
            display: flex; /* Membagi halaman menjadi kolom: Sidebar kiri & Konten kanan */
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            width: 260px; /* Sesuaikan dengan lebar sidebar asli di admin.css */
            height: 100vh;
            flex-shrink: 0; /* Mencegah sidebar menjadi gepeng/menyusut */
            overflow-y: auto; /* Jaga-jaga jika menu sidebar melebihi tinggi layar */
        }

        .main-container {
            flex: 1; /* Mengambil sisa seluruh ruang di sebelah kanan sidebar */
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Mengunci container utama */
        }

        .content-body {
            flex: 1;
            padding: 24px;
            box-sizing: border-box;
            
            /* KUNCI UTAMANYA DI SINI */
            overflow-y: auto; /* Mengaktifkan scroll hanya pada area isi konten kanan */
            background-color: #f8f9fa; /* Warna dasar abu-abu terang serasi */
        }
        /* ============================================================ */

        /* Gaya tombol status mini yang muncul menggantikan tombol Edit */
        .btn-status-mini {
            padding: 6px 10px;
            border-radius: 4px;
            border: none;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.1s ease;
        }
        .btn-status-mini:active {
            transform: scale(0.95);
        }
        .btn-mini-tersedia { background-color: #10b981; } /* Hijau */
        .btn-mini-booking { background-color: #f59e0b; }   /* Kuning/Oranye */
        .btn-mini-kosong { background-color: #ef4444; }    /* Merah */
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="img/Kulocker.jpeg" alt="Logo KuLocker">
        </div>
        
        <div class="sidebar-section-title">Main Feature</div>
        <ul class="sidebar-menu">
            <li class="<?= ($page == 'dashboard') ? 'active' : ''; ?>">
                <a href="admin.php?page=dashboard">Dashboard</a>
            </li>
            <li class="<?= ($page == 'locker') ? 'active' : ''; ?>">
                <a href="admin.php?page=locker">Device / Locker</a>
            </li>
            <li class="<?= ($page == 'user') ? 'active' : ''; ?>">
                <a href="admin.php?page=user">Data Pengguna</a>
            </li>
            <li class="<?= ($page == 'riwayat') ? 'active' : ''; ?>">
                <a href="admin.php?page=riwayat">Log Transaksi / Riwayat</a>
            </li>
            <li class="<?= ($page == 'pesan_keluar') ? 'active' : ''; ?>">
                <a href="admin.php?page=pesan_keluar">Pesan Keluar</a>
            </li>
            <li class="<?= ($page == 'inbox') ? 'active' : ''; ?>">
                <a href="admin.php?page=inbox">Inbox / Pesan Masuk</a>
            </li>
            <li><a href="#">Template</a></li>
        </ul>

        <div class="sidebar-section-title">Sistem</div>
        <ul class="sidebar-menu">
           <li><a href="dashboard.php" class="btn-logout">Logout</a></li>
        </ul>
    </div>

    <div class="main-container">
        <div class="topbar">
            <div class="topbar-info">
                <?php if ($page == 'locker'): ?>
                    Manajemen Cluster & Kondisi Fisik Unit Locker
                <?php elseif ($page == 'user'): ?>
                    Manajemen Hak Akses & Verifikasi Data Mahasiswa
                <?php elseif ($page == 'riwayat'): ?>
                    Log Aktivitas Penggunaan & Riwayat Penyewaan Locker
                <?php elseif ($page == 'pesan_keluar'): ?>
                    Riwayat Log Pesan & Notifikasi WA Gateway Keluar
                <?php elseif ($page == 'inbox'): ?>
                    Daftar Pesan Masuk / Webhook Respons dari Pengguna
                <?php else: ?>
                    Sistem Monitoring Locker Aktif & Terintegrasi
                <?php endif; ?>
            </div>
            <div class="topbar-user">
                <span class="user-name"><?= htmlspecialchars($admin_nama); ?></span>
                <div class="avatar"><?= htmlspecialchars($admin_avatar); ?></div>
            </div>
        </div>

        <div class="content-body">
            
            <?php if ($page == 'locker'): ?>
                <div class="card">
                    <div class="card-title">Visualisasi Denah Status Locker</div>
                    <div class="locker-grid-container" id="visualisasi-grid-loker">
                        <?php
                        $query_grid = mysqli_query($koneksi, "SELECT * FROM lockers ORDER BY kode_loker ASC");
                        while ($grid = mysqli_fetch_assoc($query_grid)) {
                            // PERBAIKAN: Berikan nilai default 'Tersedia' jika status kosong agar teks tag tidak hilang
                            $status_tampil_grid = (!empty(trim($grid['status'] ?? ''))) ? $grid['status'] : 'Tersedia';
                            $status_grid = strtolower($status_tampil_grid);
                            
                            $class_status = "status-empty"; 
                            if ($status_grid == 'terpakai' || $status_grid == 'dibooking' || $status_grid == 'booking') { $class_status = "status-filled"; }
                            if ($status_grid == 'rusak' || $status_grid == 'kosong') { $class_status = "status-warning"; }
                            
                            $parts = explode('-', $grid['kode_loker']);
                            $num = isset($parts[1]) ? $parts[1] : $grid['kode_loker'];
                        ?>
                            <div class="locker-box <?= $class_status; ?>">
                                <div class="box-number"><?= htmlspecialchars($num); ?></div>
                                <div class="box-label"><?= htmlspecialchars($grid['kode_loker']); ?></div>
                                <span class="box-status-tag"><?= htmlspecialchars($status_tampil_grid); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="card">
                    <div class="table-header-flex">
                        <div class="card-title" style="margin-bottom: 0;">Daftar Inventory Locker</div>
                        <button class="btn-action-add">+ Tambah Unit Locker</button>
                    </div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>KODE LOCKER</th>
                                    <th>LOKASI CLUSTER</th>
                                    <th>UKURAN</th>
                                    <th>KONDISI STATUS</th>
                                    <th style="text-align: center; width: 28%;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_table = mysqli_query($koneksi, "SELECT * FROM lockers ORDER BY kode_loker ASC");
                                if (mysqli_num_rows($query_table) > 0) {
                                    while ($row = mysqli_fetch_assoc($query_table)) {
                                        
                                        $status = strtolower($row['status'] ?? 'tersedia');
                                        $bg_color = '#d1fae5'; $text_color = '#065f46';
                                        
                                        if ($status == 'dibooking' || $status == 'terpakai' || $status == 'booking') {
                                            $bg_color = '#fef3c7'; $text_color = '#92400e';
                                        } elseif ($status == 'kosong' || $status == 'rusak') {
                                            $bg_color = '#fee2e2'; $text_color = '#991b1b';
                                        }
                                ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['kode_loker']); ?></strong></td>
                                        <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                        <td>Ukuran <?= htmlspecialchars($row['ukuran']); ?></td>
                                        <td>
                                            <span id="badge-status-<?= $row['id']; ?>" style="background-color: <?= $bg_color; ?>; color: <?= $text_color; ?>; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; display: inline-block; min-width: 90px; text-align: center;">
                                        <?= (!empty(trim($row['status'] ?? ''))) ? htmlspecialchars($row['status']) : 'Tersedia'; ?>
                                            </span>
                                        </td>
                                        
                                        <td style="text-align: center;">
                                            <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                                                
                                                <div class="edit-wrapper">
                                                    <button type="button" class="btn-trigger-edit" id="btn-edit-<?= $row['id']; ?>" onclick="tampilkanOpsiStatus(<?= $row['id']; ?>)" style="display: inline-flex; align-items: center; gap: 6px; background-color: #ffc107; color: #212529; padding: 6px 14px; border-radius: 6px; border: none; font-weight: bold; font-size: 14px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                        Edit
                                                    </button>

                                                    <div id="opsi-status-container-<?= $row['id']; ?>" style="display: none; gap: 4px; align-items: center;">
                                                        <button type="button" class="btn-status-mini btn-mini-tersedia" onclick="eksekusiUpdateStatus(<?= $row['id']; ?>, 'Tersedia')">Tersedia</button>
                                                        <button type="button" class="btn-status-mini btn-mini-booking" onclick="eksekusiUpdateStatus(<?= $row['id']; ?>, 'Booking')">Booking</button>
                                                        <button type="button" class="btn-status-mini btn-mini-kosong" onclick="eksekusiUpdateStatus(<?= $row['id']; ?>, 'Kosong')">Kosong</button>
                                                    </div>
                                                </div>

                                                <a href="hapus-locker.php?id=<?= $row['id']; ?>" class="btn-hapus-tetap" style="display: inline-flex; align-items: center; gap: 6px; background-color: #dc3545; color: #ffffff; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" onclick="return confirm('Hapus loker ini?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    Hapus
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    } 
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:#718096;'>Data inventory locker masih kosong.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'user'): ?>
                <div class="card">
                    <div class="table-header-flex">
                        <div class="card-title" style="margin-bottom: 0;">Daftar Pengguna / Mahasiswa Terdaftar</div>
                        <button class="btn-action-add">+ Tambah Pengguna Baru</button>
                    </div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>NIM / ID</th>
                                    <th>NAMA LENGKAP</th>
                                    <th>EMAIL</th>
                                    <th>NO. HANDPHONE</th>
                                    <th>ROLE SISTEM</th>
                                    <th style="text-align: center; width: 20%;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_user = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, nim ASC");
                                while ($user = mysqli_fetch_assoc($query_user)) {
                                    $badge_u = ($user['role'] == 'admin') ? 'badge-danger' : 'badge-success';
                                ?>
                                    <tr>
                                        <td><strong><?= (!empty($user['nim'])) ? htmlspecialchars($user['nim']) : 'ADMIN'; ?></strong></td>
                                        <td><?= htmlspecialchars($user['nama']); ?></td>
                                        <td><?= htmlspecialchars($user['email']); ?></td>
                                        <td><?= htmlspecialchars($user['no_hp']); ?></td>
                                        <td><span class="badge-table <?= $badge_u; ?>"><?= htmlspecialchars($user['role']); ?></span></td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                                                <a href="edit-user.php?id=<?= $user['id']; ?>" style="display: inline-flex; align-items: center; gap: 6px; background-color: #ffc107; color: #212529; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    Edit
                                                </a>
                                                <a href="hapus-user.php?id=<?= $user['id']; ?>" style="display: inline-flex; align-items: center; gap: 6px; background-color: #dc3545; color: #ffffff; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" onclick="return confirm('Hapus pengguna ini?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'riwayat'): ?>
                <div class="card">
                    <div class="card-title">Log Aktivitas Operasi Akses Locker</div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>WAKTU OPERASI</th>
                                    <th>NAMA PENGGUNA</th>
                                    <th>JENIS AKSES</th>
                                    <th>STATUS OPERASI</th>
                                    <th>KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_log = mysqli_query($koneksi, "
                                    SELECT log.*, u.nama, u.nim 
                                    FROM akses_log log 
                                    LEFT JOIN users u ON log.user_id = u.id 
                                    ORDER BY log.waktu_akses DESC
                                ");
                                if (mysqli_num_rows($query_log) > 0) {
                                    while ($log = mysqli_fetch_assoc($query_log)) {
                                        $badge_l = (strtolower($log['status'] ?? '') == 'berhasil') ? 'badge-success' : 'badge-danger';
                                        $nama_user = !empty($log['nama']) ? htmlspecialchars($log['nama']) : 'Sistem / Pengguna Luar';
                                        $nim_user  = !empty($log['nim']) ? ' (' . htmlspecialchars($log['nim']) . ')' : ' (No ID)';
                                ?>
                                        <tr>
                                            <td><?= date('d M Y, H:i', strtotime($log['waktu_akses'])); ?> WIB</td>
                                            <td><?= $nama_user . $nim_user; ?></td>
                                            <td><strong>Perintah <?= strtoupper($log['jenis'] ?? 'AKSES'); ?></strong></td>
                                            <td><span class="badge-table <?= $badge_l; ?>"><?= strtoupper($log['status'] ?? '-'); ?></span></td>
                                            <td><span class="msg-preview"><?= htmlspecialchars($log['keterangan'] ?? '-'); ?></span></td>
                                        </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center; color:#718096; padding:15px;'>Belum ada log rekaman aktivitas pintu loker.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'pesan_keluar'): ?>
                <div class="card">
                    <div class="card-title">Log Notifikasi Sistem / Antrean Pesan (`notifikasi`)</div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>WAKTU</th>
                                    <th>PENERIMA</th>
                                    <th>JUDUL NOTIFIKASI</th>
                                    <th>ISI NOTIFIKASI</th>
                                    <th>JENIS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_notif = mysqli_query($koneksi, "
                                    SELECT n.*, u.nama 
                                    FROM notifikasi n
                                    JOIN users u ON n.user_id = u.id
                                    ORDER BY n.created_at DESC
                                ");
                                if (mysqli_num_rows($query_notif) > 0) {
                                    while ($notif = mysqli_fetch_assoc($query_notif)) {
                                        $badge_n = 'badge-success';
                                        if ($notif['jenis'] == 'peringatan') { $badge_n = 'badge-danger'; }
                                        if ($notif['jenis'] == 'pengingat') { $badge_n = 'badge-warning'; }
                                ?>
                                        <tr>
                                            <td><?= date('d M Y, H:i', strtotime($notif['created_at'])); ?></td>
                                            <td><?= htmlspecialchars($notif['nama']); ?></td>
                                            <td><strong><?= htmlspecialchars($notif['judul']); ?></strong></td>
                                            <td><span class="msg-preview">"<?= htmlspecialchars($notif['pesan']); ?>"</span></td>
                                            <td><span class="badge-table <?= $badge_n; ?>"><?= strtoupper($notif['jenis']); ?></span></td>
                                        </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center; color:#718096; padding:15px;'>Belum ada data riwayat notifikasi keluar di database.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'inbox'): ?>
                <div class="card">
                    <div class="card-title">Inbox Laporan Kendala Mahasiswa (`keluhan`)</div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>WAKTU LAPOR</th>
                                    <th>MAHASISWA</th>
                                    <th>JUDUL KELUHAN</th>
                                    <th>DESKRIPSI LAPORAN</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_keluhan = mysqli_query($koneksi, "
                                    SELECT k.*, u.nama 
                                    FROM keluhan k
                                    JOIN users u ON k.user_id = u.id
                                    ORDER BY k.created_at DESC
                                ");
                                if (mysqli_num_rows($query_keluhan) > 0) {
                                    while ($kel = mysqli_fetch_assoc($query_keluhan)) {
                                        $badge_k = 'badge-warning';
                                        if ($kel['status'] == 'proses') { $badge_k = 'badge-danger'; }
                                        if ($kel['status'] == 'selesai') { $badge_k = 'badge-success'; }
                                ?>
                                        <tr>
                                            <td><?= date('d M Y, H:i', strtotime($kel['created_at'])); ?></td>
                                            <td><?= htmlspecialchars($kel['nama']); ?></td>
                                            <td><strong><?= htmlspecialchars($kel['judul']); ?></strong></td>
                                            <td><span class="msg-preview">"<?= htmlspecialchars($kel['deskripsi']); ?>"</span></td>
                                            <td><span class="badge-table <?= $badge_k; ?>"><?= strtoupper($kel['status']); ?></span></td>
                                        </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center; color:#718096; padding:15px;'>Inbox bersih. Belum ada laporan keluhan masuk dari mahasiswa.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-title">Info Sistem & Locker</div>
                        <div class="info-list">
                            <div class="info-item">
                                <div class="dot-green"></div>
                                <div>Pembersihan database otomatis dijadwalkan setiap akhir bulan. <span class="badge-important">penting!</span></div>
                            </div>
                            <div class="info-item">
                                <div class="dot-green"></div>
                                <div>Integrasi enkripsi PIN locker diperbarui ke versi v2.1. <span class="badge-new">baru!</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title">Panduan Admin</div>
                        <ul class="tutorial-list">
                            <li>1. Bagaimana cara memantau unit log aktivitas? Pilih menu <b>Log Transaksi</b>.</li>
                            <li>2. Cara merespons keluhan mahasiswa? Silakan periksa menu <b>Inbox / Pesan Masuk</b>.</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Munculkan barisan 3 tombol status saat tombol edit utama diklik
    function tampilkanOpsiStatus(id) {
        const btnEdit = document.getElementById(`btn-edit-${id}`);
        const containerOpsi = document.getElementById(`opsi-status-container-${id}`);
        
        if (btnEdit && containerOpsi) {
            btnEdit.style.setProperty('display', 'none', 'important');
            containerOpsi.style.setProperty('display', 'inline-flex', 'important');
        }
    }

    // Eksekusi perubahan status ke backend database tanpa reload halaman
    function eksekusiUpdateStatus(id, statusBaru) {
        const badgeStatus = document.getElementById(`badge-status-${id}`);
        const btnEdit = document.getElementById(`btn-edit-${id}`);
        const containerOpsi = document.getElementById(`opsi-status-container-${id}`);

        fetch(`update-status.php?id=${id}&status=${statusBaru}`)
        .then(response => {
            if (response.ok) {
                // Update text badge kondisi status di tabel
                badgeStatus.innerText = statusBaru;

                // Ubah warna latar badge secara realtime
                const statusKecil = statusBaru.toLowerCase();
                if (statusKecil === 'booking' || statusKecil === 'dibooking' || statusKecil === 'terpakai') {
                    badgeStatus.style.backgroundColor = '#fef3c7';
                    badgeStatus.style.color = '#92400e';
                } else if (statusKecil === 'kosong' || statusKecil === 'rusak') {
                    badgeStatus.style.backgroundColor = '#fee2e2';
                    badgeStatus.style.color = '#991b1b';
                } else { // Tersedia
                    badgeStatus.style.backgroundColor = '#d1fae5';
                    badgeStatus.style.color = '#065f46';
                }

                // Kembalikan 3 tombol mini menjadi Tombol Edit semula
                containerOpsi.style.setProperty('display', 'none', 'important');
                btnEdit.style.setProperty('display', 'inline-flex', 'important');

                // Sinkronisasi otomatis bagian Denah Grid Visualisasi di bagian atas
                fetch('admin.php?page=locker')
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const gridBaru = doc.getElementById('visualisasi-grid-loker');
                    if(gridBaru) {
                        document.getElementById('visualisasi-grid-loker').innerHTML = gridBaru.innerHTML;
                    }
                });

            } else {
                alert('Gagal memperbarui status data loker.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gangguan koneksi jaringan.');
        });
    }
    </script>
</body>
</html>
<?php 
mysqli_close($koneksi); 
?>