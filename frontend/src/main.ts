import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import { setUnauthorizedHandler } from './lib/axios';
import { useAuthStore } from './stores/auth';
import './style.css';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// When the API rejects a token, drop it and bounce to the login screen.
const auth = useAuthStore();
setUnauthorizedHandler(() => {
    auth.clear();
    if (router.currentRoute.value.name !== 'login') {
        router.push({ name: 'login' });
    }
});

app.mount('#app');
