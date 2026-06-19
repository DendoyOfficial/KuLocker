<<<<<<< HEAD
<?php
session_start();
require_once 'config/connection.php';

// Jika belum verifikasi kode, redirect balik
if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

$email   = $_SESSION['reset_email'];
$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password     = $_POST['new_password']     ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($new_password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $error = 'Password harus mengandung angka.';
    } elseif (!preg_match('/[^A-Za-z0-9]/', $new_password)) {
        $error = 'Password harus mengandung simbol.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $email_db = mysqli_real_escape_string($conn, $email);

        $sql = "UPDATE users SET password='$hashed' WHERE email='$email_db'";

        if (mysqli_query($conn, $sql)) {
            // Bersihkan semua session reset
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_kode']);
            unset($_SESSION['reset_expired_at']);
            unset($_SESSION['reset_verified']);

            $success = true;
        } else {
            $error = 'Gagal memperbarui password. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password – KuLocker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/reset-password.css" />
</head>
<body>

  <div class="page-wrap">

    <div class="brand">
      <div class="brand-name">Ku<span>Locker</span></div>
    </div>

    <!-- Steps -->
    <div class="steps">
      <div class="step done">
        <div class="step-dot">✓</div>
        Email
      </div>
      <div class="step-line"></div>
      <div class="step done">
        <div class="step-dot">✓</div>
        Kode
      </div>
      <div class="step-line"></div>
      <div class="step <?= $success ? 'done' : 'active' ?>">
        <div class="step-dot"><?= $success ? '✓' : '3' ?></div>
        Password Baru
      </div>
    </div>

    <div class="card">

      <?php if ($success): ?>

        <!-- STATE: SUKSES -->
        <div class="icon-ring success">
          <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <h1>Password berhasil diubah!</h1>
        <p class="subtitle">Gunakan password baru kamu untuk masuk ke akun KuLocker.</p>
        <a href="sign-in.php" class="btn-primary">
          Lanjut ke Sign In
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </a>

      <?php else: ?>

        <!-- STATE: FORM -->
        <div class="icon-ring warning">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
        </div>

        <h1>Buat password baru</h1>
        <p class="subtitle">Password baru harus berbeda dari password yang pernah digunakan sebelumnya.</p>

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

        <form method="POST" action="reset-password.php" novalidate>

          <div class="field">
            <label for="new_password">Password baru</label>
            <div class="input-wrap">
              <input type="password" id="new_password" name="new_password"
                     placeholder="Masukkan password baru" autocomplete="new-password" required />
              <button type="button" class="toggle-eye" onclick="toggleVis('new_password', this)">
                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <div class="strength-bar">
              <span id="s1"></span><span id="s2"></span>
              <span id="s3"></span><span id="s4"></span>
            </div>
            <div class="strength-label" id="strength-label"></div>
            <ul class="req-list">
              <li class="req" id="r-len"><span class="req-dot"></span> Minimal 8 karakter</li>
              <li class="req" id="r-num"><span class="req-dot"></span> Mengandung angka</li>
              <li class="req" id="r-sym"><span class="req-dot"></span> Mengandung simbol (!@#$...)</li>
            </ul>
          </div>

          <div class="field">
            <label for="confirm_password">Konfirmasi password baru</label>
            <div class="input-wrap">
              <input type="password" id="confirm_password" name="confirm_password"
                     placeholder="Ulangi password baru" autocomplete="new-password" required />
              <button type="button" class="toggle-eye" onclick="toggleVis('confirm_password', this)">
                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <p class="err-msg" id="err-match">Password tidak cocok.</p>
          </div>

          <hr class="divider" />

          <button type="submit" class="btn-primary" id="btn-submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Simpan password baru
          </button>

        </form>

        <a href="sign-in.php" class="back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
          Kembali ke Sign In
        </a>

      <?php endif; ?>

    </div>
  </div>

  <script src="js/reset-password.js"></script>
</body>
=======
<?php
session_start();
require_once 'config/connection.php';

// Jika belum verifikasi kode, redirect balik
if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

$email   = $_SESSION['reset_email'];
$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password     = $_POST['new_password']     ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($new_password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $error = 'Password harus mengandung angka.';
    } elseif (!preg_match('/[^A-Za-z0-9]/', $new_password)) {
        $error = 'Password harus mengandung simbol.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $email_db = mysqli_real_escape_string($conn, $email);

        $sql = "UPDATE users SET password='$hashed' WHERE email='$email_db'";

        if (mysqli_query($conn, $sql)) {
            // Bersihkan semua session reset
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_kode']);
            unset($_SESSION['reset_expired_at']);
            unset($_SESSION['reset_verified']);

            $success = true;
        } else {
            $error = 'Gagal memperbarui password. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password – KuLocker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/reset-password.css" />
</head>
<body>

  <div class="page-wrap">

    <div class="brand">
      <div class="brand-name">Ku<span>Locker</span></div>
    </div>

    <!-- Steps -->
    <div class="steps">
      <div class="step done">
        <div class="step-dot">✓</div>
        Email
      </div>
      <div class="step-line"></div>
      <div class="step done">
        <div class="step-dot">✓</div>
        Kode
      </div>
      <div class="step-line"></div>
      <div class="step <?= $success ? 'done' : 'active' ?>">
        <div class="step-dot"><?= $success ? '✓' : '3' ?></div>
        Password Baru
      </div>
    </div>

    <div class="card">

      <?php if ($success): ?>

        <!-- STATE: SUKSES -->
        <div class="icon-ring success">
          <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <h1>Password berhasil diubah!</h1>
        <p class="subtitle">Gunakan password baru kamu untuk masuk ke akun KuLocker.</p>
        <a href="sign-in.php" class="btn-primary">
          Lanjut ke Sign In
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </a>

      <?php else: ?>

        <!-- STATE: FORM -->
        <div class="icon-ring warning">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
        </div>

        <h1>Buat password baru</h1>
        <p class="subtitle">Password baru harus berbeda dari password yang pernah digunakan sebelumnya.</p>

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

        <form method="POST" action="reset-password.php" novalidate>

          <div class="field">
            <label for="new_password">Password baru</label>
            <div class="input-wrap">
              <input type="password" id="new_password" name="new_password"
                     placeholder="Masukkan password baru" autocomplete="new-password" required />
              <button type="button" class="toggle-eye" onclick="toggleVis('new_password', this)">
                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <div class="strength-bar">
              <span id="s1"></span><span id="s2"></span>
              <span id="s3"></span><span id="s4"></span>
            </div>
            <div class="strength-label" id="strength-label"></div>
            <ul class="req-list">
              <li class="req" id="r-len"><span class="req-dot"></span> Minimal 8 karakter</li>
              <li class="req" id="r-num"><span class="req-dot"></span> Mengandung angka</li>
              <li class="req" id="r-sym"><span class="req-dot"></span> Mengandung simbol (!@#$...)</li>
            </ul>
          </div>

          <div class="field">
            <label for="confirm_password">Konfirmasi password baru</label>
            <div class="input-wrap">
              <input type="password" id="confirm_password" name="confirm_password"
                     placeholder="Ulangi password baru" autocomplete="new-password" required />
              <button type="button" class="toggle-eye" onclick="toggleVis('confirm_password', this)">
                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <p class="err-msg" id="err-match">Password tidak cocok.</p>
          </div>

          <hr class="divider" />

          <button type="submit" class="btn-primary" id="btn-submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Simpan password baru
          </button>

        </form>

        <a href="sign-in.php" class="back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
          Kembali ke Sign In
        </a>

      <?php endif; ?>

    </div>
  </div>

  <script src="js/reset-password.js"></script>
</body>
>>>>>>> b4dfe23a4b265e955d212e01d0a28b2948d7227f
</html>