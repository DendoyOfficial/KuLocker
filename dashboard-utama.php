<?php
require 'config/auth.php';
require_once 'config/connection.php';



$user = $_SESSION['user'];

$user_id = $user['id'];

// Data lokasi loker (unik, dikelompokkan per gedung)
$lokasi_list = [];
$res_lok = mysqli_query($conn, "
    SELECT lokasi,
           COUNT(*) AS total,
           SUM(status = 'tersedia') AS tersedia
    FROM lockers
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
                    <img src="img/profl.png" alt="user">
                </div>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profil.php"><i class="ti ti-user"></i> Profil Page</a>
                <a href="settings.php"><i class="ti ti-settings"></i> Settings</a>
                <a href="keluhan.php"><i class="ti ti-message-report"></i> Keluhan</a>
                <a href="#faq"><i class="ti ti-help-circle"></i> Bantuan</a>
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
            <div class="slide-title">Temukan Loker <em>Terdekat</em> dari Lokasimu</div>
            <div class="slide-desc">Gunakan fitur peta interaktif OpenStreetMap untuk menemukan loker yang paling dekat dengan kamu.</div>
            <button class="slide-btn" onclick="openMap()"><i class="ti ti-map-pin"></i> Cari Loker Terdekat</button>
        </div>
    </div>

    <div class="slide">
        <div class="slide-bg" style="background: linear-gradient(135deg, #1a0d2d 0%, #2d1a0d 100%);"></div>
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <div class="slide-tag">KEAMANAN</div>
            <div class="slide-title">Akses Aman dengan <em>Verifikasi</em> WhatsApp</div>
            <div class="slide-desc">Setiap pemesanan dilindungi dengan verifikasi OTP melalui WhatsApp untuk keamanan maksimal.</div>
            <a href="panduan.php" class="slide-btn"><i class="ti ti-shield-check"></i> Pelajari Lebih Lanjut</a>
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
        <a href="my-loker.php" class="feature-btn">
            <div class="feature-icon gold"><i class="ti ti-layout-grid"></i></div>
            <div class="feature-name">My Locker</div>
            <div class="feature-desc">Status loker aktif & QR Code pemesanan</div>
        </a>
        <div class="feature-btn" onclick="openMap()">
            <div class="feature-icon blue"><i class="ti ti-map-pin"></i></div>
            <div class="feature-name">Pesan Loker</div>
            <div class="feature-desc">Temukan loker terdekat via peta</div>
        </div>
        <a href="riwayat.php" class="feature-btn">
            <div class="feature-icon green"><i class="ti ti-history"></i></div>
            <div class="feature-name">Riwayat</div>
            <div class="feature-desc">Riwayat penyimpanan loker kamu</div>
        </a>
        <a href="notifikasi.php" class="feature-btn">
            <div class="feature-icon red"><i class="ti ti-bell"></i></div>
            <div class="feature-name">Notifikasi</div>
            <div class="feature-desc">Pantau status dan pengingat sewa</div>
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

<!-- ═══════════════ MAP MODAL ═══════════════ -->
<div class="modal-overlay" id="mapModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title"><i class="ti ti-map-2"></i> Cari Loker Terdekat</div>
            <button class="modal-close" id="mapClose"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-search">
            <div class="modal-search-bar">
                <i class="ti ti-search"></i>
                <input type="text" id="mapSearch" placeholder="Cari gedung atau kode loker..."/>
            </div>
            <button class="btn-gps" id="gpsBtn">
                <i class="ti ti-current-location"></i> Lokasi Saya
            </button>
        </div>

        <?php if (!empty($lokasi_list)): ?>
        <div class="modal-lokasi-wrap">
            <div class="modal-lokasi-label">Pilih Lokasi</div>
            <div class="modal-lokasi-grid" id="modalLokasiGrid">
                <?php foreach ($lokasi_list as $lok):
                    $slug         = urlencode($lok['lokasi']);
                    $status_class = $lok['tersedia'] > 0 ? 'ada' : 'penuh';
                    $status_label = $lok['tersedia'] > 0 ? $lok['tersedia'] . ' tersedia' : 'Penuh';
                ?>
                <a href="locker-selection.php?lokasi=<?= $slug ?>"
                   class="modal-lokasi-card"
                   data-lokasi="<?= strtolower(htmlspecialchars($lok['lokasi'])) ?>">
                    <div class="modal-lokasi-icon">
                        <i class="ti ti-building"></i>
                    </div>
                    <div class="modal-lokasi-info">
                        <span class="modal-lokasi-name"><?= htmlspecialchars($lok['lokasi']) ?></span>
                        <span class="modal-lokasi-sub"><?= $lok['total'] ?> loker</span>
                    </div>
                    <span class="modal-lokasi-badge <?= $status_class ?>"><?= $status_label ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div id="map"></div>
        <div class="modal-list" id="lokerList"></div>
    </div>
</div>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/pnHo=" crossorigin=""></script>
<script src="js/dashboard-utama.js"></script>
</body>
</html>