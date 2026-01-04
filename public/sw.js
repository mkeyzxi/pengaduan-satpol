const CACHE_NAME = 'pengaduan-satpolpp-v1';
const OFFLINE_URL = '/pengaduan/public/offline.html';

// Only explicit static assets (avoid caching directory roots or dynamic PHP responses)
const STATIC_ASSETS = [
  '/pengaduan/index.php',
    '/pengaduan/public/offline.html',
    '/pengaduan/public/style/style.css',
    '/pengaduan/public/js/pwa-register.js',
    '/pengaduan/public/icons/icon-192x192.png',
    '/pengaduan/public/icons/icon-512x512.png',
    '/pengaduan/public/manifest.json',
    OFFLINE_URL
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[ServiceWorker] Install');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[ServiceWorker] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[ServiceWorker] Activate');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[ServiceWorker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - Network first, fallback to cache
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // Skip API requests (PHP requests that should always be fresh)
    if (event.request.url.includes('/app/')) {
        return;
    }
    // Special handling for navigation requests (pages)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Only cache successful navigation responses
                    if (response && response.ok) {
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, responseToCache));
                    }
                    return response;
                })
                .catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Default network-first for other GET requests, but only cache successful responses
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (!response || !response.ok) {
                    // Do not cache unsuccessful responses
                    return response;
                }

                const responseToCache = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseToCache);
                });

                return response;
            })
            .catch(() => {
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) return cachedResponse;
                    return new Response('Offline', {
                        status: 503,
                        statusText: 'Service Unavailable'
                    });
                });
            })
    );
});
