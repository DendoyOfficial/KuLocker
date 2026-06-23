/* ============================================================
   admin.js — KuLocker Admin Panel
   ============================================================ */

/* ----------------------------------------------------------
   LOCKER: Toggle opsi status edit
   ---------------------------------------------------------- */
function tampilkanOpsiStatus(id) {
    const btnEdit    = document.getElementById('btn-edit-' + id);
    const container  = document.getElementById('opsi-status-container-' + id);
    if (btnEdit && container) {
        btnEdit.style.setProperty('display', 'none', 'important');
        container.style.setProperty('display', 'inline-flex', 'important');
    }
}

/* ----------------------------------------------------------
   LOCKER: Eksekusi update status via AJAX
   ---------------------------------------------------------- */
function eksekusiUpdateStatus(id, statusBaru) {
    const badge     = document.getElementById('badge-status-' + id);
    const btnEdit   = document.getElementById('btn-edit-' + id);
    const container = document.getElementById('opsi-status-container-' + id);

    fetch('update-status.php?id=' + id + '&status=' + encodeURIComponent(statusBaru))
        .then(function(response) {
            if (!response.ok) throw new Error('Server error');

            // Update badge teks
            badge.innerText = statusBaru;

            // Update warna badge
            const s = statusBaru.toLowerCase();
            if (s === 'terpakai') {
                badge.style.backgroundColor = '#fef3c7';
                badge.style.color           = '#92400e';
            } else if (s === 'rusak') {
                badge.style.backgroundColor = '#fee2e2';
                badge.style.color           = '#991b1b';
            } else {
                badge.style.backgroundColor = '#d1fae5';
                badge.style.color           = '#065f46';
            }

            // Kembalikan tampilan tombol
            container.style.setProperty('display', 'none', 'important');
            btnEdit.style.setProperty('display', 'inline-flex', 'important');

            // Refresh visualisasi denah locker
            fetch('admin.php?page=locker')
                .then(function(res) { return res.text(); })
                .then(function(html) {
                    var parser  = new DOMParser();
                    var doc     = parser.parseFromString(html, 'text/html');
                    var gridBaru = doc.getElementById('visualisasi-grid-loker');
                    var gridLama = document.getElementById('visualisasi-grid-loker');
                    if (gridBaru && gridLama) {
                        gridLama.innerHTML = gridBaru.innerHTML;
                    }
                });
        })
        .catch(function(error) {
            console.error('Update status error:', error);
            alert('Gangguan koneksi jaringan. Silakan coba lagi.');
        });
}

/* ----------------------------------------------------------
   LOCKER: Modal hapus
   ---------------------------------------------------------- */
function bukaModalHapusLocker(id, kode) {
    document.getElementById('hapus-locker-id').value    = id;
    document.getElementById('hapus-locker-label').innerText = kode;
    document.getElementById('modal-hapus-loker').style.display = 'flex';
}

function tutupModalHapusLocker() {
    document.getElementById('modal-hapus-loker').style.display = 'none';
}

/* ----------------------------------------------------------
   USER: Modal detail
   ---------------------------------------------------------- */
function tampilkanDetailUser(id, nama, email, hp, role, nim, terdaftar) {
    document.getElementById('detail-user-nama').innerText      = nama;
    document.getElementById('detail-user-email').innerText     = email;
    document.getElementById('detail-user-hp').innerText        = hp;
    document.getElementById('detail-user-role').innerText      = role;
    document.getElementById('detail-user-nim').innerText       = nim || '-';
    document.getElementById('detail-user-terdaftar').innerText = terdaftar;
    document.getElementById('modal-detail-user').style.display = 'flex';
}

function tutupModalUser() {
    document.getElementById('modal-detail-user').style.display = 'none';
}

/* ----------------------------------------------------------
   RIWAYAT: Modal detail pemesanan
   ---------------------------------------------------------- */
function tampilkanDetailPemesanan(id, nama, locker, tanggalPesan, tanggalMulai, tanggalSelesai, status) {
    document.getElementById('detail-pemesanan-nama').innerText           = nama;
    document.getElementById('detail-pemesanan-locker').innerText         = locker;
    document.getElementById('detail-pemesanan-tanggal-pesan').innerText  = tanggalPesan;
    document.getElementById('detail-pemesanan-tanggal-mulai').innerText  = tanggalMulai;
    document.getElementById('detail-pemesanan-tanggal-selesai').innerText = tanggalSelesai;
    document.getElementById('detail-pemesanan-status').innerText         = status;
    document.getElementById('modal-detail-pemesanan').style.display      = 'flex';
}

function tutupModalPemesanan() {
    document.getElementById('modal-detail-pemesanan').style.display = 'none';
}

/* ----------------------------------------------------------
   Global: Tutup modal saat klik overlay
   ---------------------------------------------------------- */
document.addEventListener('click', function(event) {
    var modals = [
        'modal-detail-pemesanan',
        'modal-detail-pembayaran',
        'modal-detail-user',
        'modal-hapus-loker'
    ];
    modals.forEach(function(modalId) {
        var modal = document.getElementById(modalId);
        if (modal && event.target === modal) {
            modal.style.display = 'none';
        }
    });
});