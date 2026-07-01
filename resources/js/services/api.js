import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: { 'Accept': 'application/json' },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('verification_token');
    if (token) {
        config.headers['X-Verification-Token'] = token;
    }
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const isOtpRoute = error.config.url?.includes('/auth/');
            if (!isOtpRoute) {
                localStorage.removeItem('verification_token');
                window.location.href = '/verify';
            }
        }
        if (error.response?.status === 429) {
            alert('Terlalu banyak percobaan. Silakan tunggu beberapa saat.');
        }
        return Promise.reject(error);
    }
);

export default api;
