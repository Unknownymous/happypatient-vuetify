import Vue from 'vue';
import Vuetify from '../plugins/vuetify';
import App from './App.vue';
import Vuelidate from 'vuelidate';
import router from './router';

Vue.use(Vuetify);
Vue.use(Vuelidate);

const app = new Vue({
    vuetify: Vuetify,
    el: '#app',
    router,
    render: h => h(App)
});
