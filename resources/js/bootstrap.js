// Configuration globale pour Windows
window._ = require('lodash');

/**
 * Configuration jQuery + Bootstrap pour Windows
 */
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    
    // Bootstrap JS
    require('bootstrap');
} catch (e) {
    console.log('Erreur lors du chargement de Bootstrap:', e);
}

/**
 * Configuration Axios pour Windows
 */
window.axios = require('axios');

// Headers par défaut
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuration CSRF pour Laravel
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('Token CSRF non trouvé. Vérifie que la meta tag est présente.');
}

// Configuration spécifique Windows : timeout plus long
window.axios.defaults.timeout = 10000;

// Intercepteur pour gérer les erreurs réseau sur Windows
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.code === 'ECONNABORTED') {
            console.log('Timeout - connexion lente détectée');
        }
        return Promise.reject(error);
    }
);