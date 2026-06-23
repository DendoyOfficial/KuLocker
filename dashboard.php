<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Awal - KuLocker</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

    <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
  />

    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="css/dashboard.css" />
</head>
<body>
    <nav>
  <div class="nav-inner">
      <div class="nav-logo-icon">
        <img src="img/Kulocker.jpeg" alt="Logo Kulocker">
      </div>

    <div class="nav-links" id="navLinks">
      <a href="#hero">Home</a>
      <a href="#features">Features</a>
      <a href="#tempat-maps">Location</a>
      <a href="#faq">Help</a>
    </div>

    <button class="btn-gold" id="navCta" onclick="window.location.href='sign-in.php'">
      Sign-In
    </button>
  </div>
</nav>

    <section id="hero">
  <div class="hero-radial-1"></div>
  <div class="hero-radial-2"></div>

  <div class="hero-inner">
    <div class="hero-grid">
      <div class="reveal">
        
        <h1 class="hero-title">
          Solusi <span class="gold-text">Loker Pintar</span> 
          untuk <br> Penyimpanan yang <span class="gold-text">Aman</span>
        </h1>

        <p class="hero-subtitle">
          Titip barang berhargamu tanpa ribet. Nikmati layanan loker otomatis berbasis QR-Code yang siap menjaga keamanan barang bawaanmu kapan saja, di mana saja..
        </p>

        <div class="hero-btns">
          <button class="btn-dark" onclick="window.location.href='sign-up.php'"> Daftar sekarang</button>
        </div>
      </div>
    </div>
  </div>
</section>


<section id="features">
  <div class="section-inner">
    <div class="text-center reveal">
      <div class="section-badge"><span>Features</span></div>
      <h2 class="section-title">Nikmati Berbagai Kemudahan</h2>
      <p class="section-subtitle">Fitur modern yang dirancang khusus untuk kemudahan manajemen dan penyewaan loker mandiri</p>
    </div>
    <div class="features-grid">
      
      <div class="feature-card reveal">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
        </div>
        <h3 class="feature-title">QR Access</h3>
        <p class="feature-desc">Akses pintu loker Anda secara instan menggunakan QR Code tanpa kunci fisik.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <h3 class="feature-title">Real-time Tracking</h3>
        <p class="feature-desc">Pantau sisa durasi waktu sewa loker aktif Anda kapan saja secara real-time langsung melalui halaman dashboard.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 class="feature-title">Secure Storage</h3>
        <p class="feature-desc">Perlindungan berlapis dengan enkripsi kode digital serta pencatatan log akses yang ketat demi menjaga keamanan barang.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 9.4 7.55 4.24"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" x2="12" y1="22" y2="12"/></svg>
        </div>
        <h3 class="feature-title">Contactless Pickup</h3>
        <p class="feature-desc">Sewa dan pilih loker secara mandiri tanpa perlu mengantre atau berinteraksi langsung dengan petugas operasional.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h3 class="feature-title">24/7 Access</h3>
        <p class="feature-desc">Pusat layanan pengaduan terintegrasi yang siap merespons kendala teknis atau keluhan loker Anda dengan cepat.</p>
      </div>

    </div>
  </div>
</section>

<section id="tempat-maps">
  <div class="text-center reveal">
    <div class="section-badge"><span>Location</span></div>
    <h1 class="section-title">Maps Location</h1>
  </div>
  <div class="maps" id="maps"></div>
  <div></div>
</section>

