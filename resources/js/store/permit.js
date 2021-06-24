export default {
  namespaced: true,

  state: {
    storedPermits: [],
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
        state.storedPermits = res;
        return res; // res is an array of objects, where each object contain permit data
      })
      .catch((err) => {
        console.log(err);
      });
    },

    deletePermit(state, payload) {
      console.log(payload);
      const res = fetch(`api/permits/${payload}`, {
        method: "DELETE",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      })
      .then((response) => {
        return response.json();
      })
      .then((res) => {
        // state.storedPermits = res;
        console.log(res);
        this.commit('permit/populatePermits');
        return res; // res is an array of objects, where each object contain permit data
      })
      .catch((err) => {
        console.log(err);
      });
    }
  }
}
