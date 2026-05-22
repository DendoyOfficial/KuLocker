  //MAPS
  var map = L.map('maps').setView([-8.586716,116.0933652], 17);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var marker = L.marker([-8.586716,116.0933652]).addTo(map);