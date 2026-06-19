<?php
session_start();
require_once 'config/connection.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));

    if (empty($email)) {
        $error = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Cek apakah email terdaftar
        $cek  = mysqli_query($conn, "SELECT id, nama FROM users WHERE email='$email'");
        $user = mysqli_fetch_assoc($cek);

        if (!$user) {
            $error = 'Email ini tidak terdaftar di KuLocker.';
        } else {
            // Generate kode 6 digit
            $kode = rand(100000, 999999);

            // Simpan ke session
            $_SESSION['reset_email']      = $email;
            $_SESSION['reset_kode']       = $kode;
            $_SESSION['reset_expired_at'] = time() + (10 * 60); // 10 menit

            // Kirim email via PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Konfigurasi SMTP Gmail
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'websitekulocker@gmail.com';     // ← ganti email Gmail kamu
                $mail->Password   = 'btco iwhs uxft zhlg';     // ← App Password Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                // Pengirim & penerima
                $mail->setFrom('websitekulocker@gmail.com', 'KuLocker');
                $mail->addAddress($email, $user['nama']);

                // Konten email
                $mail->isHTML(true);
                $mail->Subject = 'Kode Verifikasi Reset Password KuLocker';
                $mail->Body    = "
                    <div style='font-family: Inter, sans-serif; max-width: 480px; margin: 0 auto; background: #1a1a1a; border-radius: 12px; padding: 32px; color: #fff;'>
                        <h2 style='color: #fbc531; margin-bottom: 8px;'>KuLocker</h2>
                        <p style='color: #888; margin-bottom: 24px;'>Reset Password</p>
                        <p style='margin-bottom: 16px;'>Halo <strong>{$user['nama']}</strong>,</p>
                        <p style='margin-bottom: 24px; color: #ccc;'>Gunakan kode berikut untuk mereset password kamu:</p>
                        <div style='background: #111; border: 1px solid #333; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 24px;'>
                            <span style='font-size: 36px; font-weight: 800; letter-spacing: 12px; color: #fbc531;'>{$kode}</span>
                        </div>
                        <p style='color: #888; font-size: 13px;'>Kode berlaku selama <strong style='color:#fbc531;'>10 menit</strong>. Jangan bagikan ke siapapun.</p>
                        <hr style='border-color: #2a2a2a; margin: 24px 0;'>
                        <p style='color: #555; font-size: 12px;'>Jika kamu tidak meminta reset password, abaikan email ini.</p>
                    </div>
                ";
                $mail->AltBody = "Kode reset password KuLocker kamu: $kode. Berlaku 10 menit.";

                $mail->send();
                $success = true;

            } catch (Exception $e) {
                //$error = 'Gagal mengirim email. Coba beberapa saat lagi.';
                // Uncomment baris berikut untuk debug:
                $error = 'Mailer Error: ' . $mail->ErrorInfo;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lupa Password – KuLocker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/reset-password.css">
</head>
<body>

<div class="page-wrap">

  <!-- Brand -->
  <div class="brand">
    <div class="brand-name">Ku<span>Locker</span></div>
  </div>

  <!-- Steps -->
  <div class="steps">
    <div class="step <?= $success ? 'done' : 'active' ?>">
      <div class="step-dot"><?= $success ? '✓' : '1' ?></div>
      Email
    </div>
    <div class="step-line"></div>
    <div class="step">
      <div class="step-dot">2</div>
      Kode
    </div>
    <div class="step-line"></div>
    <div class="step">
      <div class="step-dot">3</div>
      Password Baru
    </div>
  </div>

  <div class="card">

    <?php if ($success): ?>

      <!-- STATE: SUKSES KIRIM EMAIL -->
      <div class="icon-ring success">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
      </div>

      <h1>Email terkirim!</h1>
      <p class="subtitle">Kami mengirimkan kode verifikasi ke</p>

      <div class="email-badge">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
        <?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?>
      </div>

      <p class="subtitle" style="margin-bottom: 24px;">
        Cek inbox atau folder spam kamu. Kode berlaku selama <strong style="color:#fbc531;">10 menit</strong>.
      </p>

      <a href="verify-code.php" class="btn-primary">
        Masukkan Kode Verifikasi
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </a>

    <?php else: ?>

      <!-- STATE: FORM INPUT EMAIL -->
      <div class="icon-ring warning">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
      </div>

      <h1>Lupa password?</h1>
      <p class="subtitle">Masukkan email yang terdaftar. Kami akan mengirimkan kode verifikasi untuk mereset password kamu.</p>

      <?php if ($error): ?>
        <div class="alert-error">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="forgot-password.php" novalidate>

        <div class="field">
          <label for="email">Alamat Email</label>
          <div class="input-wrap">
            <input
              type="email"
              id="email"
              name="email"
              placeholder="contoh@email.com"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              autocomplete="email"
              required
            />
          </div>
          <p class="hint">Gunakan email yang kamu daftarkan di KuLocker.</p>
        </div>

        <hr class="divider" />

        <button type="submit" class="btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"/>
            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
          </svg>
          Kirim Kode Verifikasi
        </button>

      </form>

    <?php endif; ?>

    <a href="sign-in.php" class="back-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      Kembali ke Sign In
    </a>

  </div>
</div>

</body>
</html>