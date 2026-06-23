<?php
session_start();

// Catatan: Jika file 'connection.php' berada di luar folder 'config',
// ubah menjadi: require '../connection.php';
require 'connection.php';

if (isset($_POST['signin']))

if (password_verify($password, $user['password'])) {
            
            // JIKA COCOK -> Simpan data login ke Session 'user'
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'nama'  => $user['nama'], 
                'email' => $user['email'],  
                'nim'   => $user['nim'],
                'no_hp' => $user['no_hp'],
                'role'  => $user['role'] // 🟢 TAMBAHAN: Ambil data role ('admin' atau 'mahasiswa') dari database
            ];

            // 6. SORTIR HALAMAN BERDASARKAN ROLE
            if ($_SESSION['user']['role'] === 'admin') {
                header("Location: ../admin.php");
            } else {
                header("Location: ../dashboard-utama.php");
            }
            exit;

        } else {
            // JIKA PASSWORD SALAH
            $_SESSION['error'] = "Password yang Anda masukkan salah!";
            header("Location: ../sign-in.php");
            exit;
        }
?>