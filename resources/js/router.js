import Vue from 'vue';
import Router from 'vue-router';
import PatientCreate from './views/patient/PatientCreate.vue';
import PatientIndex from './views/patient/PatientIndex.vue';
import PatientEdit from './views/patient/PatientEdit.vue';
import ServiceIndex from './views/service/ServiceIndex.vue';
import ActivityLog from './views/activity_log/ActivityLog.vue';
import UserCreate from './views/user/UserCreate.vue';
import UserIndex from './views/user/UserIndex.vue';
import UserEdit from './views/user/UserEdit.vue';

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
  },
  {
    path: '/patient/edit/:patientid',
    name: 'patient.edit',
    component: PatientEdit
  },
  {
    path: '/service/index',
    name: 'service.index',
    component: ServiceIndex
  },
  {
    path:'/activity_logs',
    name: 'activity_logs',
    component: ActivityLog
  },
  {
    path:'/user/create',
    name: 'user.create',
    component: UserCreate
  },
  {
    path:'/user/index',
    name: 'user.index',
    component: UserIndex
  },
  {
    path:'/user/edit/:userid',
    name: 'user.edit',
    component: UserEdit
  }
];

const router = new Router({
  routes: routes
});

export default router;