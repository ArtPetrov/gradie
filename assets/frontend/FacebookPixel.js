!function (f, b, e, v, n, t, s) {
    if (f.fbq) return;
    n = f.fbq = function () {
        n.callMethod ?
            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
    };
    if (!f._fbq) f._fbq = n;
    n.push = n;
    n.loaded = !0;
    n.version = '2.0';
    n.queue = [];
    t = b.createElement(e);
    t.async = !0;
    t.src = v;
    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s)
}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

function fbqTrack(action, params) {
    params = params || null;
    if (typeof fbq !== "undefined") {
        console.log('FB: ', action, ' Params: ', params);
        return params === null ? fbq('track', action) : fbq('track', action, params);

    }
    return false;
}

fbq('init', '2445981082181052');
fbqTrack('PageView');

// add to cart
window.addEventListener('AddToCart', (e) => {
    fbqTrack('AddToCart');
});

// registration completed
window.addEventListener('CompleteRegistration', (e) => {
    fbqTrack('CompleteRegistration');
});

// init start order
window.addEventListener('InitiateCheckout', (e) => {
    fbqTrack('InitiateCheckout');
});

// Buy order
window.addEventListener('Purchase', (e) => {
    fbqTrack('Purchase', {value: 0.00, currency: 'USD'});
});

// Make require design-projects
window.addEventListener('Lead', (e) => {
    fbqTrack('Lead');
});

// Search
window.addEventListener('Search', (e) => {
    fbqTrack('Search');
});

// View works
window.addEventListener('ViewContent', (e) => {
    fbqTrack('ViewContent');
});