<section id="faq">
  <div class="section-inner">
    <div class="text-center reveal">
      <div class="section-badge"><span>FAQ</span></div>
      <h2 class="section-title">Common questions</h2>
      <p class="section-subtitle">Segala hal yang perlu Anda ketahui mengenai sistem layanan KuLocker</p>
    </div>
    <div class="faq-list">
      
      <div class="faq-item active reveal">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-question">Bagaimana cara kerja penyewaan KuLocker?</span>
          <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-body" style="max-height:400px">
          <div class="faq-body-inner">Sangat mudah! Anda cukup mendaftar akun, memilih lokasi dan ukuran loker yang tersedia, menentukan durasi sewa, lalu melakukan pembayaran digital. Setelah lunas, kode akses atau QR-code akan langsung aktif untuk membuka pintu loker pilihan Anda.</div>
        </div>
      </div>

      <div class="faq-item reveal">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-question">Apakah barang saya aman disimpan di dalam loker?</span>
          <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-body">
          <div class="faq-body-inner">Tentu saja. Setiap bilik loker menggunakan sistem penguncian elektronik otomatis berbasis server yang hanya bisa dipicu terbuka melalui akun penyewa resmi, serta dipantau oleh log akses aktivitas keamanan digital yang ketat.</div>
        </div>
      </div>

      <div class="faq-item reveal">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-question">Apa saja pilihan ukuran loker yang tersedia?</span>
          <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-body">
          <div class="faq-body-inner">KuLocker menyediakan tiga variasi ukuran utama yang dapat disesuaikan dengan kebutuhan Anda, yaitu Small (S) untuk gadget/dokumen, Medium (M) untuk tas ransel standar, dan Large (L) untuk barang bawaan berkapasitas besar.</div>
        </div>
      </div>

      <div class="faq-item reveal">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-question">Berapa tarif sewa loker pintar ini?</span>
          <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-body">
          <div class="faq-body-inner">Tarif sewa kami dihitung secara transparan berbasis jam penggunaan, mulai dari Rp5.000 per jam dengan tambahan biaya layanan flat. Anda bebas menyesuaikan berapa lama durasi pinjam sesuai kebutuhan aktivitas harian Anda.</div>
        </div>
      </div>

      <div class="faq-item reveal">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-question">Bagaimana jika waktu sewa habis namun barang belum saya ambil?</span>
          <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-body">
          <div class="faq-body-inner">Sistem akan otomatis mengirimkan pengingat ketika waktu sewa akan habis. Jika melewati batas waktu yang ditentukan tanpa perpanjangan durasi, status loker akan dikunci sementara hingga Anda menyelesaikan tagihan denda keterlambatan melalui dashboard akun.</div>
        </div>
      </div>

      <div class="faq-item reveal">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-question">Apa yang harus dilakukan jika terjadi kendala saat membuka pintu loker?</span>
          <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-body">
          <div class="faq-body-inner">Anda tidak perlu khawatir. Cukup masuk ke menu keluhan di dalam dashboard utama untuk melaporkan kendala teknis Anda. Tim admin kami terintegrasi penuh untuk membantu merespons laporan dan memverifikasi pembukaan loker darurat.</div>
        </div>
      </div>

    </div>
  </div>
</section>

<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo-icon">
          <img src="img/Kulocker.jpeg" alt="Logo Kulocker">
        </div>
        <p>Solusi penyimpanan loker pintar mandiri yang aman dan tepercaya. Menyewa loker kini menjadi jauh lebih praktis, modern, dan dapat diakses kapan saja.</p>
        <div class="social-links">
          <a href="#" class="social-link" aria-label="Twitter">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
          </a>
          <a href="#" class="social-link" aria-label="LinkedIn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
          </a>
          <a href="#" class="social-link" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
          </a>
          <a href="#" class="social-link" aria-label="Email">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          </a>
        </div>
      </div>
      <div class="footer-col">
        <h3>NAVIGATION</h3>
        <ul>
          <li><a href="#">Home</a></li>
          <li><a href="#">Location</a></li>
          <li><a href="#">Bantuan</a></li>
        </ul>
      </div>
      <div class="tanpa-hover">
        <h3>CONTACT</h3>
        <ul>
          <li> 
            <p style="display: flex; align-items: center; gap: 8px;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              082102924
            </p>
          </li>
          <li> 
            <p style="display: flex; align-items: center; gap: 8px;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
              </svg>
              @KuLocker
            </p>
          </li>
          <li> 
            <p style="display: flex; align-items: center; gap: 8px; align-items: flex-start;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 3px; flex-shrink: 0;">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              Jl. Sepatu dua belas. Kiri kanan kotak x segitiga
            </p> 
          </li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="footer-copy">© 2026 Kulocker. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

<script src="js/dashboard.js"></script>

</body>
</html>