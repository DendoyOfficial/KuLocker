const lockerButtons = document.querySelectorAll('.locker-btn.btn-available');
const durationSection = document.getElementById('duration-section');
const selectedLockerTitle = document.getElementById('selected-locker-title');
const hoursDisplay = document.getElementById('hours-display');
// 🟢 Diubah menjadi statusDisplay untuk mencerminkan status akses, bukan harga
const statusDisplay = document.getElementById('status-display');
const btnContinue = document.getElementById('btn-continue');
const btnPlus = document.getElementById('btn-plus');
const btnMinus = document.getElementById('btn-minus');

// Elemen Input Form Tersembunyi
const formLockerId = document.getElementById('form-locker-id');
const formDurasi = document.getElementById('form-durasi');

let selectedLocker = null;
let currentHours = 1;

function updateSelection() {
    if (selectedLocker) {
        statusDisplay.className = "total-price-active";
        btnContinue.disabled = false;
        btnContinue.className = "btn-continue-active";

        // === SINKRONISASI KE FORM HTML ===
        formLockerId.value = selectedLocker.getAttribute('data-db-id');
        formDurasi.value = currentHours;
        
    } else {
        statusDisplay.className = "total-price-empty";
        btnContinue.disabled = true;
        btnContinue.className = "btn-continue-disabled";

        formLockerId.value = "";
        formDurasi.value = "1";
    }
}

lockerButtons.forEach(button => {
    button.addEventListener('click', () => {
        if (selectedLocker === button) {
            button.classList.remove('btn-selected');
            button.classList.add('btn-available');
            selectedLocker = null;
            durationSection.classList.add('hidden');
        } else {
            lockerButtons.forEach(btn => {
                btn.classList.remove('btn-selected');
                btn.classList.add('btn-available');
            });

            button.classList.remove('btn-available');
            button.classList.add('btn-selected');
            selectedLocker = button;

            selectedLockerTitle.innerText = `${button.getAttribute('data-kode')} Terpilih`;
            
            currentHours = 1;
            hoursDisplay.innerText = currentHours;

            durationSection.classList.remove('hidden');
        }
        updateSelection(); // 🟢 Nama fungsi diubah agar lebih netral (bukan updatePricing)
    });
});

btnPlus.addEventListener('click', () => {
    if (currentHours < 3) {
        currentHours++;
        hoursDisplay.innerText = currentHours;
        updateSelection();
    } else {
        alert("Batas maksimal waktu penggunaan loker adalah 3 jam!");
    }
});

btnMinus.addEventListener('click', () => {
    if (currentHours > 1) {
        currentHours--;
        hoursDisplay.innerText = currentHours;
        updateSelection();
    }
});