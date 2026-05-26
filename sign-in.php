<?php
session_start();

if(isset($_SESSION['user'])){
    header("Location: dashboard-utama.php");
    exit;
}

$error = "";

if(isset($_SESSION['error'])){
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In — Kulocker</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/sign-in.css" />
</head>
<body>

  <!-- ── Decorative background blobs ── -->
    <div class="bg" aria-hidden="true">
        <div class="back-bubble bubble_1"></div>
        <div class="back-bubble bubble_2"></div>
        <div class="back-bubble bubble_3"></div>
    </div>

  <!-- ── Back button (arrow only) ── -->
    <a href="dashboard.php" class="back-btn" aria-label="Back to home">
        <img src="img/panah-kiri-svg.svg" alt="Kembali" width="24" height="24">  
    </a>

  <!-- ── Centered card ── -->
  <main class="card-wrap">
    <div class="card">

      <!-- Logo -->
    <div class="card-logo">
        <img src="img/Kulocker-removebg-preview.png" alt="logo kulocker" width="130">
    </div>

      <!-- Heading -->
      <div class="card__head">
        <p class="card__eyebrow">Welcome to our login page</p>
        <h1 class="card__title">Sign in to <em>your account</em></h1>
      </div>

      <?php if(!empty($error)): ?>

          <div class="login-error">
              <?= $error; ?>
          </div>

      <?php endif; ?>
        
      <!-- Form -->
      <form class="form" action = "config/login-process.php" method = "POST" id="signinForm" novalidate>

        
        <div class="field">
          <label class="field__label" for="email">Email </label>
          <div class="field__wrap">
            <span class="field__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </span>
            <input
              class="field__input"
              type="text"
              id="username"
              name="username"
              placeholder="Username"
              required
            >
          </div>
          <span class="field__error" id="emailError"></span>
        </div>

        <!-- Password -->
        <div class="field">
          <div class="field__label-row">
            <label class="field__label" for="password">Password</label>
            <a href="#" class="field__forgot">Forgot password?</a>
          </div>
          <div class="field__wrap">
            <span class="field__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </span>
            <input class="field__input" type="password" id="password" name="password"
                  placeholder="••••••••" autocomplete="current-password" />
            <button type="button" class="field__toggle" id="togglePwd" aria-label="Show password">
              <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-hide" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
            </button>
          </div>
          <span class="field__error" id="passwordError"></span>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-submit" id="submitBtn" name = "signin">
          <span class="btn-submit__text">Sign In</span>
          <span class="btn-submit__icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </span>
          <span class="btn-submit__loader" id="btnLoader"></span>
        </button>
        
        <!-- Sign-up link -->
        <p class="signup-cta">Don't have an account? <a href="#">Create one free</a></p>

      </form>
    </div>
  </main>

  <script src="js/sign-in.js"></script>
</body>
</html>