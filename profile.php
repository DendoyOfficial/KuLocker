<?php
require 'config/auth.php';
require_once 'config/connection.php';

// 1. Ambil ID dari session 'user'. Pakai ternary buat jaga-jaga kalau bentuknya array/string.
$id_users = is_array($_SESSION['user']) 
    ? ($_SESSION['user']['id'] ?? $_SESSION['user']['id_users'] ?? null) 
    : $_SESSION['user'];

// 2. Proteksi keamanan: Kalau ID kosong, tendang ke sign-in.php (sesuai auth.php)
if (!$id_users) {
    header("Location: sign-in.php");
    exit;
}

// 3. Helper function untuk ambil data profil pake Prepared Statement (Aman & Akurat)
$getProfil = function() use ($conn, $id_users) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id_users);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
};

$h = fn($s) => htmlspecialchars($s ?? '');
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$stay_edit = false;
$tab = $_GET['tab'] ?? 'profil';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $err = [];

    if ($aksi === 'profil') {
        $nama = $_POST['nama']??''; $nim = $_POST['nim']??''; $alamat = $_POST['alamat']??''; 
        $email = $_POST['email']??''; $no_hp = $_POST['no_hp']??'';
        
        if (!$nama) $err[] = "Nama wajib diisi.";
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err[] = "Email tidak valid.";

        $foto = null;
        if (!empty($_FILES['foto_baru']['name'])) {
            $f = $_FILES['foto_baru']; $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            if ($f['error'] !== 0) $err[] = "Gagal unggah.";
            elseif (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) $err[] = "Format tak didukung.";
            elseif ($f['size'] > 2e6) $err[] = "Maks 2MB.";
            else {
                @mkdir($dir = __DIR__."/image/", 0755, true);
                if (move_uploaded_file($f['tmp_name'], $dir.($foto = "u{$id_users}_".uniqid().".$ext"))) {} 
                else { $err[] = "Gagal simpan foto."; $foto = null; }
            }
        }

        if (!$err) {
            $sql = "UPDATE users SET nama=?,nim=?,alamat=?,email=?,no_hp=?" . ($foto?",foto_profil=?":"") . " WHERE id=?";
            $s = mysqli_prepare($conn, $sql);
            $foto ? mysqli_stmt_bind_param($s, "ssssssi", $nama,$nim,$alamat,$email,$no_hp,$foto,$id_users) 
                  : mysqli_stmt_bind_param($s, "sssssi", $nama,$nim,$alamat,$email,$no_hp,$id_users);
            $_SESSION['flash'] = mysqli_stmt_execute($s) ? ['ok'=>1,'msg'=>'Profil diperbarui.'] : ['ok'=>0,'msg'=>'Gagal.'];
            header("Location: ?tab=profil"); exit;
        }
        $stay_edit = true;
    } 
    
    if ($aksi === 'password') {
        $lama = $_POST['pwd_lama'] ?? ''; 
        $baru = $_POST['pwd_baru'] ?? ''; 
        $konf = $_POST['pwd_konfirm'] ?? '';
        $db = $getProfil();
        
        // 1. Validasi kecocokan password lama
        if (!$lama || !password_verify($lama, $db['password'])) {
            $err[] = "Password lama salah.";
        }
        elseif (strlen($baru) < 8) {
            $err[] = "Minimal 8 karakter.";
        }
        elseif ($baru === $lama) {
            $err[] = "Sama dengan lama.";
        }
        elseif ($baru !== $konf) {
            $err[] = "Konfirmasi salah.";
        }

        if (!$err) {
            // Amankan password baru menggunakan BCRYPT hash sebelum disimpan
            $password_terhash = password_hash($baru, PASSWORD_BCRYPT);

            // Gunakan Prepared Statement untuk mengupdate password baru yang sudah di-hash
            $s = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
            mysqli_stmt_bind_param($s, "si", $password_terhash, $id_users);
            
            $_SESSION['flash'] = mysqli_stmt_execute($s) ? ['ok'=>1,'msg'=>'Password berhasil diperbarui (Ter-hash).'] : ['ok'=>0,'msg'=>'Gagal memperbarui password.'];
            header("Location: ?tab=keamanan"); 
            exit;
        }
        $stay_edit = true;
    }
    if ($err) $flash = ['ok'=>0, 'msg'=>implode(' ', $err)];
}

