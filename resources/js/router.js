import Vue from 'vue';
import Router from 'vue-router';
import PatientCreate from './views/patient/PatientCreate.vue';
import PatientIndex from './views/patient/PatientIndex.vue';
Vue.use(Router);


const routes = [
  {
    path: '/dashboard',
    name: 'dashboard',
    // component: PatientCreate
  },
  {
    path: '/transactions',
    name: 'transactions',
    // component: PatientCreate
  },
  {
    path: '/patient/create',
    name: 'patient.create',
    component: PatientCreate
  },
  {
    path: '/patient/index',
    name: 'patient.index',
    component: PatientIndex
  }
];

const router = new Router({
  routes: routes
});

export default router;