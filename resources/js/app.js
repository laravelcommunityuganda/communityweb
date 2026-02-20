import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import './bootstrap';

// Import global components
import BaseButton from '@/components/ui/BaseButton.vue';
import BaseInput from '@/components/ui/BaseInput.vue';
import BaseCard from '@/components/ui/BaseCard.vue';
import BaseModal from '@/components/ui/BaseModal.vue';
import BaseDropdown from '@/components/ui/BaseDropdown.vue';
import BaseBadge from '@/components/ui/BaseBadge.vue';
import BaseAvatar from '@/components/ui/BaseAvatar.vue';
import BaseTabs from '@/components/ui/BaseTabs.vue';
import BaseToast from '@/components/ui/BaseToast.vue';
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

// Create Vue app
const app = createApp(App);

// Use plugins
app.use(createPinia());
app.use(router);

// Register global components
app.component('BaseButton', BaseButton);
app.component('BaseInput', BaseInput);
app.component('BaseCard', BaseCard);
app.component('BaseModal', BaseModal);
app.component('BaseDropdown', BaseDropdown);
app.component('BaseBadge', BaseBadge);
app.component('BaseAvatar', BaseAvatar);
app.component('BaseTabs', BaseTabs);
app.component('BaseToast', BaseToast);
app.component('LoadingSpinner', LoadingSpinner);
app.component('EmptyState', EmptyState);

// Global properties
app.config.globalProperties.$filters = {
    currency(value, currency = 'UGX') {
        return new Intl.NumberFormat('en-UG', {
            style: 'currency',
            currency: currency,
        }).format(value);
    },
    number(value) {
        return new Intl.NumberFormat().format(value);
    },
    date(value, options = {}) {
        return new Date(value).toLocaleDateString('en-UG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            ...options,
        });
    },
    time(value, options = {}) {
        return new Date(value).toLocaleTimeString('en-UG', {
            hour: '2-digit',
            minute: '2-digit',
            ...options,
        });
    },
    datetime(value, options = {}) {
        return new Date(value).toLocaleString('en-UG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            ...options,
        });
    },
    relativeTime(value) {
        const date = new Date(value);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return 'just now';
        }

        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) {
            return `${diffInMinutes}m ago`;
        }

        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) {
            return `${diffInHours}h ago`;
        }

        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 7) {
            return `${diffInDays}d ago`;
        }

        const diffInWeeks = Math.floor(diffInDays / 7);
        if (diffInWeeks < 4) {
            return `${diffInWeeks}w ago`;
        }

        const diffInMonths = Math.floor(diffInDays / 30);
        if (diffInMonths < 12) {
            return `${diffInMonths}mo ago`;
        }

        const diffInYears = Math.floor(diffInDays / 365);
        return `${diffInYears}y ago`;
    },
    truncate(value, length = 100) {
        if (!value) return '';
        if (value.length <= length) return value;
        return value.substring(0, length) + '...';
    },
    pluralize(value, count) {
        if (count === 1) return value;
        return value + 's';
    },
};

// Mount app
app.mount('#app');
