import './bootstrap';
import '../css/homepage-taxi.css';
import '../css/auth.css';
import '../css/dashboard.css';
import '../css/booking.css';
import '../css/fleet-page.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const revealTargets = document.querySelectorAll(
	'.hero-copy, .service-card, .company-collage, .company-copy, .fleet-card, .booking-promo, .booking-card, .locations-copy, .location-photo, .contact-band'
);

if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
	if ('IntersectionObserver' in window) {
		const revealObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -5% 0px' });

		revealTargets.forEach((element, index) => {
			element.classList.add('reveal');
			if (element.classList.contains('feature-card')) {
				element.style.setProperty('--reveal-delay', `${(index % 3) * 90}ms`);
			}
			revealObserver.observe(element);
		});
	} else {
		revealTargets.forEach((element) => element.classList.add('is-visible'));
	}
}

const bookingForm = document.querySelector('[data-booking-form]');

if (bookingForm) {
	const steps = [...bookingForm.querySelectorAll('[data-booking-step]')];
	const progressItems = [...document.querySelectorAll('[data-progress-step]')];
	const pickupInput = bookingForm.elements.pickup_address;
	const destinationInput = bookingForm.elements.destination_address;
	const pickupLat = bookingForm.elements.pickup_lat;
	const pickupLng = bookingForm.elements.pickup_lng;
	const destinationLat = bookingForm.elements.destination_lat;
	const destinationLng = bookingForm.elements.destination_lng;
	const vehicleTypes = JSON.parse(document.querySelector('[data-vehicle-types]').textContent);
	const locations = JSON.parse(document.querySelector('[data-locations]').textContent);
	const state = { step: 1, activePoint: 'pickup', map: null, markers: {}, route: null };

	const number = (value) => Number.parseFloat(value || '0');
	const money = (value) => `€${Number(value).toFixed(2)}`;
	const distanceKm = () => {
		if (!pickupLat.value || !destinationLat.value) return 0;
		const radians = (degrees) => degrees * Math.PI / 180;
		const latitude1 = radians(number(pickupLat.value));
		const latitude2 = radians(number(destinationLat.value));
		const deltaLatitude = latitude2 - latitude1;
		const deltaLongitude = radians(number(destinationLng.value) - number(pickupLng.value));
		const value = Math.sin(deltaLatitude / 2) ** 2 + Math.cos(latitude1) * Math.cos(latitude2) * Math.sin(deltaLongitude / 2) ** 2;
		return Math.max(1, 6371 * 2 * Math.atan2(Math.sqrt(value), Math.sqrt(1 - value)) * 1.25);
	};
	const selectedVehicle = () => vehicleTypes.find((vehicle) => String(vehicle.id) === bookingForm.elements.vehicle_type_id.value);
	const updateFare = () => {
		const vehicle = selectedVehicle();
		if (!vehicle) return;
		const rawDistance = distanceKm();
		const distance = Number(rawDistance.toFixed(2));
		const minutes = Math.ceil(distance / 35 * 60);
		const distanceFare = distance * Number(vehicle.per_km_rate);
		const timeFare = minutes * Number(vehicle.per_minute_rate);
		const total = Number(vehicle.base_fare) + distanceFare + timeFare;
		bookingForm.querySelectorAll('[data-fare-base]').forEach((node) => node.textContent = money(vehicle.base_fare));
		bookingForm.querySelectorAll('[data-fare-distance]').forEach((node) => node.textContent = distance ? `${money(distanceFare)} · ${distance.toFixed(1)} km` : 'Choose both map points');
		bookingForm.querySelectorAll('[data-fare-time]').forEach((node) => node.textContent = distance ? `${money(timeFare)} · ${minutes} min` : '—');
		bookingForm.querySelectorAll('[data-fare-total]').forEach((node) => node.textContent = money(total));
		bookingForm.querySelectorAll('[data-fare-route]').forEach((node) => node.textContent = distance ? `${distance.toFixed(1)} km · about ${minutes} min` : 'Set pickup and destination on the map');
	};
	const updateVehicles = () => {
		const passengers = Number(bookingForm.elements.passengers.value);
		const luggage = Number(bookingForm.elements.luggage_count.value);
		let firstAvailable = null;
		bookingForm.querySelectorAll('[data-vehicle-option]').forEach((option) => {
			const input = option.querySelector('input');
			const vehicle = vehicleTypes.find((item) => String(item.id) === input.value);
			const available = vehicle.passenger_capacity >= passengers && vehicle.luggage_capacity >= luggage;
			option.hidden = !available;
			input.disabled = !available;
			if (available && !firstAvailable) firstAvailable = input;
		});
		const currentVehicle = selectedVehicle();
		if (!currentVehicle || currentVehicle.passenger_capacity < passengers) {
			if (firstAvailable) firstAvailable.checked = true;
		}
		updateFare();
	};
	const updateReview = () => {
		const vehicle = selectedVehicle();
		bookingForm.querySelector('[data-review-pickup]').textContent = pickupInput.value;
		bookingForm.querySelector('[data-review-destination]').textContent = destinationInput.value;
		bookingForm.querySelector('[data-review-date]').textContent = `${bookingForm.elements.booking_date.value} at ${bookingForm.elements.booking_time.value}`;
		const passengers = Number(bookingForm.elements.passengers.value);
		bookingForm.querySelector('[data-review-passengers]').textContent = `${passengers} ${passengers === 1 ? 'passenger' : 'passengers'} · ${bookingForm.elements.luggage_count.value} bags`;
		bookingForm.querySelector('[data-review-vehicle]').textContent = vehicle ? vehicle.name : '';
		updateFare();
	};
	const setStep = (step) => {
		state.step = step;
		steps.forEach((panel) => panel.hidden = Number(panel.dataset.bookingStep) !== step);
		progressItems.forEach((item) => {
			const itemStep = Number(item.dataset.progressStep);
			item.classList.toggle('is-current', itemStep === step);
			item.classList.toggle('is-complete', itemStep < step);
		});
		if (step === 2) updateVehicles();
		if (step === 3) updateReview();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	};
	const validateStep = (step) => {
		const panel = steps.find((item) => Number(item.dataset.bookingStep) === step);
		const message = panel.querySelector('[data-step-error]');
		let error = '';
		if (step === 1) {
			if (!pickupInput.value.trim() || !destinationInput.value.trim()) error = 'Add both your pickup and destination.';
			else if (!pickupLat.value || !destinationLat.value) error = 'Select both points on the map to calculate your fare.';
			else if (!bookingForm.elements.booking_date.value || !bookingForm.elements.booking_time.value) error = 'Choose a pickup date and time.';
		} else if (step === 2 && !selectedVehicle()) {
			error = 'Choose a vehicle that fits your group.';
		}
		message.textContent = error;
		return !error;
	};
	const pointInputs = { pickup: [pickupInput, pickupLat, pickupLng], destination: [destinationInput, destinationLat, destinationLng] };
	const updateRoute = () => {
		const points = Object.values(state.markers).filter(Boolean).map((marker) => marker.getLatLng());
		if (state.route) state.map.removeLayer(state.route);
		if (points.length === 2) state.route = window.L.polyline(points, { color: '#c6ad00', weight: 4, dashArray: '7 7' }).addTo(state.map);
		updateFare();
	};
	const setPoint = (kind, latitude, longitude, label) => {
		const [input, latitudeInput, longitudeInput] = pointInputs[kind];
		input.value = label;
		latitudeInput.value = Number(latitude).toFixed(7);
		longitudeInput.value = Number(longitude).toFixed(7);
		const point = [Number(latitude), Number(longitude)];
		if (state.markers[kind]) state.markers[kind].setLatLng(point);
		else state.markers[kind] = window.L.marker(point, { draggable: true }).addTo(state.map);
		state.markers[kind].off('dragend').on('dragend', (event) => {
			const position = event.target.getLatLng();
			latitudeInput.value = position.lat.toFixed(7);
			longitudeInput.value = position.lng.toFixed(7);
			input.value = `${kind === 'pickup' ? 'Pickup' : 'Destination'} (${position.lat.toFixed(4)}, ${position.lng.toFixed(4)})`;
			updateRoute();
		});
		updateRoute();
	};

	bookingForm.querySelectorAll('[data-next-step]').forEach((button) => button.addEventListener('click', () => {
		if (validateStep(state.step)) setStep(state.step + 1);
	}));
	bookingForm.querySelectorAll('[data-previous-step]').forEach((button) => button.addEventListener('click', () => setStep(state.step - 1)));
	bookingForm.querySelectorAll('input[name="vehicle_type_id"]').forEach((input) => input.addEventListener('change', updateFare));
	bookingForm.elements.passengers.addEventListener('change', updateVehicles);
	bookingForm.elements.luggage_count.addEventListener('change', updateVehicles);
	const setActivePoint = (kind) => {
		state.activePoint = kind;
		bookingForm.querySelector('[data-select-pickup]').setAttribute('aria-pressed', String(kind === 'pickup'));
		bookingForm.querySelector('[data-select-pickup]').textContent = kind === 'pickup' ? 'Selected for map' : 'Set on map';
		bookingForm.querySelector('[data-select-destination]').setAttribute('aria-pressed', String(kind === 'destination'));
		bookingForm.querySelector('[data-select-destination]').textContent = kind === 'destination' ? 'Selected for map' : 'Set on map';
	};
	bookingForm.querySelector('[data-select-pickup]').addEventListener('click', () => setActivePoint('pickup'));
	bookingForm.querySelector('[data-select-destination]').addEventListener('click', () => setActivePoint('destination'));
	[pickupInput, destinationInput].forEach((input) => input.addEventListener('input', () => {
		const match = locations.find((location) => location.address.toLowerCase() === input.value.trim().toLowerCase());
		const kind = input === pickupInput ? 'pickup' : 'destination';
		if (match) setPoint(kind, match.latitude, match.longitude, match.address);
		else {
			const [, latitudeInput, longitudeInput] = pointInputs[kind];
			latitudeInput.value = '';
			longitudeInput.value = '';
			if (state.markers[kind]) {
				state.map.removeLayer(state.markers[kind]);
				delete state.markers[kind];
			}
			updateRoute();
		}
	}));

	if (window.L && document.getElementById('booking-map')) {
		state.map = window.L.map('booking-map').setView([65.0124, 25.4651], 12);
		window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; OpenStreetMap contributors',
			maxZoom: 19,
		}).addTo(state.map);
		locations.forEach((location) => window.L.circleMarker([location.latitude, location.longitude], { radius: 4, color: '#171714', fillColor: '#f5df00', fillOpacity: 1 }).addTo(state.map));
		state.map.on('click', (event) => setPoint(state.activePoint, event.latlng.lat, event.latlng.lng, `${state.activePoint === 'pickup' ? 'Pickup' : 'Destination'} (${event.latlng.lat.toFixed(4)}, ${event.latlng.lng.toFixed(4)})`));
		if (pickupLat.value && pickupLng.value) setPoint('pickup', pickupLat.value, pickupLng.value, pickupInput.value);
		if (destinationLat.value && destinationLng.value) setPoint('destination', destinationLat.value, destinationLng.value, destinationInput.value);
		locations.forEach((location) => {
			if (pickupInput.value === location.address && !pickupLat.value) setPoint('pickup', location.latitude, location.longitude, location.address);
			if (destinationInput.value === location.address && !destinationLat.value) setPoint('destination', location.latitude, location.longitude, location.address);
		});
	}

	updateVehicles();
	setStep(1);
}
