import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";

const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      {
        path: "",
        name: "Home",
        component: () => import("pages/IndexPage.vue"),
        meta: {
          visible: true,
        },
        beforeEnter: async () => {
          const store = useStore();
          const response = await callApi({ path: "/home", method: "get" });
          store.covers = response;
        },
      },
      {
        path: "publications",
        name: "Publications",
        component: () => import("pages/PublicationsPage.vue"),
        meta: {
          visible: true,
        },
        beforeEnter: async () => {
          const store = useStore();
          const pubTypes = await callApi({
            path: "/publications",
            method: "get",
          });

          const defaultType = pubTypes[0].id;

          const pubs = await callApi({
            path: `/publications/${defaultType}`,
            method: "get",
          });

          store.pubTypes = pubTypes;
          store.pubs = pubs;
        },
      },
      {
        path: "latest-news",
        name: "News",
        component: () => import("pages/NewsPage.vue"),
        meta: {
          visible: true,
        },
        beforeEnter: async () => {
          const store = useStore();
          store.news = await callApi({
            path: "/news",
            method: "get",
          });
        },
      },
      {
        path: "events",
        name: "Events",
        component: () => import("pages/EventsPage.vue"),
        meta: {
          visible: true,
        },
        beforeEnter: async () => {
          const store = useStore();

          const past = await callApi({
            path: "/events/past?per_page=10",
            method: "get",
          });

          const future = await callApi({
            path: "/events/future?per_page=10",
            method: "get",
          });

          const current = await callApi({
            path: "/events/current?per_page=10",
            method: "get",
          });

          store.events = {
            past: past.data.length > 0 ? past : null,
            future: future.data.length > 0 ? future : null,
            current: current.data.length > 0 ? current : null,
          };
        },
      },
      {
        path: "see-hear-read",
        name: "See / Hear / Read",
        component: () => import("src/pages/SeeHearReadPage.vue"),
        meta: {
          visible: true,
        },
      },
      {
        path: "biography",
        name: "Biography",
        component: () => import("pages/BioPage.vue"),
        meta: {
          visible: true,
        },
      },
      {
        path: "contact",
        name: "Contact",
        component: () => import("pages/ContactPage.vue"),
        meta: {
          visible: true,
        },
      },
      {
        path: "lessons-and-blessings",
        name: "Lessons & Blessings",
        component: () => import("pages/LessonsBlessingsPage.vue"),
        meta: {
          visible: true,
          more: true,
        },
      },
      {
        path: "freebies",
        name: "Today's Freebie",
        component: () => import("pages/FreebiePage.vue"),
        meta: {
          visible: true,
          more: true,
        },
      },
      {
        path: "online-resources",
        name: "Online Resources",
        component: () => import("pages/OnlineResourcesPage.vue"),
        meta: {
          visible: true,
          more: true,
        },
      },
      {
        path: "reviews-and-quotes",
        name: "Reviews & Quotes",
        component: () => import("pages/ReviewsPage.vue"),
        meta: {
          visible: true,
          more: true,
        },
      },
      {
        path: "biblio",
        name: "Bibliography",
        component: () => import("pages/BiblioPage.vue"),
        meta: {
          visible: true,
          more: true,
        },
      },
      {
        path: "awards",
        name: "Awards",
        component: () => import("pages/AwardsPage.vue"),
        meta: {
          visible: true,
          more: true,
        },
        beforeEnter: async () => {
          const store = useStore();
          const response = await callApi({ path: "/awards", method: "get" });
          console.log({ awards: response });
          store.awards = response;
        },
      },
      {
        path: "honors",
        name: "Honors",
        component: () => import("pages/HonorsPage.vue"),
        meta: {
          visible: true,
          more: true,
        },
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: "/:catchAll(.*)*",
    component: () => import("pages/ErrorNotFound.vue"),
  },
];

export default routes;
