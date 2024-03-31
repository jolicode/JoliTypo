/**************************************************** WORKBOX IMPORT ****************************************************/
importScripts('/workbox/workbox-sw.js');
workbox.setConfig({modulePathPrefix: '/workbox'});
/**************************************************** END WORKBOX IMPORT ****************************************************/

/**************************************************** CACHE STRATEGY ****************************************************/
//Static assets
const cache_0_0 = new workbox.strategies.CacheFirst({
    cacheName: 'assets',plugins: [new workbox.cacheableResponse.CacheableResponsePlugin({
        "statuses": [
            0,
            200
        ]
    }), new workbox.expiration.ExpirationPlugin({
        "maxEntries": 61,
        "maxAgeSeconds": 31536000
    })]
});
workbox.routing.registerRoute(({url, request}) => url.pathname.startsWith('/build') || request.destination === 'font' || request.destination === 'image' || request.destination === 'style' || request.destination === 'script' || request.destination === 'manifest',cache_0_0);

/**************************************************** CACHE CLEAR ****************************************************/
// The configuration is set to clear all caches on each install event
self.addEventListener("install", function (event) {
    event.waitUntil(caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (cacheName) {
                    return caches.delete(cacheName);
                })
            );
        })
    );
});
/**************************************************** END CACHE CLEAR ****************************************************/

/**************************************************** OFFLINE FALLBACK ****************************************************/
// The configuration is set to use the network only strategy by default
workbox.routing.setDefaultHandler(new workbox.strategies.NetworkOnly());
workbox.recipes.offlineFallback({
    "pageFallback": "/"
});
/**************************************************** END OFFLINE FALLBACK ****************************************************/


/**************************************************** SKIP WAITING ****************************************************/
// The configuration is set to skip waiting on each install event
self.addEventListener("install", function (event) {
    event.waitUntil(self.skipWaiting());
});
self.addEventListener("activate", function (event) {
    event.waitUntil(self.clients.claim());
});
/**************************************************** END SKIP WAITING ****************************************************/