$profil = array_merge($getProfil(), $stay_edit ? $_POST : []);
$inisial = fn($n) => strtoupper(($p=explode(' ',trim($n)))[0][0] . (count($p)>1?end($p)[0]:'')) ?: 'U';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KuLocker — Profil</title>
  <link rel="stylesheet" href="css/profile.css" />
</head>
<body>
<main class="page-wrapper">
  <header class="navbar">
    <div class="brand-with-back">
      <a href="dashboard-utama.php" class="brand-back">
        <i class="ti ti-arrow-left"></i> Kembali
      </a>
    </div>
    <div class="brand"><span class="brand-dot"></span>KuLocker</div>
    <div class="user-pill">Profil Saya</div>
  </header>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['ok'] ? 'success' : 'error' ?>"><?= $h($flash['msg']) ?></div>
  <?php endif; ?>

  <div class="hero-banner">
    <button id="btnEdit" class="btn-edit <?= ($stay_edit || $tab==='keamanan') ? 'd-none':'' ?>" onclick="setMode(true)">
      <i class="ti ti-edit"></i> Edit Profil
    </button>
    <button id="btnCancel" class="btn-cancel <?= $stay_edit ? '':'d-none' ?>" onclick="setMode(false)">
      <i class="ti ti-x"></i> Batal
    </button>
  </div>

  <section class="content-grid">
    <aside class="left-panel">
      <div class="avatar-wrap">
        <div class="avatar-circle">
          <img id="avatarImg" src="<?= $profil['foto_profil'] ? 'image/'.$h($profil['foto_profil']) : '' ?>" 
               <?= !$profil['foto_profil'] ? 'style="display:none"' : '' ?> onerror="this.style.display='none';document.getElementById('avatarIni').style.display='block'" />
          <span id="avatarIni" <?= $profil['foto_profil'] ? 'style="display:none"':'' ?>><?= $h($inisial($profil['nama'] ?? '')) ?></span>
        </div>
        <button class="avatar-edit-btn <?= $stay_edit ? '':'d-none' ?>" id="avatarBtn" onclick="document.getElementById('inputFoto').click()" title="Ganti foto">
          <i class="ti ti-camera"></i>
        </button>
      </div>
      <h2><?= $h($profil['nama'] ?: 'Pengguna') ?></h2>
      <p class="subtitle">Mahasiswa, UNRAM</p>
      <p class="avatar-preview-hint d-none" id="fotoHintSide">📷 Foto baru dipilih</p>
    </aside>

    <article>
      <div class="form-tabs">
        <div class="tab <?= $tab==='profil' ? 'active':'' ?>" id="tabProfil" onclick="switchTab('profil')">Profil</div>
        <div class="tab <?= $tab==='keamanan' ? 'active':'' ?>" id="tabKeamanan" onclick="switchTab('keamanan')">Keamanan</div>
      </div>

      <div id="panelProfil" class="<?= $tab!=='profil' ? 'd-none':'' ?>">
        <div id="viewMode" class="editor-grid <?= $stay_edit ? 'd-none':'' ?>">
          <?php foreach(['Nama Lengkap'=>'nama','NIM'=>'nim','Alamat'=>'alamat','Email'=>'email','No. HP'=>'no_hp'] as $lbl => $k): ?>
            <div class="form-group <?= $k==='alamat'?'full':'' ?>">
              <label><?= $lbl ?></label>
              <p class="text-value"><?= $h($profil[$k] ?: '-') ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <form id="editMode" class="editor-grid is-form <?= $stay_edit ? '':'d-none' ?>" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="aksi" value="profil" />
          <div class="form-group full">
            <label>Foto Profil</label>
            <input type="file" id="inputFoto" name="foto_baru" accept=".jpg,.jpeg,.png,.gif,.webp" style="display:none" onchange="previewFoto(this)" />
            <div style="display:flex;align-items:center;gap:10px">
              <button type="button" class="btn-secondary" style="padding:7px 14px;font-size:.82rem" onclick="document.getElementById('inputFoto').click()">
                <i class="ti ti-upload"></i> Pilih Foto
              </button>
              <span class="field-hint" id="fotoHint">JPG, PNG, GIF, WEBP — maks 2 MB</span>
            </div>
          </div>
          
          <div class="form-group"><label>Nama Lengkap *</label><input type="text" name="nama" required value="<?= $h($profil['nama'] ?? '') ?>" /></div>
          <div class="form-group"><label>NIM</label><input type="text" name="nim" value="<?= $h($profil['nim'] ?? '') ?>" /></div>
          <div class="form-group full"><label>Alamat</label><textarea name="alamat"><?= $h($profil['alamat'] ?? '') ?></textarea></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= $h($profil['email'] ?? '') ?>" /></div>
          <div class="form-group"><label>No. HP</label><input type="text" name="no_hp" value="<?= $h($profil['no_hp'] ?? '') ?>" /></div>

          <div class="button-group">
            <button type="submit" class="btn-save"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
            <button type="button" class="btn-secondary" onclick="setMode(false)">Batal</button>
          </div>
        </form>
      </div>

      <div id="panelKeamanan" class="<?= $tab!=='keamanan' ? 'd-none':'' ?>">
        <form class="editor-grid is-form" method="POST" onsubmit="return validasiPwd()">
          <input type="hidden" name="aksi" value="password" />
          <div class="security-info full"><i class="ti ti-shield-lock"></i> Untuk mengubah kata sandi, mohon isi field di bawah ini.</div>

          <?php 
          $pwdfields = [
              ['Lama', 'pwd-lama', 'current-password', "clearErr('err-lama')"],
              ['Baru', 'pwd-baru', 'new-password', "kekuatan(); cekKonfirm(); clearErr('err-baru')"],
              ['Konfirmasi', 'pwd-konfirm', 'new-password', "cekKonfirm(); clearErr('err-konfirm')"]
          ];
          foreach($pwdfields as $f): ?>
            <div class="form-group <?= $f[0]==='Lama'?'full':'' ?>">
              <label>Password <?= $f[0] ?></label>
              <div class="pwd-group">
                <input type="password" name="<?= str_replace('-','_',$f[1]) ?>" id="<?= $f[1] ?>" autocomplete="<?= $f[2] ?>" oninput="<?= $f[3] ?>" />
                <button type="button" class="pwd-toggle" onclick="togglePwd('<?= $f[1] ?>',this)"><i class="ti ti-eye"></i></button>
              </div>
              <span class="field-error" id="err-<?= strtolower(explode('-',$f[1])[1]) ?>"></span>
            </div>
          <?php endforeach; ?>

          <div class="form-group full" id="kekuatanWrap" style="display:none">
            <label>Kekuatan password</label>
            <div style="height:5px;border-radius:3px;background:#E5E7EB;overflow:hidden;margin-top:3px">
              <div id="kekuatanBar" style="height:100%;border-radius:3px;transition:all .3s;width:0"></div>
            </div>
            <span id="kekuatanLabel" class="field-hint" style="margin-top:3px"></span>
          </div>

          <div class="button-group">
            <button type="submit" class="btn-save"><i class="ti ti-lock"></i> Perbarui Password</button>
            <button type="button" class="btn-secondary" onclick="resetPwd()"><i class="ti ti-refresh"></i> Reset</button>
          </div>
        </form>
      </div>
    </article>
  </section>
</main>

<script src="js/profile.js"></script>
</body>
</html>