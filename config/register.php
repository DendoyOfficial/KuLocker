<<<<<<< HEAD
<?php 
session_start();
require_once 'connection.php'; // koneksi sudah ditangani di sini

if (isset($_POST['submit'])) {

    // Ambil & sanitasi input
    $nama             = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $nim              = mysqli_real_escape_string($conn, trim($_POST['nim']));
    $email            = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validasi password kosong
    if (empty($password) || empty($confirm_password)) {
        echo "<script>
                alert('Kata sandi tidak boleh kosong!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 2. Validasi minimal 8 karakter
    if (strlen($password) < 8) {
        echo "<script>
                alert('Kata sandi minimal 8 karakter!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 3. Validasi konfirmasi password
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Maaf, konfirmasi kata sandi tidak cocok!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 4. Cek email atau NIM sudah terdaftar
    $cek_akun  = "SELECT * FROM users WHERE email='$email' OR nim='$nim'";
    $hasil_cek = mysqli_query($conn, $cek_akun);

    if (mysqli_num_rows($hasil_cek) > 0) {
        echo "<script>
                alert('Maaf, NIM atau Email ini sudah terdaftar!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 5. Semua valid → hash password & simpan ke DB
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (nama, nim, email, password) 
            VALUES ('$nama', '$nim', '$email', '$password_hashed')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['nama']  = $nama;
        $_SESSION['email'] = $email;

        header("Location: ../konfirmasi.php?register=sukses");
        exit();
    } else {
        echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
    }
}
=======
<?php 
session_start();
require_once 'connection.php'; // koneksi sudah ditangani di sini

if (isset($_POST['submit'])) {

    // Ambil & sanitasi input
    $nama             = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $nim              = mysqli_real_escape_string($conn, trim($_POST['nim']));
    $email            = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validasi password kosong
    if (empty($password) || empty($confirm_password)) {
        echo "<script>
                alert('Kata sandi tidak boleh kosong!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 2. Validasi minimal 8 karakter
    if (strlen($password) < 8) {
        echo "<script>
                alert('Kata sandi minimal 8 karakter!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 3. Validasi konfirmasi password
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Maaf, konfirmasi kata sandi tidak cocok!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 4. Cek email atau NIM sudah terdaftar
    $cek_akun  = "SELECT * FROM users WHERE email='$email' OR nim='$nim'";
    $hasil_cek = mysqli_query($conn, $cek_akun);

    if (mysqli_num_rows($hasil_cek) > 0) {
        echo "<script>
                alert('Maaf, NIM atau Email ini sudah terdaftar!');
                window.location.href = '../sign-up.php';
              </script>";
        exit();
    }

    // 5. Semua valid → hash password & simpan ke DB
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (nama, nim, email, password) 
            VALUES ('$nama', '$nim', '$email', '$password_hashed')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['nama']  = $nama;
        $_SESSION['email'] = $email;

        header("Location: ../konfirmasi.php?register=sukses");
        exit();
    } else {
        echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
    }
}
>>>>>>> b4dfe23a4b265e955d212e01d0a28b2948d7227f
?>