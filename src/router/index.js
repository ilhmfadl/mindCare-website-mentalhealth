import { createRouter, createWebHistory } from 'vue-router';

import TesDiri from '../views/tes_diri.vue';
import Home from '../views/home.vue';
import Profile from '../views/profile.vue';
import ChangePassword from '../views/change_password.vue';
import ResultIsDepresi from '../views/result/resultIsDepresi.vue';
import ResultIsAnxiety from '../views/result/resultIsAnxiety.vue';
import ResultIsSkizofrenia from '../views/result/resultIsSkizofrenia.vue';
import ResultIsBipolar from '../views/result/resultIsBipolar.vue';
import ResultIsGangguanMakan from '../views/result/resultIsGangguanMakan.vue';
import ResultIsOCD from '../views/result/resultIsOCD.vue';
import ResultIsPTSD from '../views/result/resultIsPTSD.vue';
import ResultIsGangguanKepribadian from '../views/result/resultIsgangguankepribadian.vue';
import PojokEdukasi from '../views/pojokEdukasi.vue';
import Depresi from '../views/edukasi/Depresi.vue';
import Anxiety from '../views/edukasi/anxiety.vue';
import Skizofrenia from '../views/edukasi/skizofrenia.vue';
import Bipolar from '../views/edukasi/bipolar.vue';
import Personality from '../views/edukasi/personality.vue';
import OCD from '../views/edukasi/ocd.vue';
import Eating from '../views/edukasi/eating.vue';
import PTSD from '../views/edukasi/ptsd.vue';
import Forum from '../views/forum.vue';

// Tambah import register dan login
import Register from '../views/register.vue';
import Login from '../views/login.vue';

// Import admin views
import AdminDashboard from '../adminViews/Dashboard.vue';
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
    path: '/hasil/depresi',
    name: 'ResultIsDepresi',
    component: ResultIsDepresi,
    props: true
  },
  {
    path: '/hasil/anxiety',
    name: 'ResultIsAnxiety',
    component: ResultIsAnxiety,
    props: true
  },
  {
    path: '/hasil/skizofrenia',
    name: 'ResultIsSkizofrenia',
    component: ResultIsSkizofrenia,
    props: true
  },
  {
    path: '/hasil/bipolar',
    name: 'ResultIsBipolar',
    component: ResultIsBipolar,
    props: true
  },
  {
    path: '/hasil/gangguan-makan',
    name: 'ResultIsGangguanMakan',
    component: ResultIsGangguanMakan,
    props: true
  },
  {
    path: '/hasil/ocd',
    name: 'ResultIsOCD',
    component: ResultIsOCD,
    props: true
  },
  {
    path: '/hasil/ptsd',
    name: 'ResultIsPTSD',
    component: ResultIsPTSD,
    props: true
  },
  {
    path: '/hasil/gangguan-kepribadian',
    name: 'ResultIsGangguanKepribadian',
    component: ResultIsGangguanKepribadian,
    props: true
  },
  { path: '/edukasi', name: 'Edukasi', component: PojokEdukasi },
  { path: '/edukasi/depresi', name: 'EdukasiDepresi', component: Depresi },
  { path: '/edukasi/anxiety', name: 'EdukasiAnxiety', component: Anxiety },
  { path: '/edukasi/skizofrenia', name: 'EdukasiSkizofrenia', component: Skizofrenia },
  { path: '/edukasi/bipolar', name: 'EdukasiBipolar', component: Bipolar },
  { path: '/edukasi/personality', name: 'EdukasiPersonality', component: Personality },
  { path: '/edukasi/ocd', name: 'EdukasiOCD', component: OCD },
  { path: '/edukasi/eating', name: 'EdukasiEating', component: Eating },
  { path: '/edukasi/ptsd', name: 'EdukasiPTSD', component: PTSD },
  { path: '/forum', name: 'Forum', component: Forum },
  { path: '/profile', name: 'Profile', component: Profile },
  { path: '/ubah-kata-sandi', name: 'ChangePassword', component: ChangePassword },
  // Tambah route register dan login
  { path: '/register', name: 'Register', component: Register },
  { path: '/login', name: 'Login', component: Login },
  { path: '/admin/dashboard', name: 'AdminDashboard', component: AdminDashboard },
  { path: '/admin/users', name: 'UsersManagement', component: UsersManagement },
  { path: '/admin/questioner', name: 'QuestionerManagement', component: QuestionerManagement },
  { path: '/admin/journal', name: 'JournalManagement', component: JournalManagement },
  { path: '/admin/forum', name: 'ForumManagement', component: ForumManagement },
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