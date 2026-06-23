/* ============================================================
   KuLocker — verify-otp-hp.js
   OTP input behaviour + countdown timer
   ============================================================ */

const inputs    = document.querySelectorAll('.otp-input');
const btnVerify = document.getElementById('btn-verify');

// ── OTP Input behaviour ──────────────────────────────────────

inputs.forEach((input, idx) => {

  // Hanya terima angka, auto-focus ke kotak berikutnya
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

  // Backspace → balik ke kotak sebelumnya
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && !input.value && idx > 0) {
      inputs[idx - 1].focus();
      inputs[idx - 1].value = '';
      inputs[idx - 1].classList.remove('filled');
      checkAllFilled();
    }
  });

  // Blokir huruf & simbol langsung saat keypress
  input.addEventListener('keypress', (e) => {
    if (!/[0-9]/.test(e.key)) e.preventDefault();
  });
});

// Paste — isi semua kotak sekaligus
inputs[0].addEventListener('paste', (e) => {
  e.preventDefault();
  const pasted = (e.clipboardData || window.clipboardData)
    .getData('text')
    .replace(/[^0-9]/g, '')
    .slice(0, 6);

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
  const allFilled = [...inputs].every(i => i.value !== '');
  btnVerify.disabled = !allFilled;
}

// ── Countdown Timer (5 menit) ────────────────────────────────

let totalSeconds  = 5 * 60 - 1;
const countdownEl = document.getElementById('countdown');
const timerWrap   = document.getElementById('timer-wrap');
const resendBtn   = document.getElementById('resend-btn');

const timer = setInterval(() => {
  const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
  const s = String(totalSeconds % 60).padStart(2, '0');
  countdownEl.textContent = `${m}:${s}`;

  // Warna merah saat tinggal 1 menit
  if (totalSeconds <= 60) {
    countdownEl.style.color = '#e74c3c';
  }

  if (totalSeconds <= 0) {
    clearInterval(timer);
    timerWrap.innerHTML = '<span style="color:#e74c3c;">Kode OTP sudah kadaluarsa.</span>';
    resendBtn.style.display = 'inline';
    btnVerify.disabled = true;

    // Tandai semua kotak error
    inputs.forEach(i => i.classList.add('error'));
  }

  totalSeconds--;
}, 1000);

// ── Resend OTP ───────────────────────────────────────────────

function resendOTP() {
  resendBtn.disabled = true;
  resendBtn.textContent = 'Mengirim...';

  fetch('resend-otp.php', { method: 'POST' })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Reset semua kotak
        inputs.forEach(i => {
          i.value = '';
          i.classList.remove('filled', 'error');
        });
        inputs[0].focus();
        btnVerify.disabled = true;

        // Reset timer ke 5 menit
        totalSeconds = 5 * 60 - 1;
        countdownEl.style.color = '#fbc531';
        resendBtn.style.display = 'none';
        resendBtn.disabled = false;
        resendBtn.textContent = 'Kirim ulang kode';

        timerWrap.innerHTML = 'Kode berlaku selama <span id="countdown">04:59</span>';

        alert('Kode OTP baru telah dikirim ke nomor HP kamu.');
      } else {
        alert(data.message || 'Gagal mengirim ulang. Coba beberapa saat lagi.');
        resendBtn.disabled = false;
        resendBtn.textContent = 'Kirim ulang kode';
      }
    })
    .catch(() => {
      alert('Terjadi kesalahan. Periksa koneksi internet kamu.');
      resendBtn.disabled = false;
      resendBtn.textContent = 'Kirim ulang kode';
    });
}