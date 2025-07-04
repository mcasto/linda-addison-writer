import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useStore = defineStore(
  "store",
  () => {
    // examples
    // const state = {
    //   cuePointer: ref(null),
    // };
    // const getters = {
    //   activeShow: computed(activeShow),
    // };
    // const actions = {
    //   addShow,
    // };

    const state = {
      awards: ref(null),
      biblio: ref(null),
      bio: ref(null),
      covers: ref(null),
      events: ref(null),
      finds: ref(null),
      freebie: ref(null),
      honors: ref(null),
      latest_news: ref(null),
      lessons_blessings: ref(null),
      life_poem: ref(null),
      online_resources: ref(null),
      publications: ref(null),
      pubTypes: ref(null),
      pubs: ref(null),
      reviews: ref(null),
      socials: ref(null),
    };
    const getters = {};
    const actions = {};

    return { ...state, ...getters, ...actions };
  },
  {
    persist: {
      key: "lindaaddisonwriter.com",
    },
  }
);
