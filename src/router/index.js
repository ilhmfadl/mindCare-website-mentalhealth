import { createRouter, createWebHistory } from 'vue-router';

import TesDiri from '../views/tes_diri.vue';
import Home from '../views/home.vue';
import Profile from '../views/profile.vue';
import ChangePassword from '../views/change_password.vue';

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