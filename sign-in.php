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

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KuLocker - Sign In</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/sign-in.css" />
  </head>
  <body>

    <div class="background-container">
        <a href="dashboard.php" class="back-btn" aria-label="Back to home">
          <img src="img/panah-kiri-svg.svg" alt="Kembali" width="24" height="24">  
        </a>
      <div class="login-card">
        <div class="card-left">
          <div class="overlay-text">
            <p>Welcome to KuLocker</p>
          </div>
        </div>

        
        <div class="card-right">
          <img src="img/Kulocker-removebg-preview.png" alt="Logo">
          <h2 class="form-title"><i>Sign-In</i> to your account</h2>

          <?php if(!empty($error)): ?>

          <div class="login-error <?= $error ? 'show' : '' ?>">
            <?= htmlspecialchars($error ?? '') ?>
          </div>

          <?php endif; ?>
          <!--FORM-->
          <form class = form-card-sign action="config/login-process.php" method = "POST" id = "signinForm" novalidate>
            <div class="input-group">
              <label for="nim" no>NIM :</label>
              <input type="text" id="nim" name="nim" required />
            </div>

            <div class="input-group">
              <label for="password">Password :</label>
              <input class="field__input"type="password" id="password" name="password" required />

              <span
                id="togglePassword"
                style="
                  font-size: 10px;
                  color: #fbc531;
                  cursor: pointer;
                  display: block;
                  margin-top: 5px;
                  font-weight: bold;
                "
              >
            </div>

            <hr>

            <div class="forgot-password">
                <a href="forgot-password.php"> <u>Lupa password?</u></a>
                <a href="sign-up.php"> <u>Buat Akun Baru</u></a>
            </div>

            <button type="submit" class="btn-submit" name="signin" id="submitBtn">Sign In</button>
          </form>
        </div>
      </div>
    </div>
    <script src="js/sign-in.js"></script>
  </body>
</html>