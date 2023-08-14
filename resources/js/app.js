import Alpine from 'alpinejs';
import Axios from 'axios';
import focus from '@alpinejs/focus';

// Alpine
Alpine.plugin(focus);
window.Alpine = Alpine;

// Axios
window.$http = Axios.create({
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});
