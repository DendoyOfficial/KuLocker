<?php
require 'config/auth.php';

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama - KuLocker</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

    <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
  />

    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="css/dashboard-utama.css" />
</head>
<body>
            <!-- ═══════════════ NAVBAR ═══════════════ -->
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

        <div class="user-menu">
            <span class="user-name">
                <?= $user['full_name']; ?>
            </span>

            <a href="config/logout.php" class="logout-btn">
                Logout
            </a>

        </div>
    </div>
    </nav>
        <!-- ═══════════════ HERO ═══════════════ -->
    <section id="hero">
    <div class="hero-radial-1"></div>
    <div class="hero-radial-2"></div>

    <div class="hero-inner">
        <div class="hero-grid">
        <div class="reveal">
            <div class="hero-badge">
            <span>Coming sooon</span>
            </div>

            <h1 class="hero-title">
            Welcome back <br>
            <span class="gold-text">
                <?= $user['full_name']; ?>
            </span>
            </h1>

            <p class="hero-subtitle">
            Pesan jasa locker nya gaskeun
            </p>

            <div class="hero-btns">
            <button class="btn-dark" onclick="window.location.href='sign-in.html'"> Daftar sekarang</button>
            </div>
        </div>
        </div>
    </div>
    </section>
    <!-- ═══════════════ FOOTER ═══════════════ -->
    <footer>
    <div class="footer-inner">
        <div class="footer-grid">
        <div class="footer-brand">
            <div class="footer-logo-icon">
            <img src="img/Kulocker.jpeg" alt="Logo Kulocker">
        </div>
            <p>Secure smart locker solutions for modern delivery. Making package management simple, safe, and accessible 24/7.</p>
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
            <li> <p>082102924</p></li>
            <li> <p>@KuLocker</p></li>
            <li> <p>Jl. Sepatu dua belas. Kiri kanan kotak x segitiJga</p> </li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>LEGALITAS</h3>
            <ul>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Cookie Policy</a></li>
            </ul>
        </div>
        </div>
        <div class="footer-bottom">
        <p class="footer-copy">© 2026 Kulocker. All rights reserved.</p>
        </div>
    </div>
    </footer>
</body>
</html>