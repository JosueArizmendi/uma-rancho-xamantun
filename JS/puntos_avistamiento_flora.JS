// Primero obtenemos los datos del avistamiento
const idAvistamiento = document.getElementById('id_avistamiento').value;

// Inicializamos el mapa después de obtener los datos
fetch('../model/mapas/avistamiento_flora.php?id_avistamiento=' + idAvistamiento)
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos obtenidos:', data);
        
        if (data.length > 0) {
            // Obtenemos la primera coordenada para centrar el mapa
            const primeraCoordenada = data[0];
            
            // Inicializamos el mapa centrado en las coordenadas del primer punto
            var map = L.map('map').setView([primeraCoordenada.latitud, primeraCoordenada.longitud], 13);

            // Añadimos "Tile Layer"
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Marcadores personalizados
            var iconoAnimal = L.icon({
                iconUrl: '../img/leaf_icon.png',
                iconSize: [50, 50],
                popupAnchor: [0, -25]
            });

            // Array para markers
            let markers = [];

            // Agregamos todos los marcadores
            data.forEach(punto => {
                var marker = L.marker([punto.latitud, punto.longitud], { icon: iconoAnimal })
                    .bindPopup(`Latitud: ${punto.latitud}<br>Longitud: ${punto.longitud}`)
                    .addTo(map);
                markers.push(marker);
            });

            // Si hay múltiples puntos, ajustamos el zoom para que todos sean visibles
            if (data.length > 1) {
                var group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds());
            }

            // Funcionalidad para mostrar los puntos con click en el mapa
            var popup = L.popup();
            function onMapClick(e) {
                popup
                    .setLatLng(e.latlng)
                    .setContent("Hiciste click en " + e.latlng.toString())
                    .openOn(map);
            }
            map.on('click', onMapClick);
        } else {
            // Si no hay datos, mostramos un mapa por defecto con mensaje
            var map = L.map('map').setView([19.842489, -90.535649], 13);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);
            
            L.popup()
                .setLatLng([19.842489, -90.535649])
                .setContent("No se encontraron coordenadas para este avistamiento")
                .openOn(map);
        }
    })
    .catch(error => {
        console.error('Error al obtener los puntos:', error);
        // Mostrar mapa por defecto con mensaje de error
        var map = L.map('map').setView([19.842489, -90.535649], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href://http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        
        L.popup()
            .setLatLng([19.842489, -90.535649])
            .setContent("Error al cargar las coordenadas: " + error.message)
            .openOn(map);
    });