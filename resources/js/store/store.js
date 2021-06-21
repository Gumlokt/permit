import { createStore } from "vuex";

export const store = createStore({
  state() {
    return {
      permits: [],
      latestPermitNumber: null,
    };
  },

  getters: {
    sequencePermitNumber: state => {
      return state.latestPermitNumber + 1;
    }
  },

  mutations: {
    populatePermits(state, payload) {
      const res = fetch(`api/permits`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      })
        .then((response) => {
          return response.json();
        })
        .then((res) => {
          state.permits = res;
          state.latestPermitNumber = parseInt(state.permits[0].number);

          // console.log(state.latestPermitNumber + 1);
          return res; // res is an array of objects, where each object contain permit data
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
});
