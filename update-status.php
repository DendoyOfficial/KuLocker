<?php
$koneksi = mysqli_connect("localhost", "root", "", "kulocker");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    // Menggunakan trim agar tidak ada spasi yang mengganggu
    $status = mysqli_real_escape_string($koneksi, trim($_GET['status']));

    $query = "UPDATE lockers SET status = '$status' WHERE id = $id";
    
    // Tambahkan header agar respon bersih
    header('Content-Type: text/plain');
    
    if (mysqli_query($koneksi, $query)) {
        echo "success";
    } else {
        echo "error";
    }
}
mysqli_close($koneksi);
?>