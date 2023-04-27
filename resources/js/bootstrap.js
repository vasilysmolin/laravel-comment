import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]').content;

import {createApp} from 'vue/dist/vue.esm-bundler.js'
import {BootstrapVue3} from 'bootstrap-vue-3'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css'

const app = createApp({});
app.use(BootstrapVue3);
import Review from './components/Review.vue';
app.component('review-component', Review);
app.mount('#review-component');
