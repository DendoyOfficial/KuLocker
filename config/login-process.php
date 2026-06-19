<?php
session_start();

// Catatan: Jika file 'connection.php' berada di luar folder 'config',
// ubah menjadi: require '../connection.php';
require 'connection.php';

if (isset($_POST['signin'])) {

    // 1. Ambil data dari form dan amankan dari SQL Injection
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $password = $_POST['password'];

    // 2. Query mencari user berdasarkan NIM
    $query = mysqli_query($conn, "SELECT * FROM users WHERE nim = '$nim' LIMIT 1");

    if ($nim === 'admin' && $password === 'admin') {
        // Simpan data admin ke Session 'user'
        $_SESSION['user'] = [
            'id'    => 'admin',
            'nama'  => 'Administrator Utama', 
            'email' => 'admin@domain.com',  
            'nim'   => 'admin',
            'no_hp' => '-'
        ];

        // Pindahkan ke halaman dashboard admin
        header("Location: ../dashboard-admin.php");
        exit;
    }
    
    // 3. Cek apakah NIM terdaftar
    if (mysqli_num_rows($query) > 0) {
        
        $user = mysqli_fetch_assoc($query);

        // 4. Verifikasi password (Menggunakan standard password_hash Bcrypt)
        if (password_verify($password, $user['password'])) {
            
            // JIKA COCOK -> Simpan data login ke Session 'user'
            $_SESSION['user'] = [
                'id'        => $user['id'],
                'nama' => $user['nama'], 
                'email'  => $user['email'],  
                'nim'       => $user['nim'],
                'no_hp' => $user['no_hp']
            ];

            // Pindahkan ke halaman dashboard utama
            header("Location: ../dashboard-utama.php");
            exit;

        } else {
            // JIKA PASSWORD SALAH
            $_SESSION['error'] = "Password yang Anda masukkan salah!";
            header("Location: ../sign-in.php");
            exit;
        }

    } else {
        // JIKA NIM TIDAK DITEMUKAN
        $_SESSION['error'] = "NIM tidak ditemukan atau belum terdaftar!";
        header("Location: ../sign-in.php");
        exit;
    }

} else {
    // JIKA DIAKSES LANGSUNG TANPA KLIK TOMBOL SIGN IN
    $_SESSION['error'] = "Silakan masuk menggunakan form login yang tersedia.";
    header("Location: ../sign-in.php");
    exit;
}
?>