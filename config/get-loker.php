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
    WHERE (is_deleted = 0 OR is_deleted IS NULL)
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

echo json_encode($lokers);