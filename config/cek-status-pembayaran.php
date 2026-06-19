<<<<<<< HEAD
<?php
// Karena berada di dalam folder 'config', langsung panggil connection.php
require_once 'connection.php';

header('Content-Type: application/json');

if (isset($_GET['invoice'])) {
    $invoice = $_GET['invoice'];

    // Ambil status dari tabel pemesanan berdasarkan nomor invoice
    $query = "SELECT status FROM pemesanan WHERE no_invoice = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $invoice);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        echo json_encode(['status' => $data['status']]);
    } else {
        echo json_encode(['status' => 'tidak_ditemukan']);
    }
} else {
    echo json_encode(['status' => 'invalid_request']);
}
=======
<?php
// Karena berada di dalam folder 'config', langsung panggil connection.php
require_once 'connection.php';

header('Content-Type: application/json');

if (isset($_GET['invoice'])) {
    $invoice = $_GET['invoice'];

    // Ambil status dari tabel pemesanan berdasarkan nomor invoice
    $query = "SELECT status FROM pemesanan WHERE no_invoice = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $invoice);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        echo json_encode(['status' => $data['status']]);
    } else {
        echo json_encode(['status' => 'tidak_ditemukan']);
    }
} else {
    echo json_encode(['status' => 'invalid_request']);
}
>>>>>>> b4dfe23a4b265e955d212e01d0a28b2948d7227f
exit;