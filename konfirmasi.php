<<<<<<< HEAD
<?php
// Jika tidak ada parameter atau status bukan 'sukses', tolak akses langsung
if (!isset($_GET['register']) || $_GET['register'] !== 'sukses') {
    header("Location: sign-up.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Akun Berhasil Dibuat – KuLocker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/konfirmasi.css" />
</head>
<body>

  <a href="dashboard-utama.php" class="btn-back">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="15 18 9 12 15 6"/>
    </svg>
  </a>

  <div class="page-wrap">
    <div class="card">

      <div class="icon-ring">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>

      <h1>Akun berhasil dibuat!</h1>
      <p class="desc">
        Selamat datang di <strong>KuLocker</strong>! Akun kamu sudah terdaftar dan siap digunakan.
        Lanjutkan dengan sign in untuk mulai memesan locker.
      </p>

      <a href="sign-in.php" class="btn-signin">
        Lanjut ke Sign In
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </a>

    </div>
  </div>

</body>
=======
<?php
// Jika tidak ada parameter atau status bukan 'sukses', tolak akses langsung
if (!isset($_GET['register']) || $_GET['register'] !== 'sukses') {
    header("Location: sign-up.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Akun Berhasil Dibuat – KuLocker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/konfirmasi.css" />
</head>
<body>

  <a href="dashboard-utama.php" class="btn-back">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="15 18 9 12 15 6"/>
    </svg>
  </a>

  <div class="page-wrap">
    <div class="card">

      <div class="icon-ring">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>

      <h1>Akun berhasil dibuat!</h1>
      <p class="desc">
        Selamat datang di <strong>KuLocker</strong>! Akun kamu sudah terdaftar dan siap digunakan.
        Lanjutkan dengan sign in untuk mulai memesan locker.
      </p>

      <a href="sign-in.php" class="btn-signin">
        Lanjut ke Sign In
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </a>

    </div>
  </div>

</body>
>>>>>>> b4dfe23a4b265e955d212e01d0a28b2948d7227f
</html>