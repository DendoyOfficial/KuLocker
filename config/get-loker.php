<<<<<<< HEAD
<?php
/* ============================================================
   KuLocker — api/get-lokers.php
   Endpoint JSON: daftar loker + koordinat untuk peta
   ============================================================ */

header('Content-Type: application/json');
require_once '../config/connection.php';

$result = mysqli_query($conn, "
    SELECT id, kode_loker, lokasi, ukuran, status, latitude, longitude
    FROM lockers
    ORDER BY status ASC, kode_loker ASC
");

$lokers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $lokers[] = [
        'id'         => (int) $row['id'],
        'kode_loker' => $row['kode_loker'],
        'lokasi'     => $row['lokasi'],
        'ukuran'     => $row['ukuran'],
        'status'     => $row['status'],
        'lat'        => $row['latitude']  ? (float) $row['latitude']  : null,
        'lng'        => $row['longitude'] ? (float) $row['longitude'] : null,
    ];
}

=======
<?php
/* ============================================================
   KuLocker — api/get-lokers.php
   Endpoint JSON: daftar loker + koordinat untuk peta
   ============================================================ */

header('Content-Type: application/json');
require_once '../config/connection.php';

$result = mysqli_query($conn, "
    SELECT id, kode_loker, lokasi, ukuran, status, latitude, longitude
    FROM lockers
    ORDER BY status ASC, kode_loker ASC
");

$lokers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $lokers[] = [
        'id'         => (int) $row['id'],
        'kode_loker' => $row['kode_loker'],
        'lokasi'     => $row['lokasi'],
        'ukuran'     => $row['ukuran'],
        'status'     => $row['status'],
        'lat'        => $row['latitude']  ? (float) $row['latitude']  : null,
        'lng'        => $row['longitude'] ? (float) $row['longitude'] : null,
    ];
}

>>>>>>> b4dfe23a4b265e955d212e01d0a28b2948d7227f
echo json_encode($lokers);