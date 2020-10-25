import Vue from 'vue';
import Vuetify from '../plugins/vuetify';
import App from './App.vue';

Vue.use(Vuetify);

const app = new Vue({
    vuetify: Vuetify,
    el: '#app',
    render: h => h(App)
});
