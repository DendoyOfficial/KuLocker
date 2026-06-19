/* ============================================================
   KuLocker — Dashboard Awal JS
   ============================================================ */
//MAPS
  var map = L.map('maps').setView([-8.586716,116.0933652], 17);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var marker = L.marker([-8.586716,116.0933652]).addTo(map);
/* ── SCROLL REVEAL ANIMATION ──────────────────────────────── */
(function () {
  const reveals = document.querySelectorAll('.reveal');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); // animasi hanya sekali
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -40px 0px'
  });

  reveals.forEach(el => observer.observe(el));
})();

/* ── FAQ TOGGLE ───────────────────────────────────────────── */
function toggleFaq(btn) {
  const item = btn.closest('.faq-item');
  const body = item.querySelector('.faq-body');
  const isOpen = item.classList.contains('active');

  // Tutup semua
  document.querySelectorAll('.faq-item').forEach(i => {
    i.classList.remove('active');
    i.querySelector('.faq-body').style.maxHeight = null;
  });

  // Buka yang diklik kalau belum open
  if (!isOpen) {
    item.classList.add('active');
    body.style.maxHeight = body.scrollHeight + 'px';
  }
}

/* ── DROPDOWN PROFIL ──────────────────────────────────────── */
function toggleDropdown(e) {
  e.stopPropagation();
  const menu = document.getElementById('dropdownMenu');
  if (menu) menu.classList.toggle('show');
}

document.addEventListener('click', () => {
  const menu = document.getElementById('dropdownMenu');
  if (menu) menu.classList.remove('show');
});

/* ── LEAFLET MAP ──────────────────────────────────────────── */
(function () {
  const mapEl = document.getElementById('maps');
  if (!mapEl) return;

  const map = L.map('maps').setView([-8.5832, 116.1212], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
    maxZoom: 19
  }).addTo(map);

  // Lokasi user
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((pos) => {
      const { latitude, longitude } = pos.coords;
      L.marker([latitude, longitude], {
        icon: L.divIcon({
          className: '',
          html: `<div style="width:14px;height:14px;border-radius:50%;background:#378add;border:3px solid #fff;box-shadow:0 0 0 4px rgba(55,138,221,0.25);"></div>`,
          iconSize: [14, 14], iconAnchor: [7, 7]
        })
      }).addTo(map).bindPopup('Lokasi kamu').openPopup();
      map.setView([latitude, longitude], 16);
    });
  }

  
}
)();