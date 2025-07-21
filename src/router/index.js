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

// Placeholders for other pages
const Edukasi = { template: '<div><h1>Pojok Edukasi</h1></div>' };
const Forum = { template: '<div><h1>Forum Diskusi</h1></div>' };


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
  { path: '/edukasi', name: 'Edukasi', component: Edukasi },
  { path: '/forum', name: 'Forum', component: Forum },
  { path: '/profile', name: 'Profile', component: Profile },
  { path: '/ubah-kata-sandi', name: 'ChangePassword', component: ChangePassword },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;