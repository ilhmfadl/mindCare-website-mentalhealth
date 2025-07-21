import { createRouter, createWebHistory } from 'vue-router';

import TesDiri from '../views/tes_diri.vue';

// Placeholders for other pages
const Home = { template: '<div><h1>Home Page</h1></div>' };
const Edukasi = { template: '<div><h1>Pojok Edukasi</h1></div>' };
const Forum = { template: '<div><h1>Forum Diskusi</h1></div>' };
const Profile = { template: '<div><h1>Profile Page</h1></div>' };


const routes = [
  { path: '/', name: 'Home', component: Home },
  {
    path: '/tes-diri',
    name: 'TesDiri',
    component: TesDiri
  },
  { path: '/edukasi', name: 'Edukasi', component: Edukasi },
  { path: '/forum', name: 'Forum', component: Forum },
  { path: '/profile', name: 'Profile', component: Profile }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;