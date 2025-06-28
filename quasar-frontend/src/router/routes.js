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
      },
      {
        path: "publications",
        name: "Publications",
        component: () => import("pages/PublicationsPage.vue"),
        meta: {
          visible: true,
        },
      },
      {
        path: "latest-news",
        name: "News",
        component: () => import("pages/NewsPage.vue"),
        meta: {
          visible: true,
        },
      },
      {
        path: "events",
        name: "Events",
        component: () => import("pages/EventsPage.vue"),
        meta: {
          visible: true,
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
