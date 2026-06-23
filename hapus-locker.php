<?php
// Panggil file konfigurasi dan keamanan Anda
require_once "config/auth.php";
require_once "config/connection.php";

// Proteksi level admin
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: sign-in.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Soft-delete: tandai sebagai terhapus
    $query = "UPDATE lockers SET is_deleted = 1 WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header("Location: admin.php?page=locker&msg=hapus_berhasil");
    } else {
        header("Location: admin.php?page=locker&msg=hapus_gagal");
    }
} else {
    header("Location: admin.php?page=locker");
}
exit;
?>