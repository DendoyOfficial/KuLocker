(function () {
  const newPass     = document.getElementById('new_password');
  const confirmPass = document.getElementById('confirm_password');
  const btnSubmit   = document.getElementById('btn-submit');
  const errMatch    = document.getElementById('err-match');
  const strengthLbl = document.getElementById('strength-label');
  const bars        = [1,2,3,4].map(i => document.getElementById('s' + i));

  const STR_COLORS = { 1:'#E24B4A', 2:'#EF9F27', 3:'#D4AF37', 4:'#639922' };
  const STR_LABELS = { 0:'', 1:'Lemah', 2:'Cukup', 3:'Kuat', 4:'Sangat kuat' };

  function getScore(v) {
    if (!v) return 0;
    let s = 0;
    if (v.length >= 8)          s++;
    if (/[A-Z]/.test(v))        s++;
    if (/[0-9]/.test(v))        s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    return Math.max(s, 1);
  }

  function setReq(id, ok) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('ok', ok);
  }

  function updateStrength() {
    const v     = newPass.value;
    const score = getScore(v);

    bars.forEach((bar, i) => {
      bar.style.background = (v && i < score) ? STR_COLORS[score] : '';
    });

    strengthLbl.textContent = STR_LABELS[v ? score : 0];
    strengthLbl.style.color = v ? STR_COLORS[score] : '';

    setReq('r-len', v.length >= 8);
    setReq('r-num', /[0-9]/.test(v));
    setReq('r-sym', /[^A-Za-z0-9]/.test(v));
  }

  function checkMatch() {
    const np = newPass.value;
    const cp = confirmPass.value;
    // Hanya tampilkan error jika user sudah mulai ketik di kolom konfirmasi
    const mismatch = cp.length > 0 && np !== cp;

    errMatch.classList.toggle('show', mismatch);
    confirmPass.classList.toggle('error', mismatch);
  }

  function canSubmit() {
    const v      = newPass.value;
    const lenOk  = v.length >= 8;
    const numOk  = /[0-9]/.test(v);
    const symOk  = /[^A-Za-z0-9]/.test(v);
    const matchOk = confirmPass.value.length > 0 && v === confirmPass.value;
    return lenOk && numOk && symOk && matchOk;
  }

  function toggleVis(id, btn) {
    const inp    = document.getElementById(id);
    const isPass = inp.type === 'password';
    inp.type     = isPass ? 'text' : 'password';

    const svg = btn.querySelector('svg');
    if (isPass) {
      svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
      svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
  }

  // Tambahkan CSS class .show ke err-msg lewat JS, bukan display langsung
  // Pastikan di reset-password.css ada:
  // .err-msg { display: none; }
  // .err-msg.show { display: block; }

  if (newPass)     newPass.addEventListener('input',     () => { updateStrength(); checkMatch(); btnSubmit.disabled = !canSubmit(); });
  if (confirmPass) confirmPass.addEventListener('input', () => { checkMatch(); btnSubmit.disabled = !canSubmit(); });

  // Disable tombol submit saat pertama load
  if (btnSubmit) btnSubmit.disabled = true;

  window.toggleVis = toggleVis;
})();