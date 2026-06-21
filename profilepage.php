<?php
session_start();

// 1. KONEKSI KE DATABASE KULOCKER
$conn = mysqli_connect("localhost", "root", "", "kulocker");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. ID USER SEMENTARA 
$_SESSION['id_user'] = 1; 

// 3. PROSES SIMPAN DATA (JIKA TOMBOL 'SIMPAN PERUBAHAN' DIKLIK)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nama_baru = $_POST['nama'];
    $nim_baru = $_POST['nim'];
    $alamat_baru = $_POST['alamat'];
    $email_baru = $_POST['email'];
    $hp_baru = $_POST['hp'];
    $password_baru = $_POST['password'];

    $query_update = "UPDATE users SET 
                     nama_lengkap = '$nama_baru', 
                     nim = '$nim_baru', 
                     alamat = '$alamat_baru', 
                     email = '$email_baru', 
                     phone = '$hp_baru', 
                     password = '$password_baru' 
                     WHERE id = '{$_SESSION['id_user']}'";
    
    mysqli_query($conn, $query_update);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 4. AMBIL DATA DARI DATABASE UNTUK DITAMPILKAN
$query_tampil = "SELECT * FROM users WHERE id = '{$_SESSION['id_user']}'";
$result = mysqli_query($conn, $query_tampil);

if (mysqli_num_rows($result) > 0) {
    $profil = mysqli_fetch_assoc($result);
} else {

//Dummy buat base biar bisa nyimpen dulu sementara
    $profil = [
        'nama_lengkap' => 'M. Arya', 
        'nim' => '0000011122244456', 
        'alamat' => 'Jl. Majapahit, Mataram', 
        'email' => 'arya@example.com', 
        'phone' => '081234567890', 
        'password' => '********'
    ];
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>KuLocker Profile</title>
    <link rel="stylesheet" href="profilfix.css" />
  </head>
  <body>
    <main class="page-wrapper">
      <header class="navbar">
        <div class="brand">KuLocker</div>
        <div class="user-pill">PROFIL PAGE</div>
      </header>

      <div class="hero-banner">
        <button id="btnEditHero" class="btn-edit" onclick="toggleMode('edit')">
          Edit Profil
        </button>
      </div>

      <section class="content-grid">
        <aside class="left-panel">
          <div class="avatar-circle">
            <img
              src="image/Kulocker.jpg"
              alt="Profile"
              style="
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
              "
            />
          </div>
          
          <!-- Nama di kiri otomatis ikut database -->
          <h2><?= htmlspecialchars($profil['nama_lengkap'] ?? 'Raka Ganteng') ?></h2>
          <p class="subtitle">Mahasiswa, UNRAM</p>
        </aside>

        <article class="right-panel">
          <div class="form-container">
            <div class="form-tabs">
              <span>Profil</span>
              <span>Keamanan</span>
            </div>

            <!-- MODE LIHAT (VIEW MODE) -->
            <div id="viewMode" class="editor-grid">
              <div class="form-group">
                <label>Nama Lengkap</label>
                <p class="text-value"><?= htmlspecialchars($profil['nama_lengkap'] ?? '') ?></p>
              </div>
              <div class="form-group">
                <label>NIM</label>
                <p class="text-value"><?= htmlspecialchars($profil['nim'] ?? '') ?></p>
              </div>
              <div class="form-group">
                <label>Alamat</label>
                <p class="text-value"><?= htmlspecialchars($profil['alamat'] ?? '') ?></p>
              </div>
              <div class="form-group">
                <label>Email</label>
                <p class="text-value"><?= htmlspecialchars($profil['email'] ?? '') ?></p>
              </div>
              <div class="form-group">
                <label>No. HP</label>
                <p class="text-value"><?= htmlspecialchars($profil['phone'] ?? '') ?></p>
              </div>
              <div class="form-group">
                <label>Password</label>
                <p class="text-value">********</p>
              </div>
            </div>

            <!-- MODE EDIT (EDIT MODE) -->
            <form
              id="editMode"
              class="editor-grid d-none"
              action=""
              method="POST"
            >
              <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($profil['nama_lengkap'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim" value="<?= htmlspecialchars($profil['nim'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat"><?= htmlspecialchars($profil['alamat'] ?? '') ?></textarea>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($profil['email'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="hp" value="<?= htmlspecialchars($profil['phone'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label>Password</label>
                
                <input type="password" name="password" value="<?= htmlspecialchars($profil['password'] ?? '') ?>" />
              </div>

              <div class="button-group">
                <button type="submit" class="btn-save">Simpan Perubahan</button>
                <button
                  type="button"
                  class="btn-batal"
                  onclick="toggleMode('view')"
                >
                  Batal
                </button>
              </div>
            </form>
          </div>
        </article>
      </section>
    </main>

    <script>
      function toggleMode(mode) {
       
        const viewMode = document.getElementById("viewMode");
        const editMode = document.getElementById("editMode");
        const btnEditHero = document.getElementById("btnEditHero");
        const btnCoverHero = document.getElementById("btnCoverHero");

        if (mode === "edit") {
         
          if(viewMode) viewMode.classList.add("d-none");
          if(btnEditHero) btnEditHero.classList.add("d-none");

          if(editMode) editMode.classList.remove("d-none");
          if(btnCoverHero) btnCoverHero.classList.remove("d-none");
        } else {
         
          if(viewMode) viewMode.classList.remove("d-none");
          if(btnEditHero) btnEditHero.classList.remove("d-none");

          if(editMode) editMode.classList.add("d-none");
          if(btnCoverHero) btnCoverHero.classList.add("d-none");
        }
      }
    </script>
  </body>
</html>