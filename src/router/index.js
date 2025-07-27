import { createRouter, createWebHistory } from 'vue-router';
import { requireAdminAuth, requireGuest } from '../middleware/adminAuth.js';

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
import AdminChatDashboard from '../adminViews/AdminChatDashboard.vue';
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
  { path: '/register', name: 'Register', component: Register, beforeEnter: requireGuest },
  { path: '/login', name: 'Login', component: Login, beforeEnter: requireGuest },
  { path: '/admin/users', name: 'UsersManagement', component: UsersManagement, beforeEnter: requireAdminAuth, meta: { requiresAdmin: true } },
  { path: '/admin/questioner', name: 'QuestionerManagement', component: QuestionerManagement, beforeEnter: requireAdminAuth, meta: { requiresAdmin: true } },
  { path: '/admin/journal', name: 'JournalManagement', component: JournalManagement, beforeEnter: requireAdminAuth, meta: { requiresAdmin: true } },
  { path: '/admin/chat', name: 'AdminChatDashboard', component: AdminChatDashboard, beforeEnter: requireAdminAuth, meta: { requiresAdmin: true } },
  { path: '/admin/forum-user', name: 'AdminForumUser', component: Forum, beforeEnter: requireAdminAuth, meta: { requiresAdmin: true } },
  
  // Catch-all route untuk admin paths yang tidak valid
  { 
    path: '/admin/:pathMatch(.*)*', 
    redirect: '/login',
    beforeEnter: requireAdminAuth 
  },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
      if (savedPosition) {
        return savedPosition;
      } else if (to.hash) {
        // Handle anchor scrolling
        return {
          el: to.hash,
          behavior: 'smooth'
        };
      } else {
        return { top: 0 };
      }
    }
});

// Global navigation guard untuk keamanan tambahan
router.beforeEach((to, from, next) => {
  // Cek apakah route memerlukan autentikasi admin
  if (to.matched.some(record => record.meta.requiresAdmin)) {
    const user = JSON.parse(localStorage.getItem('user') || 'null');
    if (!user || user.role !== 'admin') {
      next('/login');
      return;
    }
  }
  
  next();
});

export default router;