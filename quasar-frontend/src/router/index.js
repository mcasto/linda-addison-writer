import { defineRouter } from "#q-app/wrappers";
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from "vue-router";
import routes from "./routes";
import { useStore } from "src/stores/store";
import callApi from "src/assets/call-api";

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === "history"
    ? createWebHistory
    : createWebHashHistory;

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE),
  });

  Router.beforeEach(async (to) => {
    const store = useStore();

    const isAdmin = to.matched.find(({ name }) => name == "admin-section");
    const isPublic = to.meta.public;

    if (isAdmin && !isPublic) {
      if (!store.token) {
        return { name: "admin-login" };
      }

      const valid = await callApi({
        path: "/admin/validate-token",
        method: "post",
        useAuth: true,
      }).catch(() => {
        return false;
      });

      if (!valid) {
        return { name: "admin-login" };
      }
    }

    store.design = await callApi({ path: "/design-credits", method: "get" });
    store.life_poem = await callApi({ path: "/life-poem", method: "get" });
    store.socials = await callApi({ path: "/socials", method: "get" });
  });

  return Router;
});
