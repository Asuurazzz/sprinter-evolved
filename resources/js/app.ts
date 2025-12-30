import '../css/app.css';

import { createPinia } from 'pinia';
import { PiniaColada } from '@pinia/colada';
import { createApp } from 'vue';
import { router } from './router';
import App from './App.vue';
import { initializeTheme } from './composables/useAppearance';

const pinia = createPinia();
pinia.use(PiniaColada);

const app = createApp(App);

app.use(pinia);
app.use(router);

app.mount('#app');

// Inicializa o tema (light/dark mode)
initializeTheme();
