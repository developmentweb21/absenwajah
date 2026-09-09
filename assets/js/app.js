// app.js - for PWA and basic JS setup
console.log('App.js loaded. Ready for PWA.');

// You can add global event listeners or service logic here
window.addEventListener('load', () => {
    console.log('Window loaded');
    
    // Example: confirm service worker is active
    if ('serviceWorker' in navigator) {
        console.log('Service Worker is supported.');

        navigator.serviceWorker.ready.then(registration => {
            console.log('Service Worker is active:', registration);
        }).catch(error => {
            console.error('Service Worker ready error:', error);
        });
    }
});
