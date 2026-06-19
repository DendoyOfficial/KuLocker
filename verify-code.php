<?php
session_start();
require_once 'config/connection.php';

// Jika belum melewati step 1, redirect balik
if (empty($_SESSION['reset_email']) || empty($_SESSION['reset_kode'])) {
    header("Location: forgot-password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gabungkan 6 digit dari input terpisah
    $kode = '';
    for ($i = 1; $i <= 6; $i++) {
        $kode .= $_POST['kode_' . $i] ?? '';
    }

    if (strlen($kode) !== 6 || !ctype_digit($kode)) {
        $error = 'Kode verifikasi harus 6 digit angka.';

    } elseif (time() > $_SESSION['reset_expired_at']) {
        $error = 'Kode sudah kadaluarsa. Silakan kirim ulang.';

    } elseif ($kode !== (string) $_SESSION['reset_kode']) {
        $error = 'Kode verifikasi tidak cocok. Periksa kembali email kamu.';

    } else {
        // Kode benar → tandai verified, redirect ke reset-password
        $_SESSION['reset_verified'] = true;
        header("Location: reset-password.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Verifikasi Kode – KuLocker</title>
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
    <div class="step active">
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

    <div class="icon-ring warning">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="3" width="20" height="14" rx="2"/>
        <line x1="8" y1="21" x2="16" y2="21"/>
        <line x1="12" y1="17" x2="12" y2="21"/>
      </svg>
    </div>

    <h1>Cek email kamu</h1>
    <p class="subtitle">Kami mengirimkan kode 6 digit ke</p>
    <p class="email-label"><?= htmlspecialchars($email) ?></p>

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

    <form method="POST" action="verify-code.php" id="otp-form" novalidate>

      <!-- 6 kotak OTP -->
      <div class="otp-wrap">
        <?php for ($i = 1; $i <= 6; $i++): ?>
          <input
            type="text"
            class="otp-input"
            id="kode_<?= $i ?>"
            name="kode_<?= $i ?>"
            maxlength="1"
            inputmode="numeric"
            pattern="[0-9]"
            autocomplete="off"
            <?= $i === 1 ? 'autofocus' : '' ?>
          />
        <?php endfor; ?>
      </div>

      <p class="otp-hint">Masukkan 6 digit kode yang dikirim ke email kamu</p>

      <!-- Countdown timer 10 menit -->
      <div class="timer-wrap" id="timer-wrap">
        Kode berlaku selama <span id="countdown">09:59</span>
      </div>
      <div style="text-align:center; margin-bottom:20px;">
        <button type="button" class="resend-link" id="resend-btn" onclick="window.location.href='forgot-password.php'">
          Kirim ulang kode
        </button>
      </div>

      <hr class="divider" />

      <button type="submit" class="btn-primary" id="btn-verify" disabled>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        Verifikasi Kode
      </button>

    </form>

    <a href="forgot-password.php" class="back-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      Ganti email
    </a>

  </div>
</div>

<script>
  const inputs    = document.querySelectorAll('.otp-input');
  const btnVerify = document.getElementById('btn-verify');

  // Auto-focus & angka saja
  inputs.forEach((input, idx) => {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/[^0-9]/g, '');
      if (input.value) {
        input.classList.add('filled');
        if (idx < inputs.length - 1) inputs[idx + 1].focus();
      } else {
        input.classList.remove('filled');
      }
      checkAllFilled();
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && idx > 0) {
        inputs[idx - 1].focus();
        inputs[idx - 1].value = '';
        inputs[idx - 1].classList.remove('filled');
        checkAllFilled();
      }
    });

    input.addEventListener('keypress', (e) => {
      if (!/[0-9]/.test(e.key)) e.preventDefault();
    });
  });

  // Paste langsung isi semua kotak
  inputs[0].addEventListener('paste', (e) => {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData)
      .getData('text').replace(/[^0-9]/g, '').slice(0, 6);
    pasted.split('').forEach((char, i) => {
      if (inputs[i]) {
        inputs[i].value = char;
        inputs[i].classList.add('filled');
      }
    });
    const lastIdx = Math.min(pasted.length, inputs.length - 1);
    inputs[lastIdx].focus();
    checkAllFilled();
  });

  function checkAllFilled() {
    btnVerify.disabled = ![...inputs].every(i => i.value !== '');
  }

  // Countdown 10 menit
  let totalSeconds  = 10 * 60 - 1;
  const countdownEl = document.getElementById('countdown');
  const timerWrap   = document.getElementById('timer-wrap');
  const resendBtn   = document.getElementById('resend-btn');

  const timer = setInterval(() => {
    const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
    const s = String(totalSeconds % 60).padStart(2, '0');
    countdownEl.textContent = `${m}:${s}`;

    if (totalSeconds <= 60) countdownEl.style.color = '#e74c3c';

    if (totalSeconds <= 0) {
      clearInterval(timer);
      timerWrap.innerHTML = '<span style="color:#e74c3c;">Kode sudah kadaluarsa.</span>';
      resendBtn.style.display = 'inline';
      btnVerify.disabled = true;
      inputs.forEach(i => i.classList.add('error'));
    }

    totalSeconds--;
  }, 1000);
</script>

</body>
</html>