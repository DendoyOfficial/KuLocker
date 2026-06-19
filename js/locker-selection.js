const lockerButtons = document.querySelectorAll('.locker-btn.btn-available');
const durationSection = document.getElementById('duration-section');
const selectedLockerTitle = document.getElementById('selected-locker-title');
const hoursDisplay = document.getElementById('hours-display');
const totalPriceDisplay = document.getElementById('total-price-display');
const btnContinue = document.getElementById('btn-continue');
const btnPlus = document.getElementById('btn-plus');
const btnMinus = document.getElementById('btn-minus');

// Elemen Input Form Tersembunyi
const formLockerId = document.getElementById('form-locker-id');
const formDurasi = document.getElementById('form-durasi');

let selectedLocker = null;
let basePricePerHour = 0;
let currentHours = 1;

function updatePricing() {
    if (selectedLocker) {
        const total = basePricePerHour * currentHours;
        totalPriceDisplay.innerText = `Rp${total.toLocaleString('id-ID')}`;
        totalPriceDisplay.className = "total-price-active";
        
        btnContinue.disabled = false;
        btnContinue.className = "btn-continue-active";

        // Masukkan data ke input form agar siap dikirim ke database pembayaraan/pemesanan
        formLockerId.value = selectedLocker.getAttribute('data-db-id');
        formDurasi.value = currentHours;
    } else {
        totalPriceDisplay.innerText = "Rp0";
        totalPriceDisplay.className = "total-price-empty";
        
        btnContinue.disabled = true;
        btnContinue.className = "btn-continue-disabled";

        // Reset input form jika batal pilih
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

            basePricePerHour = parseInt(button.getAttribute('data-price'));
            selectedLockerTitle.innerText = `${button.getAttribute('data-kode')} Terpilih`;
            
            currentHours = 1;
            hoursDisplay.innerText = currentHours;

            durationSection.classList.remove('hidden');
        }
        updatePricing();
    });
});

btnPlus.addEventListener('click', () => {
    currentHours++;
    hoursDisplay.innerText = currentHours;
    updatePricing();
});

btnMinus.addEventListener('click', () => {
    if (currentHours > 1) {
        currentHours--;
        hoursDisplay.innerText = currentHours;
        updatePricing();
    }
});