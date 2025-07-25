import { createRouter, createWebHistory } from 'vue-router';

import TesDiri from '../views/tes_diri.vue';
import Home from '../views/home.vue';
import Profile from '../views/profile.vue';
import ChangePassword from '../views/change_password.vue';
import ResultIsNeurosis from '../views/result/resultIsNeurosis.vue';
import ResultIsPsychotic from '../views/result/resultIsPsychotic.vue';
import ResultIsPTSD from '../views/result/resultIsPTSD.vue';
import ResultIsNormal from '../views/result/resultIsNormal.vue';
import PojokEdukasi from '../views/pojokEdukasi.vue';
import Depresi from '../views/edukasi/neurosis.vue';
import Anxiety from '../views/edukasi/psychotic.vue';
import PTSD from '../views/edukasi/ptsd.vue';
import Forum from '../views/forum.vue';

// Tambah import register dan login
import Register from '../views/register.vue';
import Login from '../views/login.vue';

// Import admin views
import UsersManagement from '../adminViews/UsersManagement.vue';
import QuestionerManagement from '../adminViews/QuestionerManagement.vue';
import JournalManagement from '../adminViews/JournalManagement.vue';
import ForumManagement from '../adminViews/ForumManagement.vue';
// import QuestionerAdd from '../adminViews/QuestionerAdd.vue';
// import QuestionerDetail from '../adminViews/QuestionerDetail.vue';

// Placeholders for other pages
const Edukasi = { template: '<div><h1>Pojok Edukasi</h1></div>' };


const routes = [
  { path: '/', name: 'Home', component: Home },
  {
    path: '/tes-diri',
    name: 'TesDiri',
    component: TesDiri
  },
  {
    path: '/hasil/neurosis',
    name: 'ResultIsNeurosis',
    component: ResultIsNeurosis,
    props: true
  },
  {
    path: '/hasil/psikotik',
    name: 'ResultIsPsychotic',
    component: ResultIsPsychotic,
    props: route => ({
      severityProp: route.params.severity
    })
  },
  {
    path: '/hasil/ptsd',
    name: 'ResultIsPTSD',
    component: ResultIsPTSD,
    props: route => ({
      hasilProp: route.params.hasil,
      severityProp: route.params.severity
    })
  },
  {
    path: '/hasil/normal',
    name: 'ResultIsNormal',
    component: ResultIsNormal
  },
  { path: '/edukasi', name: 'Edukasi', component: PojokEdukasi },
  { path: '/edukasi/depresi', name: 'EdukasiDepresi', component: Depresi },
  { path: '/edukasi/anxiety', name: 'EdukasiAnxiety', component: Anxiety },
  { path: '/edukasi/ptsd', name: 'EdukasiPTSD', component: PTSD },
  { path: '/edukasi/neurosis', name: 'EdukasiNeurosis', component: Depresi },
  { path: '/edukasi/psikotik', name: 'EdukasiPsikotik', component: Anxiety },
  { path: '/forum', name: 'Forum', component: Forum },
  { path: '/profile', name: 'Profile', component: Profile },
  { path: '/ubah-kata-sandi', name: 'ChangePassword', component: ChangePassword },
  // Tambah route register dan login
  { path: '/register', name: 'Register', component: Register },
  { path: '/login', name: 'Login', component: Login },
  { path: '/admin/users', name: 'UsersManagement', component: UsersManagement },
  { path: '/admin/questioner', name: 'QuestionerManagement', component: QuestionerManagement },
  { path: '/admin/journal', name: 'JournalManagement', component: JournalManagement },
  { path: '/admin/forum', name: 'ForumManagement', component: ForumManagement },
  { path: '/admin/forum-user', name: 'AdminForumUser', component: Forum },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
      if (savedPosition) {
        return savedPosition;
      } else {
        return { top: 0 };
      }
    }
});

export default router;