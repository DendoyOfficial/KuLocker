<?php
require 'config/auth.php';
require_once 'config/connection.php';

if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: admin.php?page=dashboard');
    exit;
}

$user = $_SESSION['user'];
if (empty($user['foto_profil']) && !empty($user['id'])) {
    $user_foto_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto_profil FROM users WHERE id = " . intval($user['id']) . " LIMIT 1"));
    if ($user_foto_row && !empty($user_foto_row['foto_profil'])) {
        $user['foto_profil'] = $user_foto_row['foto_profil'];
    }
}

$user_id = $user['id'];

// Data lokasi loker (unik, dikelompokkan per gedung)
$lokasi_list = [];
$res_lok = mysqli_query($conn, "
    SELECT lokasi,
           COUNT(*) AS total,
           SUM(status = 'tersedia') AS tersedia
    FROM lockers
    WHERE (is_deleted = 0 OR is_deleted IS NULL)
    GROUP BY lokasi
    ORDER BY lokasi ASC
");
if ($res_lok) {
    while ($row = mysqli_fetch_assoc($res_lok)) {
        $lokasi_list[] = $row;
    }
}

// Pengumuman
$pengumuman_list = [];
$res_p = mysqli_query($conn, "SELECT * FROM pengumuman LIMIT 6");
if ($res_p) {
    while ($row = mysqli_fetch_assoc($res_p)) {
        $pengumuman_list[] = $row;
    }
}

// Fungsi meta pengumuman
function pengumuman_meta($kategori) {
    switch ($kategori) {
        case 'promo':       return ['icon' => 'ti-tag',            'color' => 'green',  'label' => 'Promo'];
        case 'peringatan':  return ['icon' => 'ti-alert-triangle', 'color' => 'red',    'label' => 'Peringatan'];
        case 'maintenance': return ['icon' => 'ti-tool',           'color' => 'orange', 'label' => 'Maintenance'];
        default:            return ['icon' => 'ti-info-circle',    'color' => 'blue',   'label' => 'Info'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama - KuLocker</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <link rel="stylesheet" href="css/dashboard-utama.css"/>
</head>
<body>

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav>
    <div class="nav-inner">
        <div class="nav-logo-icon">
            <img src="img/Kulocker.jpeg" alt="Logo Kulocker">
        </div>

        <div class="nav-search-bar" onclick="openMap()">
            <i class="ti ti-search"></i>
            <input type="text" placeholder="Cari Lokasi" readonly />
        </div>

        <div class="user-menu" id="userMenu">
            <div class="user-trigger" onclick="toggleDropdown(event)">
                <span class="user-name"><?= htmlspecialchars($user['nama']) ?></span>
                <div class="profil">
                    <?php if (!empty($user['foto_profil'])): ?>
                        <img src="image/<?= htmlspecialchars($user['foto_profil']) ?>" alt="user">
                    <?php else: ?>
                        <img src="img/profl.png" alt="user">
                    <?php endif; ?>
                </div>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profile.php"><i class="ti ti-user"></i> Profil Page</a>
                <a href="keluhan.php"><i class="ti ti-message-report"></i> Keluhan & Saran</a>
                <hr class="dropdown-divider">
                <a href="config/logout.php" class="logout-link"><i class="ti ti-logout"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- ═══════════════ CAROUSEL ═══════════════ -->
<div class="carousel" id="carousel">

    <div class="slide active">
        <div class="slide-bg" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2010 100%);"></div>
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <div class="slide-tag">UNIVERSITAS MATARAM</div>
            <div class="slide-title">
                Welcome back, <em><?= htmlspecialchars(explode(' ', $user['nama'])[0]) ?></em>
            </div>
            <div class="slide-desc">Pesan loker kapan saja, akses dengan kode, dan pantau status penyimpanan kamu secara real-time.</div>
            <button class="slide-btn"onclick="openMap()"><i class="ti ti-lock-square"></i> Pesan Loker Sekarang</button>
        </div>
    </div>

    <div class="slide">
        <div class="slide-bg" style="background: linear-gradient(135deg, #0d1a2d 0%, #1a2d1a 100%);"></div>
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <div class="slide-tag">FITUR BARU</div>
            <div class="slide-title">Titip <em>Barang</em> kamu sekarang</div>
            <div class="slide-desc">Titip Beban Barang Bawaan Kamu dengan Aman dan Nyaman .</div>
            <button class="slide-btn" onclick="openMap()"><i class="ti ti-map-pin"></i> Cari Loker Terdekat</button>
        </div>
    </div>

    <div class="carousel-arrow left" onclick="prevSlide()"><i class="ti ti-chevron-left"></i></div>
    <div class="carousel-arrow right" onclick="nextSlide()"><i class="ti ti-chevron-right"></i></div>
    <div class="carousel-nav" id="carouselNav"></div>
</div>

<!-- ═══════════════ FEATURE BUTTONS ═══════════════ -->
<div class="feature-section">
  <div class="feature-section-inner">
    <div class="feature-label">FITUR UTAMA</div>
    <div class="feature-grid">
        <a href="tiket-saya.php" class="feature-btn">
            <div class="feature-icon gold"><i class="ti ti-layout-grid"></i></div>
            <div class="feature-name">My Locker</div>
            <div class="feature-desc">Status loker aktif & QR Code pemesanan</div>
        </a>
        <div class="feature-btn" onclick="openMap()">
            <div class="feature-icon blue"><i class="ti ti-map-pin"></i></div>
            <div class="feature-name">Pesan Loker</div>
            <div class="feature-desc">Titipkan barang kamu sekarang</div>
        </div>
        <a href="riwayat-sewa.php" class="feature-btn">
            <div class="feature-icon green"><i class="ti ti-history"></i></div>
            <div class="feature-name">Riwayat</div>
            <div class="feature-desc">Riwayat penyimpanan loker kamu</div>
        </a>
    </div>
  </div>
</div>

<!-- ═══════════════ PENGUMUMAN ═══════════════ -->

<?php if (!empty($pengumuman_list)): ?>
<section class="pengumuman-section">
    <div class="pengumuman-inner">
        <div class="pengumuman-header">
            <div>
                <div class="feature-label">PENGUMUMAN</div>
                <h2 class="pengumuman-title">Info & Pengumuman</h2>
            </div>
            <a href="pengumuman-list.php" class="pengumuman-lihat-semua">
                Lihat semua <i class="ti ti-arrow-right"></i>
            </a>
        </div>
        <div class="pengumuman-grid">
            <?php foreach ($pengumuman_list as $p):
                $meta        = pengumuman_meta($p['kategori']);
                $tanggal     = date('d M Y', strtotime($p['created_at']));
                $isi_singkat = mb_strlen($p['isi']) > 120 ? mb_substr($p['isi'], 0, 120) . '...' : $p['isi'];
            ?>
            <div class="pengumuman-card reveal">
                <div class="pengumuman-card-top">
                    <div class="pengumuman-badge <?= $meta['color'] ?>">
                        <i class="ti <?= $meta['icon'] ?>"></i> <?= $meta['label'] ?>
                    </div>
                    <span class="pengumuman-tanggal"><?= $tanggal ?></span>
                </div>
                <h3 class="pengumuman-judul"><?= htmlspecialchars($p['judul']) ?></h3>
                <p class="pengumuman-isi"><?= htmlspecialchars($isi_singkat) ?></p>
                <?php if ($p['expired_at']): ?>
                <div class="pengumuman-expired">
                    <i class="ti ti-clock"></i>
                    Berlaku hingga <?= date('d M Y', strtotime($p['expired_at'])) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ═══════════════ FOOTER ═══════════════ -->
<footer>
    <div class="footer-inner">
        <div class="footer-bottom">
            <p class="footer-copy">© 2026 Kulocker. All rights reserved.</p>
        </div>
    </div>
</footer>

<iframe src="api/cron-pengingat.php" style="display:none; width:0; height:0; border:none;"></iframe>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/pnHo=" crossorigin=""></script>
<script src="js/dashboard-utama.js"></script>
</body>
</html>