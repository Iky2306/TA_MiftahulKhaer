<style>
    #map {
        height: 250px;
    }
</style>

<div id="map"></div>

<script>
    var lokasi = "{{ $presensi->lokasi_in }}";
    var lok = lokasi.split(",");
    var latitude = parseFloat(lok[0]);
    var longitude = parseFloat(lok[1]);

    var map = L.map('map').setView([latitude, longitude], 18);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker = L.marker([latitude, longitude]).addTo(map);

    var circle = L.circle([], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: 20
    }).addTo(map);

    var popup = L.popup()
        .setLatLng([latitude, longitude])
        .setContent("{{ $presensi->nama_lengkap }}")
        .openOn(map);

    if ("geolocation" in navigator) {
        setInterval(function () {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    // Optional: bisa pakai untuk update marker atau logging lokasi real-time
                    console.log("Current position:", position.coords.latitude, position.coords.longitude);
                },
                function (error) {
                    console.error("Error getting location: ", error);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 5000
                }
            );
        }, 5000);
    } else {
        console.log("Geolocation tidak didukung oleh browser ini.");
        // Pastikan elemen dengan id 'status' ada jika ingin ini dipakai
        var statusEl = document.getElementById('status');
        if (statusEl) {
            statusEl.innerText = "Geolocation tidak didukung";
        }
    }
</script>
