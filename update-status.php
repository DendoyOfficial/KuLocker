<?php
// Panggil file konfigurasi dan keamanan yang sudah Anda buat
require_once "config/auth.php";       // Menjamin hanya session valid yang bisa masuk
require_once "config/connection.php"; // Menggunakan koneksi database yang sudah ada ($koneksi)

// Pastikan hanya admin yang bisa mengakses file ini
if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);

    // Menjalankan query update menggunakan variabel $conn dari connection.php
    $query = "UPDATE lockers SET status = '$status' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        http_response_code(200); 
    } else {
        http_response_code(500); 
    }
} else {
    http_response_code(400); 
}

?>