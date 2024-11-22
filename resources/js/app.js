import Alpine from 'alpinejs';
import Axios from 'axios';
import focus from '@alpinejs/focus';
import Cookie from '@conedevelopment/qkie';
import './notifications';
import './theme';
import * as Turbo from '@hotwired/turbo';

// Turbo
window.Turbo = Turbo;

// Alpine
Alpine.plugin(focus);
window.Alpine = Alpine;

// Axios
window.$http = Axios.create({
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-Root-Request': 'true',
    },
});

// Cookie
window.$cookie = new Cookie('__root_');

// Handle the relation frame load event
document.addEventListener('relation-frame-loaded', (event) => {
    if (window.location.href !== event.detail.url) {
        window.history.replaceState(window.history.state, document.title, event.detail.url);
    }
});

// Handle the turbo:frame-missing
document.addEventListener('turbo:frame-missing', (event) => {
    event.preventDefault();
});
