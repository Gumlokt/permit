import { createStore } from "vuex";

export const store = createStore({
  state() {
    return {
      permits: [],

      permit: {
        number: null,
        surname: null,
        forename: null,
        patronymic: null,
        company: null,
        position: null,
        dateStart: null,
        dateEnd: null,
      },
    };
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
          // console.log(res);
          return res; // res is an array of objects, where each object contain permit data
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
});
