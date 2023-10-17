import Alpine from 'alpinejs';
import Axios from 'axios';
import focus from '@alpinejs/focus';
import Cookie from '@conedevelopment/qkie';
import './notifications';
import './theme';

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
window.$cookie = new Cookie();
