import { createRouter, createWebHistory } from "vue-router";
import { getAccessToken } from "./services/tokenStore";
import { refreshToken } from "./services/auth";
import LoginView from "./views/LoginView.vue";
import HomeView from "./views/HomeView.vue";
import RegisterView from "./views/RegisterView.vue";

const routes = [
    { path: "/login", component: LoginView, meta: { guestOnly: true }, },
    { path: "/", component: HomeView, meta: { requiresAuth: true }, },
    { path: "/register", component: RegisterView, meta: { guestOnly: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    if (to.meta.guestOnly) {
        if (getAccessToken())
            return next("/");

        try {
            const res = await refreshToken();
            
            return next("/");
        } catch {
            return next();
        }
    } else {
        if (getAccessToken())
            return next();

        try {
            const res = await refreshToken();
            return next();
        } catch {
            return next("/login");
        }
    }
});

export default router;