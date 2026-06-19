<<<<<<< HEAD
<?php
require 'config/auth.php'; 
require_once 'config/connection.php';

// 1. Tangkap parameter lokasi dari URL
if (!isset($_GET['lokasi']) || empty($_GET['lokasi'])) {
    header("Location: dashboard-utama.php"); 
    exit;
}

$lokasi_terpilih = $_GET['lokasi'];

// 2. Ambil semua data loker di lokasi tersebut dari database
$lockers_list = [];
$query = "SELECT id, kode_loker, ukuran, status FROM lockers WHERE lokasi = ? ORDER BY kode_loker ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $lokasi_terpilih);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $lockers_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Loker - KuLocker</title>
    <link rel="stylesheet" href="css/locker-selection.css">
</head>
<body class="app-body">

    <div class="locker-container">
        
        <div class="app-header">
            <button class="btn-back" onclick="window.location.href='dashboard-utama.php'">&larr;</button>
            <div class="header-text">
                <h1 class="header-title">Locker Selection</h1>
                <p class="header-subtitle"><?= htmlspecialchars($lokasi_terpilih) ?></p>
            </div>
        </div>

        <div class="status-legend">
            <div class="legend-item">
                <div class="legend-color color-available"></div> <span>Tersedia</span>
            </div>
            <div class="legend-item">
                <div class="legend-color color-unavailable"></div> <span>Terpakai / Rusak</span>
            </div>
            <div class="legend-item">
                <div class="legend-color color-selected"></div> <span>Pilihan Kamu</span>
            </div>
        </div>

        <div class="selection-layout">
            
            <div class="grid-area">
                <div class="locker-grid">
                    
                    <?php if (empty($lockers_list)): ?>
                        <p class="empty-notice">Belum ada loker yang terdaftar di lokasi ini.</p>
                    <?php else: ?>
                        <?php 
                        $counter = 0;
                        foreach ($lockers_list as $loker): 
                            $counter++;
                            
                            if ($loker['status'] == 'tersedia') {
                                $btn_class = "btn-available";
                                $disabled = "";
                            } else {
                                $btn_class = "btn-unavailable-seat";
                                $disabled = "disabled";
                            }
                        ?>
                            <button type="button" 
                                    data-db-id="<?= $loker['id'] ?>" 
                                    data-kode="<?= htmlspecialchars($loker['kode_loker']) ?>" 
                                    data-price="5000" 
                                    class="locker-btn <?= $btn_class ?>" <?= $disabled ?>>
                                <?= htmlspecialchars($loker['kode_loker']) ?>
                            </button>

                            <?php 
                            // Memberi sekat jalan tengah grid (setelah kolom ke-2)
                            if ($counter % 4 == 2) {
                                echo '<div class="grid-gap"></div>';
                            }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                </div>

                <div class="entrance-label">
                    Pintu Masuk Area Loker
                </div>
            </div>

            <div class="sidebar-right">
                <h3 class="section-title">Konfigurasi Sewa</h3>

                <div id="duration-section" class="duration-section hidden">
                    <div class="duration-flex">
                        <div>
                            <p class="duration-label">Durasi Sewa</p>
                            <p class="selected-title" id="selected-locker-title">Loker -</p>
                        </div>
                        <div class="counter-box">
                            <button type="button" id="btn-minus" class="counter-btn">-</button>
                            <span id="hours-display" class="hours-display">1 </span>
                            <p style="padding-right: 10px">Jam</p>
                            <button type="button" id="btn-plus" class="counter-btn">+</button>
                        </div>
                    </div>
                    <p class="price-note">*Tarif sewa: Rp5.000 / jam</p>
                    <div class="divider"></div>
                </div>

                <form action="order-summary.php" method="POST">
                    <input type="hidden" name="locker_id" id="form-locker-id" value="">
                    <input type="hidden" name="durasi_jam" id="form-durasi" value="1">

                    <div class="sidebar-action">
                        <div class="total-block">
                            <p class="total-label">TOTAL PRICE</p>
                            <p id="total-price-display" class="total-price-empty">Rp0</p>
                        </div>
                        
                        <button type="submit" id="btn-continue" class="btn-continue-disabled" disabled>
                            CONTINUE
                        </button>
                    </div>
                </form>
            </div>

        </div> </div> <script src="js/locker-selection.js"></script>
