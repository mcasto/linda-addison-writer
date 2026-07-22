import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";

const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    beforeEnter: async () => {
      const store = useStore();

      store.admin.brokenLinks = await callApi({
        path: "/broken-links",
        method: "get",
      });
      // await store.getData('admin.brokenLinks', "/broken-links");
    },
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
          store.covers = await callApi({ path: "/home", method: "get" });

          // await store.getData(store.covers, "/home");
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

          store.pubTypes = pubTypes;

          const defaultType = pubTypes[0].id;

          store.pubs = await callApi({
            path: `/publications/${defaultType}`,
            method: "get",
          });

          // await store.getData(store.pubs, `/publications/${defaultType}`);
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

          store.latest_news = await callApi({ path: "/news", method: "get" });

          // await store.getData(store.latest_news, "/news");
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

          // await store.getData(store.events.past, "/events/past?per_page=10");
          // await store.getData(
          //   store.events.future,
          //   "/events/future?per_page=10"
          // );
          // await store.getData(
          //   store.events.current,
          //   "/events/current?per_page=10"
          // );
        },
      },
      {
        path: "see-hear-read",
        name: "See / Hear / Read",
        component: () => import("src/pages/SeeHearReadPage.vue"),
        meta: {
          visible: true,
        },
        beforeEnter: async () => {
          const store = useStore();

          const findTypes = await callApi({
            path: "/finds",
            method: "get",
          });

          store.findTypes = findTypes;

          const defaultType = findTypes[0].id;

          store.finds = await callApi({
            path: `/finds/${defaultType}`,
            method: "get",
          });
          // await store.getData(store.finds, `/finds/${defaultType}`);
        },
      },
      {
        path: "biography",
        name: "Biography",
        component: () => import("pages/BioPage.vue"),
        meta: {
          visible: true,
        },
        beforeEnter: async () => {
          const store = useStore();

          store.bio = await callApi({ path: "/bio", method: "get" });

          // await store.getData(store.bio, "/bio");
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
        beforeEnter: async () => {
          const store = useStore();

          store.lessons_blessings = await callApi({
            path: "/lessons-and-blessings",
            method: "get",
          });

          // await store.getData(
          //   store.lessons_blessings,
          //   "/lessons-and-blessings"
          // );
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
        beforeEnter: async () => {
          const store = useStore();

          store.freebie = await callApi({ path: "/freebies", method: "get" });

          // await store.getData(store.freebie, "/freebies");
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
        beforeEnter: async () => {
          const store = useStore();

          const types = await callApi({
            path: "/online-resources",
            method: "get",
          });

          const defaultType = types[0].id;

          store.resourceTypes = types;

          store.resourceLinks = await callApi({
            path: `/online-resources/${defaultType}`,
            method: "get",
          });

          // await store.getData(
          //   store.resourceLinks,
          //   `/online-resources/${defaultType}`
          // );
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
        beforeEnter: async () => {
          const store = useStore();

          store.reviews = await callApi({
            path: "/reviews-quotes",
            method: "get",
          });
          // await store.getData(store.reviews, "/reviews-quotes");
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
        beforeEnter: async () => {
          const store = useStore();

          const types = await callApi({
            path: "/biblio",
            method: "get",
          });

          const defaultType = types[0].id;

          store.biblioTypes = types;

          store.biblio = await callApi({
            path: `/biblio/${defaultType}`,
            method: "get",
          });
          // await store.getData(store.biblio, `/biblio/${defaultType}`);
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
          store.awards = await callApi({ path: "/awards", method: "get" });
          // await store.getData(store.awards, "/awards");
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
        beforeEnter: async () => {
          const store = useStore();

          store.honors = await callApi({ path: "/honors", method: "get" });
          // await store.getData(store.honors, "/honors");
        },
      },
    ],
  },

  {
    path: "/admin",
    component: () => import("layouts/AdminLayout.vue"),
    name: "admin-section",
    meta: { navHide: true },
    children: [
      {
        path: "login",
        component: () => import("pages/admin/LoginPage.vue"),
        name: "admin-login",
        meta: { public: true, navHide: true },
      },
      {
        path: "",
        component: () => import("pages/admin/DashboardPage.vue"),
        name: "admin-dashboard",
        meta: { order: 1, icon: "mdi-view-dashboard", tip: "Dashboard" },
      },
      {
        path: "import-excel",
        component: () => import("src/pages/admin/ImportExcel.vue"),
        name: "admin-import-excel",
        meta: {
          order: 1.5,
          icon: "fa-solid fa-file-lines",
          tip: "Import Excel",
        },
      },
      {
        path: "import-biblio",
        component: () => import("src/pages/admin/ImportBiblio.vue"),
        name: "admin-import-biblio",
        meta: {
          order: 1.75,
          icon: "fa-solid fa-file-excel",
          tip: "Import Biblio Text",
        },
      },
      {
        path: "edit-biblio/:id",
        component: () => import("src/pages/admin/EditBiblio.vue"),
        beforeEnter: async () => {
          const store = useStore();
          const response = await callApi({
            path: "/admin/biblio",
            method: "get",
            useAuth: true,
          });

          store.admin.biblioTypes = response;
        },
        name: "admin-edit-biblio",
        meta: { navHide: true },
      },
      {
        path: "awards",
        component: () => import("pages/admin/AdminAwards.vue"),
        name: "admin-awards",
        meta: { order: 2, icon: "fa-solid fa-award", tip: "Awards" },
        beforeEnter: async () => {
          const store = useStore();
          store.admin.awards = await callApi({
            path: "/admin/awards",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "biblio",
        component: () => import("pages/admin/AdminBiblio.vue"),
        name: "admin-biblio",
        meta: { order: 3, icon: "mdi-bookshelf", tip: "Bibliography" },
        beforeEnter: async () => {
          const store = useStore();

          const types = await callApi({
            path: "/admin/biblio",
            method: "get",
            useAuth: true,
          });

          const defaultType = types[0].id;

          const biblios = await callApi({
            path: `/admin/biblio/${defaultType}`,
            method: "get",
            useAuth: true,
          });

          store.admin.biblioTypes = types;
          store.admin.biblio = biblios;
        },
      },
      {
        path: "contacts",
        component: () => import("pages/admin/AdminContacts.vue"),
        name: "admin-contacts",
        meta: { order: 4, icon: "fa-solid fa-address-book", tip: "Contacts" },
        beforeEnter: async () => {
          const store = useStore();

          store.admin.contacts = await callApi({
            path: "/admin/contacts",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "covers",
        component: () => import("pages/admin/AdminCovers.vue"),
        name: "admin-covers",
        meta: { order: 5, icon: "fa-solid fa-images", tip: "Covers" },
        beforeEnter: async () => {
          const store = useStore();

          store.admin.covers = await callApi({
            path: "/admin/covers",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "bio",
        component: () => import("pages/admin/AdminBio.vue"),
        beforeEnter: async () => {
          const store = useStore();
          store.admin.bio = await callApi({
            path: "/bio",
            method: "get",
          });
        },
        name: "admin-bio",
        meta: {
          order: 5.5,
          icon: "mdi-account-details",
          tip: "Bio",
        },
      },
      {
        path: "events",
        component: () => import("pages/admin/AdminEvents.vue"),
        name: "admin-events",
        meta: { order: 6, icon: "fa-solid fa-calendar", tip: "Events" },
        beforeEnter: async () => {
          const store = useStore();

          store.admin.events = await callApi({
            path: "/admin/events",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "finds",
        component: () => import("pages/admin/AdminFinds.vue"),
        name: "admin-finds",
        meta: {
          order: 7,
          icon: "mdi-multimedia",
          tip: "See / Hear / Read",
        },
        beforeEnter: async () => {
          const store = useStore();

          store.findTypes = await callApi({
            path: "/finds",
            method: "get",
          });

          store.admin.finds = await callApi({
            path: "/admin/finds",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "freebies",
        component: () => import("pages/admin/AdminFreebies.vue"),
        name: "admin-freebies",
        meta: {
          order: 8,
          icon: "mdi-gift-open-outline",
          tip: "Freebies",
        },
        beforeEnter: async () => {
          const store = useStore();

          store.admin.freebies = await callApi({
            path: "/admin/freebies",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "honors",
        component: () => import("pages/admin/AdminHonors.vue"),
        name: "admin-honors",
        meta: {
          order: 9,
          icon: "mdi-trophy",
          tip: "Honors",
        },
        beforeEnter: async () => {
          const store = useStore();

          store.admin.honors = await callApi({
            path: "/admin/honors",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "news",
        component: () => import("pages/admin/AdminNews.vue"),
        name: "admin-news",
        meta: {
          order: 10,
          icon: "fa-solid fa-newspaper",
          tip: "Latest News",
        },
        beforeEnter: async () => {
          const store = useStore();

          store.admin.latest_news = await callApi({
            path: "/admin/news",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "life-poems",
        component: () => import("pages/admin/AdminLifePoems.vue"),
        name: "admin-life-poems",
        meta: {
          order: 11,
          icon: "mdi-feather",
          tip: "Life Poems",
        },
        beforeEnter: async () => {
          const store = useStore();
          store.admin.life_poems = await callApi({
            path: "/admin/life-poems",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "online-resources",
        component: () => import("pages/admin/AdminOnlineResources.vue"),
        name: "admin-online-resources",
        meta: {
          order: 12,
          icon: "mdi-file-cabinet",
          tip: "Online Resources",
        },
        beforeEnter: async () => {
          const store = useStore();

          store.resourceTypes = await callApi({
            path: "/online-resources",
            method: "get",
          });

          store.admin.online_resources = await callApi({
            path: "/admin/online-resources",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "publications",
        component: () => import("pages/admin/AdminPublications.vue"),
        name: "admin-publications",
        meta: {
          order: 13,
          icon: "fa-brands fa-leanpub",
          tip: "Publications",
        },
        beforeEnter: async () => {
          const store = useStore();
          const response = await callApi({
            path: "/admin/publications",
            method: "get",
            useAuth: true,
          });

          store.admin.pubTypes = response.pubTypes;
          store.admin.publications = response.publications;
        },
      },
      {
        path: "manage-pub-types",
        component: () => import("pages/admin/AdminMangePubTypes.vue"),
        beforeEnter: async () => {
          const store = useStore();
          store.pubTypes = await callApi({
            path: "/publications",
            method: "get",
          });
        },
        name: "admin-manage-pub-types",
        meta: {
          order: 13.5,
          icon: "mdi-cogs",
          tip: "Manage Publication Types",
        },
      },
      {
        path: "reviews",
        component: () => import("pages/admin/AdminReviews.vue"),
        beforeEnter: async () => {
          const store = useStore();
          store.admin.reviews = await callApi({
            path: "/reviews-quotes",
            method: "get",
          });
        },
        name: "admin-reviews",
        meta: {
          order: 14,
          icon: "star",
          tip: "Reviews",
        },
      },
      {
        path: "socials",
        component: () => import("pages/admin/AdminSocials.vue"),
        beforeEnter: async () => {
          const store = useStore();
          store.admin.socials = await callApi({
            path: "/socials",
            method: "get",
          });
        },
        name: "admin-socials",
        meta: {
          order: 15,
          icon: "fa-solid fa-comment",
          tip: "Socials",
        },
      },

      {
        path: "broken-links",
        component: () => import("pages/admin/BrokenLinks.vue"),
        beforeEnter: async () => {
          const store = useStore();
          store.admin.brokenLinks = await callApi({
            path: "/broken-links",
            method: "get",
          });
        },
        name: "admin-broken-links",
        meta: {
          order: 16,
          icon: "mdi-link-off",
          tip: "Broken Links",
        },
      },
      {
        path: "users",
        component: () => import("pages/admin/AdminUsers.vue"),
        name: "admin-users",
        meta: {
          order: 17,
          icon: "mdi-account-group-outline",
          tip: "Users",
          requirePermissions: true,
        },
        beforeEnter: async () => {
          const store = useStore();
          if (!store.user.permissions == 1) {
            return;
          }

          store.admin.users = await callApi({
            path: "/admin/users",
            method: "get",
            useAuth: true,
          });
        },
      },
      {
        path: "profile",
        component: () => import("pages/admin/AdminProfile.vue"),
        name: "admin-profile",
        meta: {
          order: 1.25,
          icon: "mdi-account-cog-outline",
          tip: "User Account",
        },
        beforeEnter: async () => {
          const store = useStore();

          store.admin.profile = await callApi({
            path: "/admin/user",
            method: "get",
            useAuth: true,
          });
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
