require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';

import App from './App.vue';

axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest'
};

Vue.use(VueAxios, axios);

import 'vuetify/dist/vuetify.min.css'
import Vuetify from 'vuetify';
Vue.use(Vuetify);

import Mask from 'vue-the-mask'
Vue.use(Mask);

import Routes from './routes';

const router = new VueRouter({ mode: 'history', routes: Routes});
const app = new Vue(Vue.util.extend({ router }, App)).$mount('#app');
