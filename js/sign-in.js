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
});