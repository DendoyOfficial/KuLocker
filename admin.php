<?php
// 1. PANGGIL AUTH DAN CONNECTION (Pastikan file-file ini berada di dalam folder config)
require_once "config/auth.php";       
require_once "config/connection.php"; 

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// 2. Proteksi Level Admin (Memastikan hanya admin yang bisa masuk)
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: sign-in.php");
    exit;
}

$admin_nama = $_SESSION['user']['nama'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah_locker') {
        $kode_loker = trim($_POST['kode_loker'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $ukuran = trim($_POST['ukuran'] ?? '');

        if ($kode_loker !== '' && $lokasi !== '' && in_array($ukuran, ['S', 'M', 'L'], true)) {
            $kode_loker_safe = mysqli_real_escape_string($conn, $kode_loker);
            $lokasi_safe = mysqli_real_escape_string($conn, $lokasi);
            $ukuran_safe = mysqli_real_escape_string($conn, $ukuran);

            $cek_aktif = mysqli_query($conn, "SELECT id FROM lockers WHERE kode_loker = '$kode_loker_safe' AND (is_deleted = 0 OR is_deleted IS NULL)");
            if (mysqli_num_rows($cek_aktif) === 0) {
                $cek_hapus = mysqli_query($conn, "SELECT id FROM lockers WHERE kode_loker = '$kode_loker_safe' AND is_deleted = 1 ORDER BY id DESC LIMIT 1");
                if (mysqli_num_rows($cek_hapus) > 0) {
                    $hapus_row = mysqli_fetch_assoc($cek_hapus);
                    $restore_id = intval($hapus_row['id']);
                    mysqli_query($conn, "UPDATE lockers SET lokasi = '$lokasi_safe', ukuran = '$ukuran_safe', status = 'Tersedia', is_deleted = 0 WHERE id = $restore_id");
                } else {
                    mysqli_query($conn, "INSERT INTO lockers (kode_loker, lokasi, ukuran, status, is_deleted, created_at) VALUES ('$kode_loker_safe', '$lokasi_safe', '$ukuran_safe', 'Tersedia', 0, NOW())");
                }
                header('Location: admin.php?page=locker&msg=tambah_berhasil');
                exit;
            } else {
                header('Location: admin.php?page=locker&msg=tambah_gagal');
                exit;
            }
        } else {
            header('Location: admin.php?page=locker&msg=tambah_gagal');
            exit;
        }
    }

    if ($_POST['action'] === 'hapus_locker') {
        $locker_id = intval($_POST['locker_id'] ?? 0);
        if ($locker_id > 0) {
            // Soft-delete the locker so related pemesanan history remains intact
            mysqli_query($conn, "UPDATE lockers SET is_deleted = 1 WHERE id = $locker_id");
            header('Location: admin.php?page=locker&msg=hapus_berhasil');
            exit;
        }
        header('Location: admin.php?page=locker&msg=hapus_gagal');
        exit;
    }
}

// Generate inisial huruf untuk avatar secara otomatis
$kata = explode(" ", $admin_nama);
$admin_avatar = strtoupper(substr($kata[0], 0, 1)) . (isset($kata[1]) ? strtoupper(substr($kata[1], 1, 1)) : "");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - KuLocker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
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
            <li class="<?= ($page == 'inbox') ? 'active' : ''; ?>">
                <a href="admin.php?page=inbox">Inbox / Pesan Masuk</a>
            </li>
        </ul>
        <div class="sidebar-section-title">Sistem</div>
        <ul class="sidebar-menu">
           <li><a href="config/logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </div>

    <div class="main-container">
        
        <div class="topbar">
            <div class="topbar-info">
                <?php if ($page == 'dashboard'): ?>
                    Dashboard Utama - Monitoring Sistem KuLocker
                <?php elseif ($page == 'locker'): ?>
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
            
            <?php if ($page == 'dashboard'): ?>
                <!-- DASHBOARD SECTION -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <?php
                    // Total Lockers (exclude soft-deleted)
                    $total_lockers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM lockers WHERE (is_deleted = 0 OR is_deleted IS NULL)"))['count'];
                    $lockers_tersedia = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM lockers WHERE status = 'tersedia' AND (is_deleted = 0 OR is_deleted IS NULL)"))['count'];
                    $lockers_terpakai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM lockers WHERE status = 'terpakai' AND (is_deleted = 0 OR is_deleted IS NULL)"))['count'];
                    $lockers_rusak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM lockers WHERE status = 'rusak' AND (is_deleted = 0 OR is_deleted IS NULL)"))['count'];
                    
                    // Total Users
                    $total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'mahasiswa'"))['count'];
                    
                    // Active Pemesanan
                    $pemesanan_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pemesanan WHERE status = 'aktif'"))['count'];
                    $pemesanan_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pemesanan WHERE status = 'pending'"))['count'];
                    
                    // Payments
                    $pembayaran_lunas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pembayaran WHERE status = 'lunas'"))['count'];
                    $pembayaran_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pembayaran WHERE status = 'pending'"))['count'];
                    
                    // Complaints
                    $keluhan_open = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM keluhan WHERE status = 'open'"))['count'];
                    ?>
                    
                    <!-- Card 1: Total Lockers -->
                    <div class="dashboard-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Total Locker</div>
                                <div style="font-size: 32px; font-weight: bold;"><?= $total_lockers; ?></div>
                                <div style="font-size: 12px; opacity: 0.8; margin-top: 10px;">
                                    ✓ <?= $lockers_tersedia; ?> Tersedia | ⚠ <?= $lockers_terpakai; ?> Terpakai
                                </div>
                            </div>
                            <div style="font-size: 40px; opacity: 0.3;">📦</div>
                        </div>
                    </div>

                    <!-- Card 2: Active Orders -->
                    <div class="dashboard-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Pemesanan Aktif</div>
                                <div style="font-size: 32px; font-weight: bold;"><?= $pemesanan_aktif; ?></div>
                                <div style="font-size: 12px; opacity: 0.8; margin-top: 10px;">
                                    ⏳ <?= $pemesanan_pending; ?> Pending
                                </div>
                            </div>
                            <div style="font-size: 40px; opacity: 0.3;">📋</div>
                        </div>
                    </div>

                    <!-- Card 3: Registered Users -->
                    <div class="dashboard-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Total Pengguna</div>
                                <div style="font-size: 32px; font-weight: bold;"><?= $total_users; ?></div>
                                <div style="font-size: 12px; opacity: 0.8; margin-top: 10px;">
                                    👥 Mahasiswa Terdaftar
                                </div>
                            </div>
                            <div style="font-size: 40px; opacity: 0.3;">👨</div>
                        </div>
                    </div>

                    <!-- Card 4: Payment Status -->
                    <div class="dashboard-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Pembayaran Lunas</div>
                                <div style="font-size: 32px; font-weight: bold;"><?= $pembayaran_lunas; ?></div>
                                <div style="font-size: 12px; opacity: 0.8; margin-top: 10px;">
                                    ⏳ <?= $pembayaran_pending; ?> Pending
                                </div>
                            </div>
                            <div style="font-size: 40px; opacity: 0.3;">💳</div>
                        </div>
                    </div>
                </div>

                <!-- Status Overview Section -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    
                    <!-- Locker Status Distribution -->
                    <div class="card">
                        <div class="card-title">Status Distribusi Locker</div>
                        <div style="padding: 20px 0;">
                            <?php
                            $total = $lockers_tersedia + $lockers_terpakai + $lockers_rusak;
                            $percent_tersedia = ($total > 0) ? round(($lockers_tersedia / $total) * 100) : 0;
                            $percent_terpakai = ($total > 0) ? round(($lockers_terpakai / $total) * 100) : 0;
                            $percent_rusak = ($total > 0) ? round(($lockers_rusak / $total) * 100) : 0;
                            ?>
                            
                            <div style="margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px;">
                                    <span>✓ Tersedia</span>
                                    <strong><?= $percent_tersedia; ?>%</strong>
                                </div>
                                <div style="background-color: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background-color: #28a745; height: 100%; width: <?= $percent_tersedia; ?>%;"></div>
                                </div>
                            </div>

                            <div style="margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px;">
                                    <span>⚠ Terpakai</span>
                                    <strong><?= $percent_terpakai; ?>%</strong>
                                </div>
                                <div style="background-color: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background-color: #ffc107; height: 100%; width: <?= $percent_terpakai; ?>%;"></div>
                                </div>
                            </div>

                            <div style="margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px;">
                                    <span>❌ Rusak</span>
                                    <strong><?= $percent_rusak; ?>%</strong>
                                </div>
                                <div style="background-color: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background-color: #dc3545; height: 100%; width: <?= $percent_rusak; ?>%;"></div>
                                </div>
                            </div>

                            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 6px; margin-top: 15px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; text-align: center;">
                                    <div>
                                        <div style="font-size: 20px; font-weight: bold; color: #28a745;"><?= $lockers_tersedia; ?></div>
                                        <div style="font-size: 11px; color: #666;">Tersedia</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 20px; font-weight: bold; color: #ffc107;"><?= $lockers_terpakai; ?></div>
                                        <div style="font-size: 11px; color: #666;">Terpakai</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 20px; font-weight: bold; color: #dc3545;"><?= $lockers_rusak; ?></div>
                                        <div style="font-size: 11px; color: #666;">Rusak</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>



            <?php elseif ($page == 'locker'): ?>
                
                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'hapus_berhasil'): ?>
                    <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-weight: 500;">
                        ✓ Unit locker berhasil dihapus dari sistem.
                    </div>
                <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'hapus_terkait'): ?>
                    <div style="background-color: #fef3c7; color: #92400e; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-weight: 500;">
                        ⚠️ Tidak dapat menghapus locker karena masih terkait dengan riwayat pemesanan.
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-title">Visualisasi Denah Status Locker</div>
                    <div class="locker-grid-container" id="visualisasi-grid-loker">
                        <?php
                        $query_grid = mysqli_query($conn, "SELECT * FROM lockers WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY kode_loker ASC");
                        while ($grid = mysqli_fetch_assoc($query_grid)) {
                            $status_tampil_grid = (!empty(trim($grid['status'] ?? ''))) ? $grid['status'] : 'Tersedia';
                            $status_grid = strtolower($status_tampil_grid);
                            
                            $class_status = "status-empty"; 
                            if ($status_grid == 'terpakai') { $class_status = "status-filled"; }
                            if ($status_grid == 'rusak') { $class_status = "status-warning"; }
                            
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
                        <a href="tambah-locker.php" class="btn-action-add" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Tambah Unit Locker</a>
                    </div>
                    <div class="table-responsive">
                        <div id="modal-tambah-loker" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1200; align-items:center; justify-content:center;">
                            <div style="background:#ffffff; border-radius:12px; padding:28px; width:100%; max-width:520px; box-shadow:0 24px 60px rgba(0,0,0,0.18); position:relative;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
                                    <div style="font-size:18px; font-weight:700;">Tambah Unit Locker</div>
                                    <button type="button" onclick="tutupModalTambahLocker()" style="background:transparent; border:none; color:#374151; font-size:24px; line-height:1; cursor:pointer;">&times;</button>
                                </div>
                                <form method="POST" action="admin.php?page=locker">
                                    <input type="hidden" name="action" value="tambah_locker">
                                    <div style="display:grid; gap:16px;">
                                        <label style="font-weight:600; color:#111827; font-size:14px;">Kode Locker</label>
                                        <input type="text" name="kode_loker" required placeholder="Contoh: LKR-001" style="width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:10px; font-size:14px;">

                                        <label style="font-weight:600; color:#111827; font-size:14px;">Lokasi Cluster</label>
                                        <input type="text" name="lokasi" required placeholder="Contoh: Cluster A" style="width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:10px; font-size:14px;">

                                        <label style="font-weight:600; color:#111827; font-size:14px;">Ukuran</label>
                                        <select name="ukuran" required style="width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:10px; font-size:14px; background:#ffffff;">
                                            <option value="">Pilih ukuran</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                        </select>

                                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:8px;">
                                            <button type="button" onclick="tutupModalTambahLocker()" style="background:#e5e7eb; color:#111827; border:none; border-radius:10px; padding:10px 18px; font-weight:600; cursor:pointer;">Batal</button>
                                            <button type="submit" style="background:#0f766e; color:white; border:none; border-radius:10px; padding:10px 18px; font-weight:700; cursor:pointer;">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
                                $query_table = mysqli_query($conn, "SELECT * FROM lockers WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY kode_loker ASC");
                                if (mysqli_num_rows($query_table) > 0) {
                                    while ($row = mysqli_fetch_assoc($query_table)) {
                                        
                                        $status = strtolower($row['status'] ?? 'tersedia');
                                        $bg_color = '#d1fae5'; $text_color = '#065f46';
                                        
                                        if ($status == 'terpakai') {
                                            $bg_color = '#fef3c7'; $text_color = '#92400e';
                                        } elseif ($status == 'rusak') {
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
                                                        <button type="button" class="btn-status-mini" style="background-color: #28a745; color: white; padding: 4px 8px; border: none; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;" onclick="eksekusiUpdateStatus(<?= $row['id']; ?>, 'Tersedia')">Tersedia</button>
                                                        <button type="button" class="btn-status-mini" style="background-color: #ffc107; color: #212529; padding: 4px 8px; border: none; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;" onclick="eksekusiUpdateStatus(<?= $row['id']; ?>, 'Terpakai')">Terpakai</button>
                                                        <button type="button" class="btn-status-mini" style="background-color: #dc3545; color: white; padding: 4px 8px; border: none; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;" onclick="eksekusiUpdateStatus(<?= $row['id']; ?>, 'Rusak')">Rusak</button>
                                                    </div>
                                                </div>

                                                <button type="button" class="btn-hapus-tetap" onclick="bukaModalHapusLocker(<?= $row['id']; ?>, '<?= htmlspecialchars($row['kode_loker'], ENT_QUOTES); ?>')" style="display: inline-flex; align-items: center; gap: 6px; background-color: #dc3545; color: #ffffff; padding: 6px 14px; border-radius: 6px; border: none; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); cursor: pointer;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    Hapus
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    } 
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:#718096;'>Data inventory locker kosong.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="modal-hapus-loker" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1200; align-items:center; justify-content:center;">
                    <div style="background:#ffffff; border-radius:12px; padding:28px; width:100%; max-width:460px; box-shadow:0 24px 60px rgba(0,0,0,0.18); position:relative;">
                        <div style="font-size:18px; font-weight:700; margin-bottom:16px;">Konfirmasi Hapus Locker</div>
                        <div style="margin-bottom:18px; color:#374151; line-height:1.6;">
                            Apakah Anda yakin ingin menghapus locker <strong id="hapus-locker-label">-</strong> dari sistem? Tindakan ini tidak dapat dikembalikan.
                        </div>
                        <div style="display:flex; justify-content:flex-end; gap:10px;">
                            <button type="button" onclick="tutupModalHapusLocker()" style="background:#e5e7eb; color:#111827; border:none; border-radius:10px; padding:10px 18px; font-weight:600; cursor:pointer;">Batal</button>
                            <form id="form-hapus-loker" method="POST" action="admin.php?page=locker" style="margin:0;">
                                <input type="hidden" name="action" value="hapus_locker">
                                <input type="hidden" name="locker_id" id="hapus-locker-id" value="">
                                <button type="submit" style="background:#dc2626; color:white; border:none; border-radius:10px; padding:10px 18px; font-weight:700; cursor:pointer;">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php elseif ($page == 'user'): ?>
                <div class="card">
                    <div class="card-title">Data Pengguna</div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th>Role</th>
                                    <th>NIM (Mahasiswa)</th>
                                    <th>Terdaftar</th>
                                    <th style="text-align: center; width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_users = mysqli_query($conn, "
                                    SELECT * FROM users 
                                    ORDER BY created_at DESC
                                ");
                                
                                if (mysqli_num_rows($query_users) > 0) {
                                    while ($row = mysqli_fetch_assoc($query_users)) {
                                        $role = strtoupper($row['role']);
                                        $badge_color = $role === 'ADMIN' ? '#dc3545' : '#007bff';
                                ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['nama']); ?></strong></td>
                                        <td><?= htmlspecialchars($row['email']); ?></td>
                                        <td><?= htmlspecialchars($row['no_hp']); ?></td>
                                        <td>
                                            <span style="background-color: <?= $badge_color; ?>; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold;">
                                                <?= $role; ?>
                                            </span>
                                        </td>
                                        <td><?= !empty($row['nim']) ? htmlspecialchars($row['nim']) : '-'; ?></td>
                                        <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                                        <td style="text-align: center;">
                                            <button onclick="tampilkanDetailUser(<?= $row['id']; ?>, '<?= htmlspecialchars($row['nama']); ?>', '<?= htmlspecialchars($row['email']); ?>', '<?= htmlspecialchars($row['no_hp']); ?>', '<?= $role; ?>', '<?= htmlspecialchars($row['nim'] ?? ''); ?>', '<?= date('d M Y H:i', strtotime($row['created_at'])); ?>')" style="background-color: #17a2b8; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600;">Detail</button>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center; padding:20px; color:#718096;'>Belum ada data pengguna.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Detail User -->
                <div id="modal-detail-user" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:white; border-radius:10px; padding:30px; max-width:500px; width:90%;">
                        <div style="font-size:18px; font-weight:bold; margin-bottom:20px;">Detail Pengguna</div>
                        <div style="margin-bottom:15px;">
                            <label style="font-weight:600; color:#333;">Nama:</label>
                            <div id="detail-user-nama">-</div>
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-weight:600; color:#333;">Email:</label>
                            <div id="detail-user-email">-</div>
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-weight:600; color:#333;">No. HP:</label>
                            <div id="detail-user-hp">-</div>
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-weight:600; color:#333;">Role:</label>
                            <div id="detail-user-role">-</div>
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-weight:600; color:#333;">NIM:</label>
                            <div id="detail-user-nim">-</div>
                        </div>
                        <div style="margin-bottom:20px;">
                            <label style="font-weight:600; color:#333;">Terdaftar:</label>
                            <div id="detail-user-terdaftar">-</div>
                        </div>
                        <button onclick="tutupModalUser()" style="width:100%; background-color:#6c757d; color:white; padding:10px; border:none; border-radius:6px; font-weight:600; cursor:pointer;">Tutup</button>
                    </div>
                </div>

            <?php elseif ($page == 'riwayat'): ?>

                <!-- Riwayat Pemesanan -->
                <div class="card" style="margin-bottom: 30px;">
                    <div class="card-title">Riwayat Pemesanan</div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>Tanggal Pesan</th>
                                    <th>Pengguna</th>
                                    <th>Locker</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th style="text-align: center; width: 12%;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_pemesanan = mysqli_query($conn, "
                                    SELECT p.*, u.nama, l.kode_loker
                                    FROM pemesanan p
                                    JOIN users u ON p.user_id = u.id
                                    JOIN lockers l ON p.locker_id = l.id
                                    ORDER BY p.created_at DESC
                                    LIMIT 50
                                ");
                                
                                if (mysqli_num_rows($query_pemesanan) > 0) {
                                    while ($row = mysqli_fetch_assoc($query_pemesanan)) {
                                        $status = strtolower($row['status']);
                                        $badge_color = $status == 'aktif' ? '#ffc107' : ($status == 'selesai' ? '#28a745' : '#6c757d');
                                        $text_color = $status == 'aktif' ? '#92400e' : 'white';
                                ?>
                                    <tr>
                                        <td><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td><?= htmlspecialchars($row['nama']); ?></td>
                                        <td><strong><?= htmlspecialchars($row['kode_loker']); ?></strong></td>
                                        <td><?= date('d M Y H:i', strtotime($row['tanggal_mulai'])); ?></td>
                                        <td><?= date('d M Y H:i', strtotime($row['tanggal_selesai'])); ?></td>
                                        <td>
                                            <span style="background-color: <?= $badge_color; ?>; color: <?= $text_color; ?>; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold;">
                                                <?= htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <button onclick="tampilkanDetailPemesanan(
                                                <?= $row['id']; ?>,
                                                '<?= htmlspecialchars($row['nama'], ENT_QUOTES); ?>',
                                                '<?= htmlspecialchars($row['kode_loker'], ENT_QUOTES); ?>',
                                                '<?= date('d M Y H:i', strtotime($row['created_at'])); ?>',
                                                '<?= date('d M Y H:i', strtotime($row['tanggal_mulai'])); ?>',
                                                '<?= date('d M Y H:i', strtotime($row['tanggal_selesai'])); ?>',
                                                '<?= htmlspecialchars($row['status'], ENT_QUOTES); ?>'
                                            )" style="background-color: #17a2b8; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600;">Detail</button>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center; padding:20px; color:#718096;'>Belum ada riwayat pemesanan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="modal-detail-pemesanan" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:white; border-radius:10px; padding:30px; max-width:520px; width:90%; box-shadow:0 20px 40px rgba(0,0,0,0.12);">
                        <div style="font-size:18px; font-weight:bold; margin-bottom:18px;">Detail Riwayat Pemesanan</div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; color:#333;">Pengguna:</label>
                            <div id="detail-pemesanan-nama">-</div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; color:#333;">Locker:</label>
                            <div id="detail-pemesanan-locker">-</div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; color:#333;">Tanggal Pesan:</label>
                            <div id="detail-pemesanan-tanggal-pesan">-</div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; color:#333;">Tanggal Mulai:</label>
                            <div id="detail-pemesanan-tanggal-mulai">-</div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; color:#333;">Tanggal Selesai:</label>
                            <div id="detail-pemesanan-tanggal-selesai">-</div>
                        </div>
                        <div style="margin-bottom:20px;">
                            <label style="font-weight:600; color:#333;">Status:</label>
                            <div id="detail-pemesanan-status">-</div>
                        </div>
                        <button onclick="tutupModalPemesanan()" style="width:100%; background-color:#6c757d; color:white; padding:10px; border:none; border-radius:6px; font-weight:600; cursor:pointer;">Tutup</button>
                    </div>
                </div>

            <?php elseif ($page == 'inbox'): ?>
                <div class="card">
                    <div class="card-title">Inbox / Pesan Masuk</div>
                    <div class="table-responsive">
                        <table class="locker-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Pengirim</th>
                                    <th>Loker</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query mengambil data keluhan digabung dengan nama user dan kode locker
                                $query_keluhan = mysqli_query($conn, "
                                    SELECT k.*, u.nama, l.kode_loker 
                                    FROM keluhan k
                                    LEFT JOIN users u ON k.user_id = u.id
                                    LEFT JOIN pemesanan p ON k.pemesanan_id = p.id
                                    LEFT JOIN lockers l ON p.locker_id = l.id
                                    ORDER BY k.id DESC
                                ");

                                if (mysqli_num_rows($query_keluhan) > 0) {
                                    while ($keluhan = mysqli_fetch_assoc($query_keluhan)) {
                                        $status_keluhan = strtolower($keluhan[''] ?? '');
                                        
                                        // Atur warna badge status keluhan
                                        if ($status_keluhan === 'resolved' || $status_keluhan === 'closed') {
                                            $bg_status = '#d1fae5'; $text_status = '#065f46'; // Hijau
                                        }
                                ?>
                                    <tr>
                                        <td><?= date('d M Y H:i', strtotime($keluhan['waktu_keluhan'] ?? $keluhan['created_at'] ?? 'now')); ?></td>
                                        <td><strong><?= htmlspecialchars($keluhan['nama'] ?? 'Guest/Unknown'); ?></strong></td>
                                        <td><?= htmlspecialchars($keluhan['kode_loker'] ?? '-'); ?></td>
                                        <td><?= nl2br(htmlspecialchars($keluhan['deskripsi'] ?? $keluhan['pesan'] ?? '')); ?></td>
                                        <td>
                                            <span style="background-color: <?= $bg_status; ?>; color: <?= $text_status; ?>; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; display: inline-block; text-transform: uppercase;">
                                                <?= htmlspecialchars($status_keluhan); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php 
                                    } 
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:#718096;'>Tidak ada pesan keluhan masuk.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
                
<script src="js/admin.js"></script>
</body>
</html>