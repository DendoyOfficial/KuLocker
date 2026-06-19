<<<<<<< HEAD
document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Tukar tipe input antara password dan text
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.textContent = 'Sembunyikan Password';
            } else {
                passwordInput.type = 'password';
                this.textContent = 'Lihat Password';
            }
        });
    }
=======
document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Tukar tipe input antara password dan text
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.textContent = 'Sembunyikan Password';
            } else {
                passwordInput.type = 'password';
                this.textContent = 'Lihat Password';
            }
        });
    }
>>>>>>> b4dfe23a4b265e955d212e01d0a28b2948d7227f
});