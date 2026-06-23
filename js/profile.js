const $ = (id) => document.getElementById(id);

const setErr = (id, msg) => {
  let e = $("err-" + id);
  if (e) {
    e.style.color = "#dc2626";
    e.textContent = msg;
  }
};

const clearErr = (id) => {
  let e = $(id);
  if (e) e.textContent = "";
};

const setMode = (edit) => {
  $("viewMode").classList.toggle("d-none", edit);
  $("editMode").classList.toggle("d-none", !edit);
  $("btnEdit").classList.toggle("d-none", edit);
  $("btnCancel").classList.toggle("d-none", !edit);
  $("avatarBtn").classList.toggle("d-none", !edit);
};

const switchTab = (t) => {
  ["profil", "keamanan"].forEach((id) => {
    $("panel" + (id[0].toUpperCase() + id.slice(1))).classList.toggle(
      "d-none",
      t !== id,
    );
    $("tab" + (id[0].toUpperCase() + id.slice(1))).classList.toggle(
      "active",
      t === id,
    );
  });
  if (t === "keamanan") setMode(false);
  $("btnEdit").classList.toggle("d-none", t === "keamanan");
};

const previewFoto = (inp) => {
  const f = inp.files[0];
  if (!f) return;
  if (
    f.size > 2e6 ||
    !["image/jpeg", "image/png", "image/gif", "image/webp"].includes(f.type)
  ) {
    alert(f.size > 2e6 ? "Maks 2 MB" : "Format tak didukung");
    inp.value = "";
    return;
  }
  const r = new FileReader();
  r.onload = (e) => {
    $("avatarImg").src = e.target.result;
    $("avatarImg").style.display = "block";
    $("avatarIni").style.display = "none";
    $("fotoHintSide").classList.remove("d-none");
    $("fotoHint").textContent = `✓ ${f.name} (${Math.round(f.size / 1024)} KB)`;
  };
  r.readAsDataURL(f);
};

const togglePwd = (id, btn) => {
  const el = $(id),
    show = el.type === "password";
  el.type = show ? "text" : "password";
  btn.querySelector("i").className = "ti ti-" + (show ? "eye-off" : "eye");
};

const kekuatan = () => {
  const v = $("pwd-baru").value,
    wrp = $("kekuatanWrap");
  if (!v) return (wrp.style.display = "none");
  wrp.style.display = "block";

  let s =
    (v.length >= 8) +
    (v.length >= 12) +
    /[A-Z]/.test(v) +
    /[0-9]/.test(v) +
    /[^A-Za-z0-9]/.test(v);
  const [lbl, col] = [
    ["", "Sangat Lemah", "Lemah", "Cukup", "Kuat", "Sangat Kuat"][s],
    ["", "#ef4444", "#f97316", "#eab308", "#22c55e", "#16a34a"][s],
  ];

  $("kekuatanBar").style.cssText =
    `height:100%;border-radius:3px;transition:all .3s;width:${s * 20}%;background:${col}`;
  $("kekuatanLabel").textContent = lbl;
  $("kekuatanLabel").style.color = col;
};

const cekKonfirm = () => {
  const b = $("pwd-baru").value,
    k = $("pwd-konfirm").value,
    el = $("err-konfirm");
  if (!k) return (el.textContent = "");
  el.style.color = b === k ? "#16a34a" : "#dc2626";
  el.textContent = b === k ? "✓ Password cocok" : "Password tidak cocok";
};

const validasiPwd = () => {
  let ok = true,
    l = $("pwd-lama").value.trim(),
    b = $("pwd-baru").value,
    k = $("pwd-konfirm").value;
    
  if (!l) {
    setErr("lama", "Wajib diisi.");
    ok = false;
  }
  if (!b) {
    setErr("baru", "Wajib diisi.");
    ok = false;
  } else if (b.length < 8) {
    setErr("baru", "Minimal 8 karakter.");
    ok = false;
  } else if (b === l) {
    setErr("baru", "Sama dengan password lama.");
    ok = false;
  }
  if (b && b !== k) {
    setErr("konfirm", "Konfirmasi tak cocok.");
    ok = false;
  }
  return ok;
};

const resetPwd = () => {
  ["pwd-lama", "pwd-baru", "pwd-konfirm"].forEach((id) => ($(id).value = ""));
  ["err-lama", "err-baru", "err-konfirm"].forEach((id) => clearErr(id));
  $("kekuatanWrap").style.display = "none";
};