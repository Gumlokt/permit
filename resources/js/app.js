// require('./bootstrap');
import { createApp } from 'vue';
import { store } from './store/store';

import App from './components/App.vue';

// createApp(App).use(store).mount("#app");

const app = createApp(App);
app.use(store);
app.mount("#app");
