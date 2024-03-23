<?php
include('module.php');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K&ouml;penickralley</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="map.css" />
    <link rel="stylesheet" href="darksite.css" />
</head>
<?php
$conn = dbconnect();
httpsredirect();
?>
<body>
    <div class="main">
        <?php
            loginmaske($conn);
        ?>
        <div id="menu-togglea" style="position: absolute;">&#9776;</div>
        <div id="links-container">
            <div id="menu-toggleb">&#9776;</div>
            <!-- Links werden hier dynamisch hinzugefügt -->
        </div>
        <div id="reload-button" onclick="reloadMap()">
            <img src="navigation.png" alt="Reload" />
        </div>
        <div id="map-container"></div>
    </div>


    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        
        var map = L.map('map-container').setView([52.4440, 13.5850], 15); // Köpenick, Berlin
        var userLocation;
        var customMarkers = [];

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '� OpenStreetMap contributors'
        }).addTo(map);

        // Geolocation API verwenden, um Benutzerposition zu erhalten
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
            // Function to continuously monitor the user's location
            function trackUserLocation() {
                var options = {
                    enableHighAccuracy: true,
                    timeout: 50,
                    maximumAge: 0
                };
                var watchId = navigator.geolocation.watchPosition(updateUserLocation, handleLocationError, options);
            }
        } else {
            alert("Geolocation is not supported by this browser.");
        }

        function showPosition(position) {
            userLocation = L.latLng(position.coords.latitude, position.coords.longitude);
            // Blauer Kreis für den eigenen Standort
            var userCircle = L.circle(userLocation, {
                color: 'blue',
                fillColor: 'lightblue',
                fillOpacity: 0.75,
                radius: 25
            }).addTo(map);

   // Begrenzt die Pan- und Zoommöglichkeiten auf den Bereich von Köpenick
            var southWest = L.latLng(52.42, 13.53);
            var northEast = L.latLng(52.49, 13.66);
            var bounds = L.latLngBounds(southWest, northEast);

            map.setMaxBounds(bounds);
            map.on('drag', function () {
                map.panInsideBounds(bounds, { animate: false });
            });


            <?php            
            createtable($conn, "stations");
            
            
            ?>
            var points = [
                <?php 
                    if(angemeldet($conn)){
                        stationsasvar($conn);
                    }
                ?>
                
            ];

            var connections = [];

            points.forEach(function (point, index) {
                var markerColor = getMarkerColor(point.status);
                var customMarker = L.marker([point.lat, point.lon], {
                    icon: L.icon({
                        iconUrl: markerColor,
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup(point.label);

                customMarkers.push(customMarker);

                // Verbindungen hinzufügen (außer für den letzten Punkt)
                if (index < points.length - 1) {
                    var connection = [
                        [point.lat, point.lon],
                        [points[index + 1].lat, points[index + 1].lon]
                    ];
                    connections.push(connection);
                    var line = L.polyline(connection, { color: 'blue' }).addTo(map);
                }

                // Erstellt einen Link für jeden Punkt
                var linkContainer = document.getElementById('links-container');
                var pointLink = document.createElement('a');
                pointLink.href = '#';
                pointLink.className = 'point-link' + point.status;
                pointLink.innerHTML = point.label + "</br>";

                // Überprüft den Status und aktualisiert den Link entsprechend
                updateLinkStatus(point, pointLink);

                // Fügt den Link zum Container hinzu
                linkContainer.appendChild(pointLink);
            });

            // Überwacht die Bewegung des Benutzers, um die Links zu aktualisieren
            map.on('move', function () {
                points.forEach(function (point, index) {
                    var pointLink = document.querySelector('.point-link:nth-child(' + (index + 1) + ')');
                    if (pointLink) {
                        updateLinkStatus(point, pointLink);
                    }
                });
            });
        }

        // Funktion zur Bestimmung der Markerfarbe basierend auf dem Status
        function getMarkerColor(status) {
            switch (status) {
                case 'locked':
                    return 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png';
                case 'unlocked':
                    return 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png';
                case 'solved':
                    return 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png';
                default:
                    return '';
            }
        }

        // Funktion zum Aktualisieren des Status des Links basierend auf der Benutzerentfernung zum Punkt
        function updateLinkStatus(point, link) {
            var distance = userLocation.distanceTo(L.latLng(point.lat, point.lon));
            
            if (distance < 75 && point.status === 'locked') {
                point.status = 'unlocked';
                link.className = 'point-link unlocked';
                link.href = "https://xn--kpenickralley-imb.de/station.php?stationname="+encodeURIComponent(point.label);
                var marker = customMarkers.find(function (marker) {
                        return marker.getLatLng().lat === point.lat && marker.getLatLng().lng === point.lon;
                    });
                    if (marker) {
                        marker.setIcon(L.icon({
                            iconUrl: getMarkerColor(point.status),
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        }));
                    }
                link.addEventListener('click', function () {
                    //alert('Link zu ' + point.label + ' wurde geklickt!');
                    // Aktionen ausführen, wenn der Link geklickt wird
                    point.status = 'solved';
                    var marker = customMarkers.find(function (marker) {
                        return marker.getLatLng().lat === point.lat && marker.getLatLng().lng === point.lon;
                    });
                    if (marker) {
                        marker.setIcon(L.icon({
                            iconUrl: getMarkerColor(point.status),
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        }));
                    }
                    // Link-Klasse aktualisieren
                    link.className = 'point-link solved';
                    link.removeEventListener('click', null);
                });
            } else if (point.status === 'unlocked' || point.status === 'solved') {
                link.className = 'point-link ';
                link.removeEventListener('click', null);
            }
        }

        

        //Nutzerposition updaten
        function updateUserLocation(position) {
            userLocation = L.latLng(position.coords.latitude, position.coords.longitude);
               userMarker.setLatLng(userLocation);
        }

        //Fehlermeldungen bei Problemen mit der Karte
        function handleLocationError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    console.log("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    console.log("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    console.log("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    console.log("An unknown error occurred.");
                    break;
            }
        }

        function reloadMap() {
            location.reload();
        }
    </script>
    <script>
        let menuToggle = document.getElementById('menu-togglea');
        let linksContainer = document.getElementById('links-container');
        <?php

        if(angemeldet($conn)){
            echo "let logoutlink = document.createElement('a');";
            echo "logoutlink.href = 'abmelden.php';";
            echo "logoutlink.className = 'point-link unlocked unten logoutbutton';";
            echo 'logoutlink.innerHTML = "Abmelden &#128682;" + "</br>";';
            echo 'linksContainer.appendChild(logoutlink);';
        }

        ?>
        menuToggle.addEventListener('click', () => {
            // Menü öffnen/schließen
            isOpen = linksContainer.style.left === '0px';
            animiere(isOpen)
            linksContainer.style.display = isOpen ? 'none' : 'block';
        });

        menuToggleb = document.getElementById('menu-toggleb');


        menuToggleb.addEventListener('click', () => {
            // Menü öffnen/schließen
            isOpen = linksContainer.style.left === '0px';
            animiere(true)
            linksContainer.style.display = isOpen ? 'none' : 'block';
        });

        function animiere(opening) {
		
        if(opening == true){
            linksContainer.animate(
          [
	    			{
	    				left: '-10px'
            }, {
	    				left: '-300px'
            }
          ], 
          {
	    			duration: 100,
	    			iterations: 1,
	    			fill: 'forwards'
	    		});
	    }else{
            linksContainer.animate(
          [
	    			{
	    				left: '-300px'
            }, {
	    				left: '-10px'
            }
          ], 
          {
	    			duration: 100,
	    			iterations: 1,
	    			fill: 'forwards'
	    		});
        }
        }
    </script>
</body>
</html>