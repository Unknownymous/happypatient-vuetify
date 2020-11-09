import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Login from './auth/Login.vue';
import Register from './auth/Register.vue';
import PatientCreate from './views/patient/PatientCreate.vue';
import PatientIndex from './views/patient/PatientIndex.vue';
import PatientEdit from './views/patient/PatientEdit.vue';
import ServiceIndex from './views/service/ServiceIndex.vue';
import ProcedureIndex from './views/service_procedure/ProcedureIndex.vue';
import ProcedureCreate from './views/service_procedure/ProcedureCreate.vue';
import ActivityLog from './views/activity_log/ActivityLog.vue';
import TemplateContent from './views/template_content/TemplateContent.vue';
import UserCreate from './views/user/UserCreate.vue';
import UserIndex from './views/user/UserIndex.vue';
import UserEdit from './views/user/UserEdit.vue';
import PageNotFound from './404/PageNotFound.vue';

Vue.use(Router);


const routes = [
  {
    path: '/home',
    name: 'home',
    component: Home,
    children: [
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
        path: '/procedure/create',
        name: 'procedure.create',
        component: ProcedureCreate
      },
      {
        path: '/procedure/index',
        name: 'procedure.index',
        component: ProcedureIndex
      },
      {
        path: '/procedure/template/create/:procedureid',
        name: 'template.create',
        component: TemplateContent
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
      },
    ],
    beforeEnter(to, from, next)
    { 

      if(localStorage.getItem('access_token'))
      {
        next('/home');
        
      }
      else
      {
        next('/login');
      }
    }
  },
  {
    path: '/register',
    name: 'register',
    component: Register
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    beforeEnter(to, from, next)
    { 

      if(localStorage.getItem('access_token'))
      {
        next('/home');
      }
      else
      {
        next('/login');
      }
    }
  },
  {
    path: '*',
    component: PageNotFound,
    
  },

];

const router = new Router({
  routes: routes
});

export default router;