<x-sidebar-layout>
    <x-slot:heading>
        Map
    </x-slot:heading>

    <h1>This is the Map page.</h1>
    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <div id="map" style="height: 500px;"></div>
<button id="resetMapButton" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
  Reset to Marker
</button>

<script>
  let map;
  let markers = {}; // Store markers by ferry_aide_id
  let defaultMarkerPosition = null; // To store the initial/default marker position
  let isMarkerSet = false; // Flag to check if the default marker has been set

  function initMap() {
      // Default center coordinates (you can change this to fit the area)
      var defaultCenter = { lat: 14.56241840, lng: 121.08076320 };

      map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: defaultCenter,
      });

      // Fetch initial data and start polling
      fetchAndDisplayMarkers();
      setInterval(fetchAndDisplayMarkers, 10000); // Update every 10 seconds
  }

  function fetchAndDisplayMarkers() {
      console.log('Fetching ferry aide locations...'); // Log that fetch has started

      fetch('{{ route('ferry-aide.locations') }}') // Use the named route defined in web.php
          .then(response => {
              console.log('Response received:', response); // Log the entire response
              return response.json(); // Convert response to JSON
          })
          .then(data => {
                console.log('Fetched data:', data); // Log the data received

                if (data.length > 0) {
                    data.forEach((location, index) => {
                        const ferryAideId = location.ferry_aide_id;
                        const position = { 
                            lat: parseFloat(location.latitude), // Ensure lat is a float
                            lng: parseFloat(location.longitude)  // Ensure lng is a float
                        };

                        // If this is the first marker, set it as the default marker position
                        if (!isMarkerSet && index === 0) {
                            defaultMarkerPosition = position;
                            isMarkerSet = true; // Mark that the default marker has been set
                            console.log('Default marker set at:', defaultMarkerPosition); // Log default marker position
                        }

                        // Check if marker already exists for the ferry aide
                        if (markers[ferryAideId]) {
                            // Update marker position
                            markers[ferryAideId].setPosition(position);
                            console.log(`Marker updated for Ferry Aide ID: ${ferryAideId} at position`, position);
                        } else {
                            // Create new marker for this ferry aide
                            const marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                icon: {
                                    url: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', // Custom icon
                                    scaledSize: new google.maps.Size(40, 40) // Resize icon
                                }
                            });

                            // Store the marker in the markers object
                            markers[ferryAideId] = marker;
                            console.log(`New marker created for Ferry Aide ID: ${ferryAideId} at position`, position);
                        }
                    });
                } else {
                    console.warn('No locations available'); // Log if no data is returned
                }
            })
          .catch(error => {
              console.error('Error fetching data:', error); // Log any errors that occur during the fetch
          });
  }

  // Event listener for the reset button
  document.getElementById('resetMapButton').addEventListener('click', function() {
      if (isMarkerSet && defaultMarkerPosition) {
          // Reset the map to the default marker position and zoom level
          map.setCenter(defaultMarkerPosition);
          map.setZoom(15); // Adjust zoom as needed
          console.log('Map reset to default marker position:', defaultMarkerPosition); // Log when map is reset
      } else {
          alert('No default marker position set yet!');
          console.warn('Attempted to reset map but no default marker is set.');
      }
  });

  // Load the Google Maps script asynchronously
  function loadScript(url) {
      var script = document.createElement("script");
      script.type = "text/javascript";
      script.src = url;
      document.body.appendChild(script);
  }

  // Add your Google Maps API Key here
  loadScript('https://maps.googleapis.com/maps/api/js?key=AIzaSyDa8BUEO6XgsEKaaougduKoBBKL-7x1LBQ&callback=initMap');
</script>

</x-sidebar-layout>
