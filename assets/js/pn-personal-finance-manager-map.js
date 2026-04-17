// pn-personal-finance-manager-map.js
(function($) {
  function initPnPersonalFinanceManagerMap(mapId, searchId, inputId, initialValue) {
    if (typeof L === 'undefined') {
      setTimeout(function() { initPnPersonalFinanceManagerMap(mapId, searchId, inputId, initialValue); }, 200);
      return;
    }
    var map = L.map(mapId).setView([40.4168, -3.7038], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    var marker;
    var input = document.getElementById(inputId);
    var searchInput = document.getElementById(searchId);
    // Si hay valor guardado, centrar el mapa
    if (initialValue) {
      var coords = initialValue.split(',');
      if (coords.length === 2) {
        var lat = parseFloat(coords[0]);
        var lng = parseFloat(coords[1]);
        marker = L.marker([lat, lng], {draggable:true}).addTo(map);
        map.setView([lat, lng], 15);
        marker.on('dragend', function(e) {
          var pos = marker.getLatLng();
          input.value = pos.lat + ',' + pos.lng;
        });
      }
    }
    map.on('click', function(e) {
      if (marker) map.removeLayer(marker);
      marker = L.marker(e.latlng, {draggable:true}).addTo(map);
      input.value = e.latlng.lat + ',' + e.latlng.lng;
      marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        input.value = pos.lat + ',' + pos.lng;
      });
    });
    // Búsqueda de dirección usando Nominatim
    if (searchInput) {
      searchInput.addEventListener('change', function() {
        var query = searchInput.value;
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query))
          .then(response => response.json())
          .then(data => {
            if (data && data.length > 0) {
              var lat = parseFloat(data[0].lat);
              var lon = parseFloat(data[0].lon);
              map.setView([lat, lon], 16);
              if (marker) map.removeLayer(marker);
              marker = L.marker([lat, lon], {draggable:true}).addTo(map);
              input.value = lat + ',' + lon;
              marker.on('dragend', function(e) {
                var pos = marker.getLatLng();
                input.value = pos.lat + ',' + pos.lng;
              });
            }
          });
      });
    }
  }

  // Inicializar todos los mapas de tipo .pn-personal-finance-manager-map
  function initAllPnPersonalFinanceManagerMaps() {
    $('.pn-personal-finance-manager-map-wrapper').each(function() {
      var wrapper = $(this);
      var mapId = wrapper.find('.pn-personal-finance-manager-map').attr('id');
      var searchId = wrapper.find('.pn-personal-finance-manager-map-search').attr('id');
      var inputId = wrapper.find('input[type=hidden]').attr('id');
      var initialValue = wrapper.find('input[type=hidden]').val();
      if (mapId && searchId && inputId) {
        initPnPersonalFinanceManagerMap(mapId, searchId, inputId, initialValue);
      }
    });
  }

  // Leaflet is loaded via wp_enqueue_script dependency — no CDN needed
  $(document).ready(function() {
    initAllPnPersonalFinanceManagerMaps();
  });
})(jQuery); 