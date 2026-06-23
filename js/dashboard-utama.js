/* ============================================================
   KuLocker — Dashboard Utama JS
   ============================================================ */

/* ── DROPDOWN PROFIL ──────────────────────────────────────── */
function toggleDropdown(e) {
    e.stopPropagation();
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('show');
}

document.addEventListener('click', () => {
    const menu = document.getElementById('dropdownMenu');
    if (menu) menu.classList.remove('show');
});

/* ── CAROUSEL ─────────────────────────────────────────────── */
(function () {
    const slides = document.querySelectorAll('.slide');
    const nav    = document.getElementById('carouselNav');
    if (!slides.length || !nav) return;

    let current = 0;
    let autoplay;

    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.addEventListener('click', () => { goTo(i); resetAutoplay(); });
        nav.appendChild(dot);
    });

    function goTo(n) {
        slides[current].classList.remove('active');
        nav.children[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        nav.children[current].classList.add('active');
    }

    window.nextSlide = () => { goTo(current + 1); resetAutoplay(); };
    window.prevSlide = () => { goTo(current - 1); resetAutoplay(); };

    function startAutoplay() { autoplay = setInterval(() => goTo(current + 1), 4500); }
    function resetAutoplay() { clearInterval(autoplay); startAutoplay(); }

    startAutoplay();
})();

