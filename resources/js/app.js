// require('./bootstrap');
import { createApp } from 'vue';
import { store } from './store/store';
import { router } from './router/router';

import App from './components/App.vue';

const app = createApp(App);
app.use(store);
app.use(router);
app.mount("#app");
