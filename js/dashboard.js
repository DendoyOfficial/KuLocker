// FAQ Accordion
const faqItems = document.querySelectorAll('.faq-item');

faqItems.forEach(item => {
  const trigger = item.querySelector('.faq-trigger');
  const body = item.querySelector('.faq-body');

  if (!trigger || !body) return;

  trigger.addEventListener('click', () => {
    const isOpen = item.classList.contains('active');

    faqItems.forEach(i => {
      i.classList.remove('active');

      const b = i.querySelector('.faq-body');
      if (b) {
        b.style.maxHeight = null;
      }
    });

    if (!isOpen) {
      item.classList.add('active');
      body.style.maxHeight = body.scrollHeight + 'px';
    }
  });
});

//MAPS
  var map = L.map('maps').setView([-8.586716,116.0933652], 17);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var marker = L.marker([-8.586716,116.0933652]).addTo(map);