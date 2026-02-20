import axios from 'axios';

// Configure axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;
axios.defaults.baseURL = import.meta.env.VITE_API_URL || '/api';

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Add request interceptor for authentication
axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add response interceptor for error handling
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    // Unauthorized - redirect to login
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                    break;
                case 403:
                    // Forbidden - show error message
                    console.error('Access denied');
                    break;
                case 404:
                    // Not found
                    console.error('Resource not found');
                    break;
                case 422:
                    // Validation error - return errors
                    break;
                case 429:
                    // Rate limited
                    console.error('Too many requests');
                    break;
                case 500:
                    // Server error
                    console.error('Server error');
                    break;
            }
        }
        return Promise.reject(error);
    }
);

// Echo initialization for real-time events (optional)
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';

// window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true,
//     auth: {
//         headers: {
//             Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
//         },
//     },
// });

export { axios };
