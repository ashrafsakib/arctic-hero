<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<div class="grid gap-5 sm:grid-cols-2">
    <label class="text-sm font-medium text-slate-700">Location name <span class="text-red-700">*</span><input class="mt-1 block min-h-11 w-full border-slate-300" name="name" value="{{ old('name', $location->name) }}" maxlength="255" required></label>
    <label class="text-sm font-medium text-slate-700">Category <select class="mt-1 block min-h-11 w-full border-slate-300" name="category" required>@foreach ($categories as $category)<option value="{{ $category }}" @selected(old('category', $location->category) === $category)>{{ \Illuminate\Support\Str::headline($category) }}</option>@endforeach</select></label>
    <label class="text-sm font-medium text-slate-700 sm:col-span-2">Address <input class="mt-1 block min-h-11 w-full border-slate-300" name="address" value="{{ old('address', $location->address) }}" maxlength="255"></label>
    <label class="text-sm font-medium text-slate-700 sm:col-span-2">Description <textarea class="mt-1 block w-full border-slate-300" name="description" rows="3" maxlength="2000">{{ old('description', $location->description) }}</textarea></label>
    <label class="text-sm font-medium text-slate-700">Latitude <input id="location-latitude" class="mt-1 block min-h-11 w-full border-slate-300" name="latitude" type="number" min="64.5" max="65.5" step="0.0000001" value="{{ old('latitude', $location->latitude) }}"></label>
    <label class="text-sm font-medium text-slate-700">Longitude <input id="location-longitude" class="mt-1 block min-h-11 w-full border-slate-300" name="longitude" type="number" min="24.8" max="26" step="0.0000001" value="{{ old('longitude', $location->longitude) }}"></label>
    <p class="text-xs text-slate-600 sm:col-span-2">Select or drag the pin on the map. Active public locations require a complete coordinate pair inside the supported Oulu service area.</p>
    <div id="location-pin-map" class="h-96 border border-slate-300 sm:col-span-2" role="application" aria-label="Map for choosing a location pin"></div>
    <label class="text-sm font-medium text-slate-700">Status <select class="mt-1 block min-h-11 w-full border-slate-300" name="status" required><option value="active" @selected(old('status', $location->status) === 'active')>Active</option><option value="inactive" @selected(old('status', $location->status) === 'inactive')>Inactive</option></select></label>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    (() => {
        const mapElement = document.getElementById('location-pin-map');
        const latitudeInput = document.getElementById('location-latitude');
        const longitudeInput = document.getElementById('location-longitude');
        if (!mapElement || !latitudeInput || !longitudeInput || typeof L === 'undefined') return;

        const ouluCenter = [65.0141, 25.4698];
        const map = L.map(mapElement).setView(ouluCenter, 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;
        const setPin = (latitude, longitude) => {
            const point = [latitude, longitude];
            if (marker) marker.setLatLng(point);
            else marker = L.marker(point, { draggable: true }).addTo(map);
            marker.on('dragend', () => {
                const position = marker.getLatLng();
                latitudeInput.value = position.lat.toFixed(7);
                longitudeInput.value = position.lng.toFixed(7);
            });
        };

        const initialLatitude = Number(latitudeInput.value);
        const initialLongitude = Number(longitudeInput.value);
        if (Number.isFinite(initialLatitude) && Number.isFinite(initialLongitude) && initialLatitude && initialLongitude) {
            setPin(initialLatitude, initialLongitude);
            map.setView([initialLatitude, initialLongitude], 15);
        }

        map.on('click', (event) => {
            setPin(event.latlng.lat, event.latlng.lng);
            latitudeInput.value = event.latlng.lat.toFixed(7);
            longitudeInput.value = event.latlng.lng.toFixed(7);
        });
    })();
</script>