/* ── SCROLL REVEAL ────────────────────────────────────────── */
(function () {
    const reveals = document.querySelectorAll('.reveal');
    if (!reveals.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    reveals.forEach(el => observer.observe(el));
})();

/* ── MAP MODAL ────────────────────────────────────────────── */
(function () {
    const overlay   = document.getElementById('mapModal');
    const closeBtn  = document.getElementById('mapClose');
    const searchIn  = document.getElementById('mapSearch');
    const gpsBtn    = document.getElementById('gpsBtn');
    const lokerList = document.getElementById('lokerList');
    if (!overlay) return;

    let map, markers = [], allLokers = [];

    window.openMap = function () {
        overlay.classList.add('open');
        if (!map) initMap();
    };

    if (closeBtn) closeBtn.addEventListener('click', closeMap);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) closeMap(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMap(); });

    function closeMap() { overlay.classList.remove('open'); }

    function initMap() {
        map = L.map('map').setView([-8.5832, 116.1212], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);
        loadLokers();
    }

    function loadLokers() {
        fetch('api/get-lokers.php')
            .then(r => r.json())
            .then(data => { allLokers = data; renderMarkers(data); renderList(data); })
            .catch(() => {
                const dummy = [
                    { id: 1, kode_loker: 'A-01', lokasi: 'Gedung A Lt.1', status: 'tersedia', lat: -8.5830, lng: 116.1210 },
                    { id: 2, kode_loker: 'A-02', lokasi: 'Gedung A Lt.1', status: 'terpakai', lat: -8.5831, lng: 116.1213 },
                    { id: 3, kode_loker: 'B-01', lokasi: 'Gedung B Lt.2', status: 'tersedia', lat: -8.5835, lng: 116.1220 },
                    { id: 4, kode_loker: 'B-02', lokasi: 'Gedung B Lt.2', status: 'tersedia', lat: -8.5836, lng: 116.1222 },
                ];
                allLokers = dummy; renderMarkers(dummy); renderList(dummy);
            });
    }

    function renderMarkers(lokers) {
        markers.forEach(m => map.removeLayer(m));
        markers = [];
        lokers.forEach(loker => {
            if (!loker.lat || !loker.lng) return;
            const color = loker.status === 'tersedia' ? '#27ae60' : '#e74c3c';
            const icon = L.divIcon({
                className: '',
                html: `<div style="width:32px;height:32px;border-radius:50%;background:${color};border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.25);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;font-family:Inter,sans-serif;">${loker.kode_loker}</div>`,
                iconSize: [32, 32], iconAnchor: [16, 16]
            });
            const marker = L.marker([loker.lat, loker.lng], { icon }).addTo(map)
                .bindPopup(`<div style="font-family:Inter,sans-serif;min-width:150px;padding:4px 0;">
                    <strong style="font-size:14px;">${loker.kode_loker}</strong>
                    <p style="color:#888;font-size:12px;margin:4px 0;">${loker.lokasi}</p>
                    <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:999px;background:${loker.status === 'tersedia' ? 'rgba(39,174,96,0.1)' : 'rgba(231,76,60,0.1)'};color:${loker.status === 'tersedia' ? '#27ae60' : '#e74c3c'};">${loker.status}</span>
                    ${loker.status === 'tersedia' ? `<br><a href="pesan-loker.php?id=${loker.id}" style="display:inline-block;margin-top:10px;background:#D4AF37;color:#111;font-size:12px;font-weight:600;padding:6px 14px;border-radius:999px;text-decoration:none;">Pesan</a>` : ''}
                </div>`);
            markers.push(marker);
        });
    }

    function renderList(lokers) {
        if (!lokerList) return;
        lokerList.innerHTML = '';
        if (!lokers.length) {
            lokerList.innerHTML = '<p style="color:#aaa;font-size:13px;padding:12px 0;">Tidak ada loker ditemukan.</p>';
            return;
        }
        lokers.forEach(loker => {
            const item = document.createElement('div');
            item.className = 'modal-list-item';
            item.innerHTML = `
                <div class="loker-info">
                    <p>${loker.kode_loker}</p>
                    <span>${loker.lokasi}</span>
                </div>
                <span class="loker-status ${loker.status}">${loker.status}</span>`;
            item.addEventListener('click', () => {
                if (loker.lat && loker.lng) {
                    map.setView([loker.lat, loker.lng], 18);
                    markers.forEach(m => {
                        if (m.getLatLng().lat === loker.lat && m.getLatLng().lng === loker.lng) m.openPopup();
                    });
                }
            });
            lokerList.appendChild(item);
        });
    }

    if (searchIn) {
        searchIn.addEventListener('input', () => {
            const q = searchIn.value.toLowerCase();

            // Filter marker & list loker
            const filtered = allLokers.filter(l =>
                l.kode_loker.toLowerCase().includes(q) || l.lokasi.toLowerCase().includes(q)
            );
            renderMarkers(filtered);
            renderList(filtered);

            // Filter card lokasi gedung
            document.querySelectorAll('.modal-lokasi-card').forEach(card => {
                const nama = card.getAttribute('data-lokasi') || '';
                card.style.display = (q === '' || nama.includes(q)) ? 'flex' : 'none';
            });
        });
    }

    if (gpsBtn) {
        gpsBtn.addEventListener('click', () => {
            if (!navigator.geolocation) return alert('Browser tidak mendukung geolocation.');
            gpsBtn.innerHTML = '<i class="ti ti-loader-2"></i> Mencari...';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const { latitude, longitude } = pos.coords;
                    L.marker([latitude, longitude], {
                        icon: L.divIcon({
                            className: '',
                            html: `<div style="width:14px;height:14px;border-radius:50%;background:#378add;border:3px solid #fff;box-shadow:0 0 0 4px rgba(55,138,221,0.25);"></div>`,
                            iconSize: [14, 14], iconAnchor: [7, 7]
                        })
                    }).addTo(map).bindPopup('Lokasi kamu').openPopup();
                    map.setView([latitude, longitude], 17);

                    const sorted = [...allLokers].filter(l => l.lat && l.lng)
                        .map(l => ({ ...l, jarak: getDistance(latitude, longitude, l.lat, l.lng) }))
                        .sort((a, b) => a.jarak - b.jarak);

                    renderMarkers(sorted); renderList(sorted);
                    gpsBtn.innerHTML = '<i class="ti ti-current-location"></i> Terdekat';
                },
                () => {
                    alert('Gagal mendapatkan lokasi.');
                    gpsBtn.innerHTML = '<i class="ti ti-current-location"></i> Lokasi Saya';
                }
            );
        });
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const φ1 = lat1 * Math.PI / 180, φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180, Δλ = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(Δφ/2)**2 + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

})();