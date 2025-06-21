<x-sidebar-layout>
    <x-slot:heading>
        Map
    </x-slot:heading>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <div class="mt-6 container mx-auto bg-gray-100 p-3">
        <!-- Styled Container for the Map and Buttons -->
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Map Container -->
            <div id="map" class="w-full lg:w-3/4 h-96 border-2 border-blue-300 rounded-lg shadow-md" style="height: 500px;"></div>
            
            <!-- Buttons Container -->
            <div id="buttonsContainer" class="w-full lg:w-1/4 p-4 bg-white border-2 border-gray-200 rounded-lg shadow-md mt-4 lg:mt-0 overflow-y-auto" style="max-height: 500px;">
                <!-- Buttons will be dynamically added here -->
                <h3 class="text-lg font-semibold mb-2 text-gray-700">Ferry Aide Navigation</h3>
            </div>
        </div>
        <p class="mt-3 text-lg font-medium">Current Station: {{ $station }}</p>
    </div>

    <script>
        let map;
        let markers = {}; // Store markers by ferry_aide_id
        let defaultMarkerPosition = null; // To store the initial/default marker position
        let isMarkerSet = false; // Flag to check if the default marker has been set

        function initMap() {
            // Fetch the assigned station from the backend
            fetch("{{ route('ferry-aide.assigned-station') }}")
                .then(response => response.json())
                .then(data => {
                    // Check if the station data is available
                    if (data.lat && data.lng) {
                        var defaultCenter = { lat: data.lat, lng: data.lng };
                        console.log('Assigned station coordinates:', defaultCenter);
                    } else {
                        // Fallback to a default location if the station is not found
                        var defaultCenter = { lat: 14.5680014, lng: 121.0479274 }; // Guadalupe as default
                        console.warn('Assigned station not found. Using default center.');
                    }

                    // Initialize the map with the center based on assigned station or default
                    map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 20,
                        center: defaultCenter,
                    });

                    fetchAndDisplayMarkers();
                    setInterval(fetchAndDisplayMarkers, 10000); // Update markers every 10 seconds
                })
                .catch(error => {
                    console.error('Error fetching assigned station:', error);
                    // Fallback to default location in case of error
                    map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 20,
                        center: { lat: 14.5680014, lng: 121.0479274 } // Default to Guadalupe
                    });
                    fetchAndDisplayMarkers();
                });
        }

        // Define an array of colors for the boat markers (already in your code)
        const boatColors = [
        '#cc0000', // Red
        '#009900', // Green
        '#0000cc', // Blue
        '#cccc00', // Yellow
        '#cc00cc', // Magenta
        '#ff9900', // Orange
        '#00cccc', // Cyan
        '#9900ff', // Purple
        '#ffff99', // Light Yellow
        '#ff6699'  // Pink
        ];

        // Fetch and display markers function (only relevant parts shown)
        function fetchAndDisplayMarkers() {
            console.log('Fetching ferry aide locations...');

            fetch("{{ route('ferry-aide.locations') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched data:', data);

                    if (data.length > 0) {
                        let newFerryAideIds = [];
                        data.forEach((location, index) => {
                            const ferryAideId = location.ferry_aide_id;
                            const position = { 
                                lat: parseFloat(location.latitude), 
                                lng: parseFloat(location.longitude)
                            };
                            const boatName = location.boat_name; // Retrieve boat name from data

                            // Determine the color for the current boat using the index and boatColors array
                            const boatColor = boatColors[index % boatColors.length]; // Cycle through the colors

                            // Create the marker icon as usual (already in your code)
                            const iconSvg = `data:image/svg+xml;utf-8, \
                                %3Csvg xmlns=%22http://www.w3.org/2000/svg%22 height=%2224px%22 viewBox=%220%20-960%20960%20960%22 width=%2224px%22 fill=%22${encodeURIComponent(boatColor)}%22%3E%3Cpath d=%22M479-418ZM158-200 82-468q-3-12 2.5-28t23.5-22l52-18v-184q0-33 23.5-56.5T240-800h120v-120h240v120h120q33 0 56.5 23.5T800-720v184l52 18q21 8 25 23.5t1 26.5l-76 268q-50 0-91-23.5T640-280q-30 33-71 56.5T480-200q-48 0-89-23.5T320-280q-30 33-71 56.5T158-200ZM80-40v-80h80q42 0 83-13t77-39q36 26 77 38t83 12q42 0 83-12t77-38q36 26 77 39t83 13h80v80h-80q-42 0-82-10t-78-30q-38 20-78.5 30T480-40q-41 0-81.5-10T320-80q-38 20-78 30t-82 10H80Zm160-522 240-78 240 78v-158H240v158Zm240 282q47 0 79.5-33t80.5-89q48 54 65 74t41 34l44-160-310-102-312 102 46 158q24-14 41-32t65-74q50 57 81.5 89.5T480-280Z%22/%3E%3C/svg%3E`;

                            // Check if marker already exists for this ferry aide
                            if (markers[ferryAideId]) {
                                // Update existing marker position and color
                                markers[ferryAideId].setPosition(position);
                                markers[ferryAideId].setIcon({
                                    url: iconSvg,
                                    scaledSize: new google.maps.Size(50, 50)
                                });
                                console.log(`Marker updated for ${boatName} at position`, position);
                            } else {

                                const marker = new google.maps.Marker({
                                    position: position,
                                    map: map,
                                    icon: {
                                        url: iconSvg,
                                        scaledSize: new google.maps.Size(50, 50)
                                    }
                                });

                                markers[ferryAideId] = marker;
                                console.log(`New marker created for ${boatName} at position`, position);

                                createButton(ferryAideId, boatName, boatColor); 
                            }

                            newFerryAideIds.push(ferryAideId);
                        });

                        Object.keys(markers).forEach(existingFerryAideId => {
                            if (!newFerryAideIds.includes(parseInt(existingFerryAideId))) {
                                markers[existingFerryAideId].setMap(null);
                                delete markers[existingFerryAideId]; 
                                removeButton(existingFerryAideId);
                                console.log(`Removed marker and button for Ferry Aide ID: ${existingFerryAideId}`);
                            }
                        });
                    } else {
                        console.warn('No locations available');
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        function createButton(ferryAideId, boatName, boatColor) {
            const button = document.createElement('button');
            button.innerText = `${boatName}`;

            button.className = `text-white text-xs font-bold py-2 px-4 rounded m-2`;
            button.style.backgroundColor = boatColor; 
            
            button.addEventListener('click', () => {
                const marker = markers[ferryAideId];
                if (marker) {
                    map.setCenter(marker.getPosition());
                    map.setZoom(17);
                    console.log(`Map centered on ${boatName}`);
                }
            });

            document.getElementById('buttonsContainer').appendChild(button);
        }

        function removeButton(ferryAideId) {
            const buttonContainer = document.getElementById('buttonsContainer');
            Array.from(buttonContainer.children).forEach(button => {
                if (button.innerText.includes(`Ferry Aide ${ferryAideId}`)) {
                    buttonContainer.removeChild(button);
                }
            });
        }

        function loadScript(url) {
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.src = url;
            document.body.appendChild(script);
        }

        loadScript('');
    </script>

</x-sidebar-layout>
