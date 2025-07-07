import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useStore = defineStore(
  "store",
  () => {
    const state = {
      awards: ref(null),
      biblio: ref(null),
      biblioTypes: ref(null),
      bio: ref(null),
      covers: ref(null),
      events: ref(null),
      finds: ref(null),
      findTypes: ref(null),
      freebie: ref(null),
      honors: ref(null),
      latest_news: ref(null),
      lessons_blessings: ref(null),
      life_poem: ref(null),
      online_resources: ref(null),
      pubTypes: ref(null),
      pubs: ref(null),
      resourceTypes: ref(null),
      resourceLinks: ref(null),
      reviews: ref(null),
      socials: ref(null),
    };
    const getters = {};
    const actions = {};

    return { ...state, ...getters, ...actions };
  }
  // {
  //   persist: {
  //     key: "lindaaddisonwriter.com",
  //   },
  // }
);
