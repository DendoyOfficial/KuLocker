document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        const nim             = document.getElementById('nim').value.trim();
        const password        = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Validasi format NIM (contoh: F1D02410001)
        const nimRegex = /^[A-Z][0-9][A-Z][0-9]{8}$/;
        if (!nimRegex.test(nim)) {
            alert("Format NIM tidak valid! Contoh: F1D02410001");
            e.preventDefault();
            return;
        }

        // Validasi panjang password minimal 8 karakter
        if (password.length < 8) {
            alert("Kata sandi minimal harus 8 karakter!");
            e.preventDefault();
            return;
        }

        // Validasi kecocokan password dan konfirmasi password
        if (password !== confirmPassword) {
            alert("Konfirmasi kata sandi tidak cocok! Pastikan keduanya sama.");
            e.preventDefault();
            return;
        }
    });
});

