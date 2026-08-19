// ======================================================
// Service Worker PWA - Strategy: Network Only (No Cache)
// Menjamin aplikasi PWA selalu mengambil data & tampilan terbaru dari server
// tanpa menyimpan cache lokal di penyimpanan HP pengguna.
// ======================================================

// Saat Service Worker di-install, lewati pemuatan cache
self.addEventListener('install', event => {
  self.skipWaiting();
});

// Saat Service Worker aktif, bersihkan SEMUA cache lama di device pengguna
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => caches.delete(cache))
      );
    }).then(() => self.clients.claim())
  );
});

// Setiap request selalu ambil langsung dari server/network tanpa menyimpan cache
self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  event.respondWith(
    fetch(event.request, { cache: 'no-store' })
      .catch(() => fetch(event.request))
  );
});
