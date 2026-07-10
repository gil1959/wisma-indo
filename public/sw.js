/* PWA Service Worker (Advanced)
 * - Offline fallback for navigation
 * - Runtime caching strategies:
 *   HTML: Network First
 *   CSS/JS: Stale While Revalidate
 *   Images: Cache First with expiration
 * - Cache versioning + cleanup
 * - Navigation preload (where supported)
 */

const VERSION = "bw-v2";
const CACHE_STATIC = `static-${VERSION}`;
const CACHE_PAGES = `pages-${VERSION}`;
const CACHE_ASSETS = `assets-${VERSION}`;
const CACHE_IMAGES = `images-${VERSION}`;

const OFFLINE_URL = "/offline.html";

// Adjust these if your routes differ
const PRECACHE_URLS = [
    "/",
    "/dokumentasi",
    "/dokumentasi/sewa-kapal",
  "/dokumentasi/umrah",
    "/about",
    "/artikel",
    "/paket-tour",
    OFFLINE_URL,
    "/manifest.webmanifest",
];

// basic helpers
const isNavigationRequest = (req) => req.mode === "navigate";
const isHTML = (req) => req.headers.get("accept")?.includes("text/html");
const isCSSJS = (req) => ["style", "script", "worker"].includes(req.destination);
const isImage = (req) => req.destination === "image";

// cache size limiter
async function trimCache(cacheName, maxItems) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    if (keys.length <= maxItems) return;
    const deletions = keys.slice(0, keys.length - maxItems).map((k) => cache.delete(k));
    await Promise.all(deletions);
}

// strategies
async function networkFirst(request, cacheName, timeoutMs = 4000) {
    const cache = await caches.open(cacheName);

    const networkPromise = new Promise(async (resolve, reject) => {
        try {
            const response = await fetch(request);
            if (response && response.ok) cache.put(request, response.clone());
            resolve(response);
        } catch (e) {
            reject(e);
        }
    });

    const timeoutPromise = new Promise((_, reject) => {
        setTimeout(() => reject(new Error("timeout")), timeoutMs);
    });

    try {
        return await Promise.race([networkPromise, timeoutPromise]);
    } catch (_) {
        const cached = await cache.match(request);
        return cached || null;
    }
}

async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);

    const fetchPromise = fetch(request)
        .then((response) => {
            if (response && response.ok) cache.put(request, response.clone());
            return response;
        })
        .catch(() => null);

    return cached || (await fetchPromise);
}

async function cacheFirst(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response && response.ok) cache.put(request, response.clone());
        return response;
    } catch (_) {
        return null;
    }
}

// install
self.addEventListener("install", (event) => {
    event.waitUntil(
        (async () => {
            const staticCache = await caches.open(CACHE_STATIC);
            const pageCache = await caches.open(CACHE_PAGES);

            // split: keep static + pages separated
            await staticCache.addAll(["/manifest.webmanifest"]);
            await pageCache.addAll(PRECACHE_URLS.filter((u) => u !== "/manifest.webmanifest"));

            self.skipWaiting();
        })()
    );
});

// activate
self.addEventListener("activate", (event) => {
    event.waitUntil(
        (async () => {
            // cleanup old caches
            const keys = await caches.keys();
            await Promise.all(
                keys.map((key) => {
                    const keep = [CACHE_STATIC, CACHE_PAGES, CACHE_ASSETS, CACHE_IMAGES].includes(key);
                    return keep ? Promise.resolve() : caches.delete(key);
                })
            );

            // navigation preload
            if (self.registration.navigationPreload) {
                await self.registration.navigationPreload.enable();
            }

            self.clients.claim();
        })()
    );
});

// fetch
self.addEventListener("fetch", (event) => {
    const { request } = event;

    // ignore non-GET
    if (request.method !== "GET") return;

    event.respondWith(
        (async () => {
            // 1) NAVIGATION: Network First + offline fallback
            if (isNavigationRequest(request) || isHTML(request)) {
                // try navigation preload first (if available)
                const preload = await event.preloadResponse;
                if (preload) return preload;

                const response = await networkFirst(request, CACHE_PAGES, 4500);
                if (response) return response;

                const offline = await caches.match(OFFLINE_URL);
                return offline;
            }

            // 2) CSS/JS: Stale While Revalidate
            if (isCSSJS(request)) {
                const res = await staleWhileRevalidate(request, CACHE_ASSETS);
                return res || fetch(request);
            }

            // 3) IMAGES: Cache First + expiration
            if (isImage(request) || request.url.match(/\.(png|jpg|jpeg|webp|gif|svg)$/i)) {
                const res = await cacheFirst(request, CACHE_IMAGES);
                // keep images cache from growing forever
                trimCache(CACHE_IMAGES, 80);
                if (res) return res;

                // if image fails, return a tiny fallback (optional)
                // (we don't ship a fallback image here; you can add /images/fallback.png later)
                return fetch(request);
            }

            // 4) Other requests: try SWR, fallback network
            const res = await staleWhileRevalidate(request, CACHE_ASSETS);
            return res || fetch(request);
        })()
    );
});

// optional: allow “skipWaiting” from client when you want force update
self.addEventListener("message", (event) => {
    if (event.data && event.data.type === "SKIP_WAITING") {
        self.skipWaiting();
    }
});
