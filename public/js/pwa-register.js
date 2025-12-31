// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
	window.addEventListener('load', () => {
		// Determine the correct path to sw.js based on manifest link
		const manifestLink = document.querySelector('link[rel="manifest"]');
		let swPath = '/pengaduan/public/sw.js';

		if (manifestLink) {
			const href = manifestLink.getAttribute('href');
			// Get the directory path from manifest.json location
			const baseDir = href.substring(0, href.lastIndexOf('/') + 1) || '';
			swPath = baseDir + 'sw.js';
		}

		navigator.serviceWorker.register(swPath)
			.then((registration) => {
				console.log('[PWA] Service Worker registered successfully:', registration.scope);

				// Check for updates
				registration.addEventListener('updatefound', () => {
					const newWorker = registration.installing;
					console.log('[PWA] New service worker installing...');

					newWorker.addEventListener('statechange', () => {
						if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
							console.log('[PWA] New content available, please refresh.');
						}
					});
				});
			})
			.catch((error) => {
				console.error('[PWA] Service Worker registration failed:', error);
			});
	});
}

// Optional: Handle install prompt for better UX
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
	console.log('[PWA] Install prompt available');
	// Prevent the mini-infobar from appearing on mobile
	e.preventDefault();
	// Stash the event so it can be triggered later
	deferredPrompt = e;

	// Optionally show your own install button/banner here
	// showInstallPromotion();
});

window.addEventListener('appinstalled', () => {
	console.log('[PWA] App was installed');
	deferredPrompt = null;
});
