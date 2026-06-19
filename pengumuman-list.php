<?php
require 'config/auth.php';
require_once 'config/connection.php';

$user = $_SESSION['user'];

// Filter kategori
$kategori_filter = $_GET['kategori'] ?? 'semua';
$allowed = ['semua', 'info', 'promo', 'peringatan', 'maintenance'];
if (!in_array($kategori_filter, $allowed)) $kategori_filter = 'semua';

// Pagination
$per_page = 9;
$page     = max(1, (int) ($_GET['page'] ?? 1));
$offset   = ($page - 1) * $per_page;

// Query total
$where = "WHERE is_aktif = 1 AND (expired_at IS NULL OR expired_at > NOW())";
if ($kategori_filter !== 'semua') {
    $kat = mysqli_real_escape_string($conn, $kategori_filter);
    $where .= " AND kategori = '$kat'";
}

$total     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman $where"))['total'];
$total_page = ceil($total / $per_page);

// Query data
$result = mysqli_query($conn, "SELECT * FROM pengumuman $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
$pengumuman_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pengumuman_list[] = $row;
}

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
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Pengumuman – KuLocker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <link rel="stylesheet" href="css/dashboard-utama.css"/>
    <link rel="stylesheet" href="css/pengumuman-list.css"/>
</head>
<body>

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav>
    <div class="nav-inner">
        <div class="nav-logo-icon">
            <img src="img/Kulocker.jpeg" alt="Logo KuLocker">
        </div>
        <div class="nav-search-bar">
            <i class="ti ti-search"></i>
            <input type="text" placeholder="Cari Lokasi" readonly/>
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
                <hr class="dropdown-divider">
                <a href="config/logout.php" class="logout-link"><i class="ti ti-logout"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- ═══════════════ KONTEN ═══════════════ -->
<div class="pgl-wrap">

    <!-- Breadcrumb -->
    <div class="pgl-breadcrumb">
        <a href="dashboard-utama.php"><i class="ti ti-home"></i> Dashboard</a>
        <i class="ti ti-chevron-right"></i>
        <span>Pengumuman</span>
    </div>

<!-- Tombol kembali -->
    <a href="dashboard-utama.php" class="pgl-back-link">
        <i class="ti ti-arrow-left"></i> Kembali ke Dashboard
    </a>

    <!-- Header -->
    <div class="pgl-header">
        <div>
            <div class="feature-label">PENGUMUMAN</div>
            <h1 class="pgl-title">Info & Pengumuman</h1>
            <p class="pgl-subtitle">Informasi terbaru seputar KuLocker Universitas Mataram</p>
        </div>
        <div class="pgl-total">
            <span><?= $total ?> pengumuman</span>
        </div>
    </div>

    <!-- Filter Kategori -->
    <div class="pgl-filter">
        <?php
        $filters = [
            'semua'       => ['label' => 'Semua',       'icon' => 'ti-layout-grid'],
            'info'        => ['label' => 'Info',         'icon' => 'ti-info-circle'],
            'promo'       => ['label' => 'Promo',        'icon' => 'ti-tag'],
            'peringatan'  => ['label' => 'Peringatan',   'icon' => 'ti-alert-triangle'],
            'maintenance' => ['label' => 'Maintenance',  'icon' => 'ti-tool'],
        ];
        foreach ($filters as $key => $f):
        ?>
        <a href="?kategori=<?= $key ?>"
           class="pgl-filter-btn <?= $kategori_filter === $key ? 'active' : '' ?>">
            <i class="ti <?= $f['icon'] ?>"></i>
            <?= $f['label'] ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Grid Pengumuman -->
    <?php if (!empty($pengumuman_list)): ?>
    <div class="pengumuman-grid">
        <?php foreach ($pengumuman_list as $p):
            $meta        = pengumuman_meta($p['kategori']);
            $tanggal     = date('d M Y', strtotime($p['created_at']));
            $isi_singkat = mb_strlen($p['isi']) > 150
                ? mb_substr($p['isi'], 0, 150) . '...'
                : $p['isi'];
        ?>
        <div class="pengumuman-card">
            <div class="pengumuman-card-top">
                <div class="pengumuman-badge <?= $meta['color'] ?>">
                    <i class="ti <?= $meta['icon'] ?>"></i>
                    <?= $meta['label'] ?>
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

    <!-- Pagination -->
    <?php if ($total_page > 1): ?>
    <div class="pgl-pagination">
        <?php if ($page > 1): ?>
        <a href="?kategori=<?= $kategori_filter ?>&page=<?= $page - 1 ?>" class="pgl-page-btn">
            <i class="ti ti-chevron-left"></i>
        </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_page; $i++): ?>
        <a href="?kategori=<?= $kategori_filter ?>&page=<?= $i ?>"
           class="pgl-page-btn <?= $i === $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>

        <?php if ($page < $total_page): ?>
        <a href="?kategori=<?= $kategori_filter ?>&page=<?= $page + 1 ?>" class="pgl-page-btn">
            <i class="ti ti-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="pgl-empty">
        <i class="ti ti-speakerphone"></i>
        <p>Tidak ada pengumuman untuk kategori ini.</p>
        <a href="?kategori=semua" class="pgl-back-btn">Lihat semua pengumuman</a>
    </div>
    <?php endif; ?>

</div>

<script>
function toggleDropdown(e) {
    e.stopPropagation();
    document.getElementById('dropdownMenu').classList.toggle('show');
}
document.addEventListener('click', () => {
    const m = document.getElementById('dropdownMenu');
    if (m) m.classList.remove('show');
});
</script>

</body>
</html>