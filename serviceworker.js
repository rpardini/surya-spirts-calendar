// This is the "Offline copy of pages" service worker
const CACHE = "offline-1";

self.addEventListener("install", function (event) {
    console.log("[PWA Builder] Install Event processing");
    event.waitUntil(caches.open(CACHE).then(function (cache) {
        console.log("[PWA Builder] Cached offline page during install: /?pwa=true");
        return cache.add("/?pwa=true");
    }));
});

// If any fetch fails, it will look for the request in the cache and serve it from there first
self.addEventListener("fetch", function (event) {
    if (event.request.method !== "GET") return;

    event.respondWith(fetch(event.request)
        .then(function (response) {
            console.log("[PWA Builder] add page to offline cache: " + response.url);

            // If request was success, add or update it in the cache
            event.waitUntil(updateCache(event.request, response.clone()));

            return response;
        })
        .catch(function (error) {
            console.log("[PWA Builder] Network request Failed. Serving content from cache: " + error);
            return fromCache(event.request);
        }));
});

function fromCache(request) {
    // Check to see if you have it in the cache
    // Return response
    // If not in the cache, then return error page
    return caches.open(CACHE).then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                return Promise.reject("no-match");
            }
            return matching;
        });
    });
}

function updateCache(request, response) {
    return caches.open(CACHE).then(function (cache) {
        return cache.put(request, response);
    });
}
