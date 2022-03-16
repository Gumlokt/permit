import { createRouter, createWebHistory } from 'vue-router';
import Printer from '../components/Printer.vue';

const routes = [
  {
    path: '/print',
    name: 'Print',
    component: Printer
  }
];

export const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});
