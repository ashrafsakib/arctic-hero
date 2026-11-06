<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<div class="grid gap-5 sm:grid-cols-2">
    <label class="text-sm font-medium text-slate-700">Location name <span class="text-red-700" aria-hidden="true">*</span><input class="mt-1 block min-h-11 w-full border-slate-300" name="name" value="{{ old('name', $location->name) }}" maxlength="255" required></label>
    <label class="text-sm font-medium text-slate-700">Category <select class="mt-1 block min-h-11 w-full border-slate-300" name="category" required>@foreach ($categories as $category)<option value="{{ $category }}" @selected(old('category', $location->category) === $category)>{{ \Illuminate\Support\Str::headline($category) }}</option>@endforeach</select></label>
    <label class="text-sm font-medium text-slate-700 sm:col-span-2">Address <input class="mt-1 block min-h-11 w-full border-slate-300" name="address" value="{{ old('address', $location->address) }}" maxlength="255"></label>
    <label class="text-sm font-medium text-slate-700 sm:col-span-2">Description <textarea class="mt-1 block w-full border-slate-300" name="description" rows="3" maxlength="2000">{{ old('description', $location->description) }}</textarea></label>
    <label class="text-sm font-medium text-slate-700">Latitude <input id="location-latitude" class="mt-1 block min-h-11 w-full border-slate-300" name="latitude" type="number" min="64.5" max="65.5" step="0.0000001" value="{{ old('latitude', $location->latitude) }}" aria-describedby="location-coordinate-help location-coordinate-status"></label>
    <label class="text-sm font-medium text-slate-700">Longitude <input id="location-longitude" class="mt-1 block min-h-11 w-full border-slate-300" name="longitude" type="number" min="24.8" max="26" step="0.0000001" value="{{ old('longitude', $location->longitude) }}" aria-describedby="location-coordinate-help location-coordinate-status"></label>
    <p id="location-coordinate-help" class="text-sm text-slate-700 sm:col-span-2">Choose a point on the map or enter coordinates directly. To place the map pin at entered coordinates, use the button below. Active public locations require a complete coordinate pair inside the supported Oulu service area.</p>
    <div id="location-pin-map" class="h-96 border border-slate-300 sm:col-span-2" role="region" aria-label="Map preview for the location pin"></div>
    <div class="flex flex-wrap items-center gap-3 sm:col-span-2">
        <button id="location-set-pin" class="min-h-11 border border-slate-400 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50" type="button" hidden>Set map pin from coordinates</button>
        <p id="location-coordinate-status" class="text-sm text-slate-700" aria-live="polite" aria-atomic="true">If the map is unavailable, coordinates can still be entered manually.</p>
    </div>
    <label class="text-sm font-medium text-slate-700">Status <select class="mt-1 block min-h-11 w-full border-slate-300" name="status" required><option value="active" @selected(old('status', $location->status) === 'active')>Active</option><option value="inactive" @selected(old('status', $location->status) === 'inactive')>Inactive</option></select></label>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    (() => {
        const mapElement = document.getElementById('location-pin-map');
        const latitudeInput = document.getElementById('location-latitude');
        const longitudeInput = document.getElementById('location-longitude');
        const setPinButton = document.getElementById('location-set-pin');
        const statusElement = document.getElementById('location-coordinate-status');
        if (!mapElement || !latitudeInput || !longitudeInput || !setPinButton || !statusElement) return;

        if (typeof L === 'undefined') {
            statusElement.textContent = 'The interactive map is unavailable. Enter the latitude and longitude manually.';
            return;
        }

        const ouluCenter = [65.0141, 25.4698];
        const map = L.map(mapElement, { keyboard: true, scrollWheelZoom: false }).setView(ouluCenter, 12);
        const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        tiles.on('tileerror', () => {
            statusElement.textContent = 'Map tiles could not be loaded. Coordinates can still be entered and saved manually.';
        });

        let marker = null;
        const isInServiceArea = (latitude, longitude) =>
            latitude >= 64.5 && latitude <= 65.5 && longitude >= 24.8 && longitude <= 26;
        const setPin = (latitude, longitude, zoom = false) => {
            const point = [latitude, longitude];
            if (marker) marker.setLatLng(point);
            else {
                marker = L.marker(point, {
                    alt: 'Location pin',
                    draggable: true,
                    keyboard: true,
                    title: 'Location pin. Use the coordinate fields to position it precisely.'
                }).addTo(map);
                marker.on('dragend', () => {
                    const position = marker.getLatLng();
                    if (!isInServiceArea(position.lat, position.lng)) {
                        const previousLatitude = Number(latitudeInput.value);
                        const previousLongitude = Number(longitudeInput.value);
                        marker.setLatLng([previousLatitude, previousLongitude]);
                        statusElement.textContent = 'The pin must stay inside the supported Oulu service area.';
                        return;
                    }

                    latitudeInput.value = position.lat.toFixed(7);
                    longitudeInput.value = position.lng.toFixed(7);
                    statusElement.textContent = 'Map pin coordinates updated.';
                });
            }

            latitudeInput.value = latitude.toFixed(7);
            longitudeInput.value = longitude.toFixed(7);
            if (zoom) map.setView(point, 15);
        };

        const hasInitialCoordinates = latitudeInput.value.trim() !== '' && longitudeInput.value.trim() !== '';
        const initialLatitude = Number(latitudeInput.value);
        const initialLongitude = Number(longitudeInput.value);
        if (hasInitialCoordinates && isInServiceArea(initialLatitude, initialLongitude)) {
            setPin(initialLatitude, initialLongitude, true);
        }

        setPinButton.hidden = false;
        setPinButton.addEventListener('click', () => {
            const latitude = Number(latitudeInput.value);
            const longitude = Number(longitudeInput.value);
            if (latitudeInput.value.trim() === '' || longitudeInput.value.trim() === '' || !isInServiceArea(latitude, longitude)) {
                statusElement.textContent = 'Enter a complete coordinate pair inside the supported Oulu service area.';
                (latitudeInput.value.trim() === '' || !Number.isFinite(latitude) || latitude < 64.5 || latitude > 65.5
                    ? latitudeInput
                    : longitudeInput).focus();
                return;
            }

            setPin(latitude, longitude, true);
            statusElement.textContent = 'Map pin set from the entered coordinates.';
        });

        map.on('click', (event) => {
            if (!isInServiceArea(event.latlng.lat, event.latlng.lng)) {
                statusElement.textContent = 'Choose a point inside the supported Oulu service area.';
                return;
            }

            setPin(event.latlng.lat, event.latlng.lng);
            statusElement.textContent = 'Map pin set. You can refine the position with the coordinate fields.';
        });

        mapElement.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && document.activeElement === mapElement) {
                event.preventDefault();
                statusElement.textContent = 'Use the coordinate fields or click the map to set the pin.';
            }
        });
        statusElement.textContent = 'Map ready. Click to set the pin, drag it to refine, or enter coordinates directly.';
    })();
</script>
