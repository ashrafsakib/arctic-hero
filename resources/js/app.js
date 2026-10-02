import './bootstrap';
import '../css/homepage-taxi.css';
import '../css/auth.css';

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