</body>
=======
<?php
require 'config/auth.php'; 
require_once 'config/connection.php';

// 1. Tangkap parameter lokasi dari URL
if (!isset($_GET['lokasi']) || empty($_GET['lokasi'])) {
    header("Location: dashboard-utama.php"); 
    exit;
}

$lokasi_terpilih = $_GET['lokasi'];

// 2. Ambil semua data loker di lokasi tersebut dari database
$lockers_list = [];
$query = "SELECT id, kode_loker, ukuran, status FROM lockers WHERE lokasi = ? ORDER BY kode_loker ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $lokasi_terpilih);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $lockers_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Loker - KuLocker</title>
    <link rel="stylesheet" href="css/locker-selection.css">
</head>
<body class="app-body">

    <div class="locker-container">
        
        <div class="app-header">
            <button class="btn-back" onclick="window.location.href='dashboard-utama.php'">&larr;</button>
            <div class="header-text">
                <h1 class="header-title">Locker Selection</h1>
                <p class="header-subtitle"><?= htmlspecialchars($lokasi_terpilih) ?></p>
            </div>
        </div>

        <div class="status-legend">
            <div class="legend-item">
                <div class="legend-color color-available"></div> <span>Tersedia</span>
            </div>
            <div class="legend-item">
                <div class="legend-color color-unavailable"></div> <span>Terpakai / Rusak</span>
            </div>
            <div class="legend-item">
                <div class="legend-color color-selected"></div> <span>Pilihan Kamu</span>
            </div>
        </div>

        <div class="selection-layout">
            
            <div class="grid-area">
                <div class="locker-grid">
                    
                    <?php if (empty($lockers_list)): ?>
                        <p class="empty-notice">Belum ada loker yang terdaftar di lokasi ini.</p>
                    <?php else: ?>
                        <?php 
                        $counter = 0;
                        foreach ($lockers_list as $loker): 
                            $counter++;
                            
                            if ($loker['status'] == 'tersedia') {
                                $btn_class = "btn-available";
                                $disabled = "";
                            } else {
                                $btn_class = "btn-unavailable-seat";
                                $disabled = "disabled";
                            }
                        ?>
                            <button type="button" 
                                    data-db-id="<?= $loker['id'] ?>" 
                                    data-kode="<?= htmlspecialchars($loker['kode_loker']) ?>" 
                                    data-price="5000" 
                                    class="locker-btn <?= $btn_class ?>" <?= $disabled ?>>
                                <?= htmlspecialchars($loker['kode_loker']) ?>
                            </button>

                            <?php 
                            // Memberi sekat jalan tengah grid (setelah kolom ke-2)
                            if ($counter % 4 == 2) {
                                echo '<div class="grid-gap"></div>';
                            }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                </div>

                <div class="entrance-label">
                    Pintu Masuk Area Loker
                </div>
            </div>

            <div class="sidebar-right">
                <h3 class="section-title">Konfigurasi Sewa</h3>

                <div id="duration-section" class="duration-section hidden">
                    <div class="duration-flex">
                        <div>
                            <p class="duration-label">Durasi Sewa</p>
                            <p class="selected-title" id="selected-locker-title">Loker -</p>
                        </div>
                        <div class="counter-box">
                            <button type="button" id="btn-minus" class="counter-btn">-</button>
                            <span id="hours-display" class="hours-display">1 </span>
                            <p style="padding-right: 10px">Jam</p>
                            <button type="button" id="btn-plus" class="counter-btn">+</button>
                        </div>
                    </div>
                    <p class="price-note">*Tarif sewa: Rp5.000 / jam</p>
                    <div class="divider"></div>
                </div>

                <form action="order-summary.php" method="POST">
                    <input type="hidden" name="locker_id" id="form-locker-id" value="">
                    <input type="hidden" name="durasi_jam" id="form-durasi" value="1">

                    <div class="sidebar-action">
                        <div class="total-block">
                            <p class="total-label">TOTAL PRICE</p>
                            <p id="total-price-display" class="total-price-empty">Rp0</p>
                        </div>
                        
                        <button type="submit" id="btn-continue" class="btn-continue-disabled" disabled>
                            CONTINUE
                        </button>
                    </div>
                </form>
            </div>

        </div> </div> <script src="js/locker-selection.js"></script>
</body>
>>>>>>> b4dfe23a4b265e955d212e01d0a28b2948d7227f
</html>