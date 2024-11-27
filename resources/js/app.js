import './bootstrap';

import { createApp } from 'vue';
import { createRouter, createWebHistory } from "vue-router";

import App from '../components/App.vue';
import i18n from "./i18n.js";
import LoginForm from "../components/LoginForm.vue";
import QuizForm from "../components/QuizForm.vue";

const routes = [
  { path: "/login", component: LoginForm },
  {
    path: "/quiz",
    component: QuizForm,
    name: "quiz",
    meta: { requiresAuth: true },
  },
  { path: "/", redirect: "/login" },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem("authToken");
    if (to.meta.requiresAuth && !isAuthenticated) {
      next({ name: "login" });
    } else {
      next();
    }
  });

createApp(App).use(router).use(i18n).mount("#app");